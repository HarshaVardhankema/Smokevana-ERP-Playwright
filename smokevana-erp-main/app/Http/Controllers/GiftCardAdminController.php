<?php

namespace App\Http\Controllers;

use App\Contact;
use App\GiftCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class GiftCardAdminController extends Controller
{
    /**
     * Display gift cards list (page or DataTables JSON).
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $giftCards = GiftCard::with('purchaser')
                ->whereHas('purchaser', function ($q) use ($business_id) {
                    $q->where('contacts.business_id', $business_id);
                })
                ->select('gift_cards.*');

            return DataTables::of($giftCards)
                ->editColumn('image', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset('uploads/img/' . $row->image) . '" alt="Gift Card" style="max-width: 60px; max-height: 60px; border-radius: 4px;">';
                    }
                    return '<span class="text-muted">—</span>';
                })
                ->editColumn('initial_amount', function ($row) {
                    return number_format($row->initial_amount, 2) . ' ' . $row->currency;
                })
                ->editColumn('balance', function ($row) {
                    return number_format($row->balance, 2) . ' ' . $row->currency;
                })
                ->editColumn('type', function ($row) {
                    return ucfirst($row->type ?? '—');
                })
                ->editColumn('status', function ($row) {
                    $badges = [
                        'pending_payment' => 'warning',
                        'active' => 'success',
                        'redeemed' => 'info',
                        'expired' => 'danger',
                        'cancelled' => 'default',
                    ];
                    $badge = $badges[$row->status] ?? 'default';
                    return '<span class="label label-' . $badge . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $badges = [
                        'pending_payment' => 'warning',
                        'active' => 'success',
                        'redeemed' => 'info',
                        'expired' => 'danger',
                        'cancelled' => 'default',
                    ];
                    $badge = $badges[$row->status] ?? 'default';
                    return '<span class="label label-' . $badge . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->addColumn('purchaser_name', function ($row) {
                    return $row->purchaser ? ($row->purchaser->name ?? $row->purchaser->supplier_business_name ?? $row->purchaser->contact_id ?? '—') : '—';
                })
                ->editColumn('purchased_at', function ($row) {
                    return $row->purchased_at ? $row->purchased_at->format('Y-m-d H:i') : '—';
                })
                ->editColumn('expires_at', function ($row) {
                    return $row->expires_at ? $row->expires_at->format('Y-m-d') : '—';
                })
                ->addColumn('action', function ($row) {
                    $action = '<a href="' . action([self::class, 'show'], [$row->id]) . '" class="btn btn-xs btn-info view-gift-card" data-href="' . action([self::class, 'show'], [$row->id]) . '"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a> ';
                    $action .= '<a href="' . action([self::class, 'edit'], [$row->id]) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> ' . __("messages.edit") . '</a>';
                    return $action;
                })
                ->rawColumns(['image', 'status', 'status_badge', 'action'])
                ->make(true);
        }

        return view('gift_cards.index');
    }

    /**
     * Show the form for creating a new gift card.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $customers = Contact::where('business_id', $business_id)
            ->whereIn('type', ['customer', 'both'])
            ->get()
            ->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => ($contact->name ?? '') . ($contact->supplier_business_name ? ' - ' . $contact->supplier_business_name : '') ?: $contact->contact_id,
                ];
            })
            ->pluck('name', 'id');

        return view('gift_cards.create', compact('customers'));
    }

    /**
     * Store a newly created gift card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|size:3',
            'purchaser_contact_id' => 'required|exists:contacts,id',
            'type' => 'nullable|in:egift,physical,printable',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_email' => 'nullable|email|max:255',
            'status' => 'nullable|in:pending_payment,active,redeemed,expired,cancelled',
            'expires_at' => 'nullable|date',
            'message' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $code = $this->generateUniqueCode();
            $amount = (float) $request->amount;
            $currency = strtoupper($request->currency ?? 'USD');

            $data = [
                'code' => $code,
                'initial_amount' => $amount,
                'balance' => $amount,
                'currency' => $currency,
                'purchaser_contact_id' => $request->purchaser_contact_id,
                'type' => $request->type ?? 'egift',
                'recipient_name' => $request->recipient_name,
                'recipient_email' => $request->recipient_email,
                'message' => $request->message,
                'status' => $request->status ?? 'active',
                'purchased_at' => now(),
                'expires_at' => $request->expires_at ?: null,
                'created_by_user_id' => auth()->id(),
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'giftcard_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/img'), $filename);
                $data['image'] = $filename;
            }

            GiftCard::create($data);

            $output = ['success' => 1, 'msg' => __('Gift card created successfully')];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            $output = ['success' => 0, 'msg' => __('messages.something_went_wrong')];
        }

        return redirect()->action([self::class, 'index'])->with('status', $output);
    }

    /**
     * Display the specified gift card (for modal).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $giftCard = GiftCard::with('purchaser')->findOrFail($id);
        return view('gift_cards.show', compact('giftCard'));
    }

    /**
     * Show the form for editing the specified gift card.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $giftCard = GiftCard::with('purchaser')->findOrFail($id);
        $business_id = request()->session()->get('user.business_id');
        $customers = Contact::where('business_id', $business_id)
            ->whereIn('type', ['customer', 'both'])
            ->get()
            ->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => ($contact->name ?? '') . ($contact->supplier_business_name ? ' - ' . $contact->supplier_business_name : '') ?: $contact->contact_id,
                ];
            })
            ->pluck('name', 'id');
        $selectedCustomer = $giftCard->purchaser_contact_id;

        return view('gift_cards.edit', compact('giftCard', 'customers', 'selectedCustomer'));
    }

    /**
     * Update the specified gift card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $giftCard = GiftCard::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|size:3',
            'purchaser_contact_id' => 'required|exists:contacts,id',
            'type' => 'nullable|in:egift,physical,printable',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_email' => 'nullable|email|max:255',
            'status' => 'nullable|in:pending_payment,active,redeemed,expired,cancelled',
            'expires_at' => 'nullable|date',
            'message' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable',
        ]);

        try {
            $amount = (float) $request->amount;
            $giftCard->currency = strtoupper($request->currency ?? 'USD');
            $giftCard->initial_amount = $amount;
            $giftCard->balance = $amount;
            $giftCard->purchaser_contact_id = $request->purchaser_contact_id;
            $giftCard->type = $request->type ?? 'egift';
            $giftCard->recipient_name = $request->recipient_name;
            $giftCard->recipient_email = $request->recipient_email;
            $giftCard->message = $request->message;
            $giftCard->status = $request->status ?? $giftCard->status;
            $giftCard->expires_at = $request->expires_at ?: null;

            if ($request->remove_image && $giftCard->image) {
                if (file_exists(public_path('uploads/img/' . $giftCard->image))) {
                    @unlink(public_path('uploads/img/' . $giftCard->image));
                }
                $giftCard->image = null;
            }
            if ($request->hasFile('image')) {
                if ($giftCard->image && file_exists(public_path('uploads/img/' . $giftCard->image))) {
                    @unlink(public_path('uploads/img/' . $giftCard->image));
                }
                $file = $request->file('image');
                $filename = 'giftcard_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/img'), $filename);
                $giftCard->image = $filename;
            }

            $giftCard->save();

            $output = ['success' => 1, 'msg' => __('Gift card updated successfully')];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            $output = ['success' => 0, 'msg' => __('messages.something_went_wrong')];
        }

        return redirect()->action([self::class, 'index'])->with('status', $output);
    }

    /**
     * Cancel the specified gift card (POST, returns JSON).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel($id)
    {
        try {
            $giftCard = GiftCard::findOrFail($id);
            if ($giftCard->status !== 'active') {
                return response()->json(['success' => false, 'msg' => __('Gift card is not active and cannot be cancelled.')]);
            }
            $giftCard->status = 'cancelled';
            $giftCard->save();
            return response()->json(['success' => true, 'msg' => __('Gift card cancelled successfully.')]);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    /**
     * Generate a unique gift card code.
     *
     * @param  int  $length
     * @return string
     */
    private function generateUniqueCode(int $length = 16): string
    {
        do {
            $raw = strtoupper(\Illuminate\Support\Str::random($length));
            $code = implode('-', str_split($raw, 4));
        } while (GiftCard::where('code', $code)->exists());

        return $code;
    }
}
