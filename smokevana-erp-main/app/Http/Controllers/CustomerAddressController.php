<?php

namespace App\Http\Controllers;

use App\Contact;
use App\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;

class CustomerAddressController extends Controller
{
    /**
     * Display a listing of addresses for a contact (DataTables)
     *
     * @return mixed
     */
    public function index()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $contact_id = request()->get('contact_id');
        
        if (!$contact_id) {
            return response()->json(['error' => 'Contact ID is required'], 400);
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            
            // Check if company column exists before selecting it
            $columns = [
                'multiple_address_customer.id',
                'multiple_address_customer.contact_id',
                'multiple_address_customer.first_name',
                'multiple_address_customer.last_name',
                'multiple_address_customer.address_label',
                'multiple_address_customer.address_type',
                'multiple_address_customer.address_line_1',
                'multiple_address_customer.address_line_2',
                'multiple_address_customer.city',
                'multiple_address_customer.state',
                'multiple_address_customer.zip_code',
                'multiple_address_customer.country',
                'multiple_address_customer.created_at'
            ];
            
            // Add company column only if it exists in the table
            try {
                $hasCompanyColumn = Schema::hasColumn('multiple_address_customer', 'company');
                if ($hasCompanyColumn) {
                    $columns[] = 'multiple_address_customer.company';
                }
            } catch (\Exception $e) {
                // If schema check fails, just continue without company column
            }
            
            $addresses = CustomerAddress::join('contacts', 'multiple_address_customer.contact_id', '=', 'contacts.id')
                ->where('contacts.business_id', $business_id)
                ->where('multiple_address_customer.contact_id', $contact_id)
                ->select($columns);

            return DataTables::of($addresses)
                ->addColumn('name', function ($row) {
                    $name_parts = array_filter([$row->first_name, $row->last_name]);
                    return !empty($name_parts) ? implode(' ', $name_parts) : '-';
                })
                ->editColumn('address_label', function ($row) {
                    return $row->address_label ?? '-';
                })
                ->editColumn('address_type', function ($row) {
                    return $row->address_type ? ucfirst($row->address_type) : '-';
                })
                ->addColumn('full_address', function ($row) {
                    $address_parts = array_filter([
                        $row->address_line_1,
                        $row->address_line_2,
                        $row->city,
                        $row->state,
                        $row->zip_code,
                        $row->country
                    ]);
                    return implode(', ', $address_parts);
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info btn-xs btn-modal edit-address-btn" 
                            data-href="' . action([\App\Http\Controllers\CustomerAddressController::class, 'edit'], [$row->id]) . '">
                            <i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '
                        </button>
                        <button type="button" class="btn btn-danger btn-xs delete-address-btn" 
                            data-href="' . action([\App\Http\Controllers\CustomerAddressController::class, 'destroy'], [$row->id]) . '">
                            <i class="glyphicon glyphicon-trash"></i> ' . __('messages.delete') . '
                        </button>
                    </div>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    /**
     * Store a newly created address
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $business_id = $request->session()->get('user.business_id');

            $request->validate([
                'contact_id' => 'required|integer',
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'address_label' => 'nullable|string|max:255',
                'address_type' => 'nullable|string|in:billing,shipping',
                'address_line_1' => 'required|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:255',
            ]);

            // Verify contact belongs to business
            $contact = Contact::where('business_id', $business_id)
                ->where('id', $request->input('contact_id'))
                ->firstOrFail();

            $address = new CustomerAddress();
            $address->contact_id = $request->input('contact_id');
            $address->first_name = $request->input('first_name');
            $address->last_name = $request->input('last_name');
            $address->company = $request->input('company');
            $address->address_label = $request->input('address_label');
            $address->address_type = $request->input('address_type');
            $address->address_line_1 = $request->input('address_line_1');
            $address->address_line_2 = $request->input('address_line_2');
            $address->city = $request->input('city');
            $address->state = $request->input('state');
            $address->zip_code = $request->input('zip_code');
            $address->country = $request->input('country');
            $address->save();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.added_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return response()->json($output);
    }

    /**
     * Show the form for editing the specified address
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $address = CustomerAddress::join('contacts', 'multiple_address_customer.contact_id', '=', 'contacts.id')
            ->where('contacts.business_id', $business_id)
            ->where('multiple_address_customer.id', $id)
            ->select('multiple_address_customer.*')
            ->firstOrFail();

        return view('contact.partials.edit_address_modal')
            ->with(compact('address'));
    }

    /**
     * Update the specified address
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $business_id = $request->session()->get('user.business_id');

            $request->validate([
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'address_label' => 'nullable|string|max:255',
                'address_type' => 'nullable|string|in:billing,shipping',
                'address_line_1' => 'required|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:255',
            ]);

            $address = CustomerAddress::join('contacts', 'multiple_address_customer.contact_id', '=', 'contacts.id')
                ->where('contacts.business_id', $business_id)
                ->where('multiple_address_customer.id', $id)
                ->select('multiple_address_customer.*')
                ->firstOrFail();

            $address->first_name = $request->input('first_name');
            $address->last_name = $request->input('last_name');
            $address->company = $request->input('company');
            $address->address_label = $request->input('address_label');
            $address->address_type = $request->input('address_type');
            $address->address_line_1 = $request->input('address_line_1');
            $address->address_line_2 = $request->input('address_line_2');
            $address->city = $request->input('city');
            $address->state = $request->input('state');
            $address->zip_code = $request->input('zip_code');
            $address->country = $request->input('country');
            $address->save();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.updated_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return response()->json($output);
    }

    /**
     * Remove the specified address
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            $address = CustomerAddress::join('contacts', 'multiple_address_customer.contact_id', '=', 'contacts.id')
                ->where('contacts.business_id', $business_id)
                ->where('multiple_address_customer.id', $id)
                ->select('multiple_address_customer.*')
                ->firstOrFail();

            $address->delete();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.deleted_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return response()->json($output);
    }
}

