<?php

namespace App\Http\Controllers;

use App\Contact;
use App\CustomerAddress;
use App\Events\TransactionPaymentAdded;
use App\Events\TransactionPaymentUpdated;
use App\Exceptions\AdvanceBalanceNotAvailable;
use App\Models\TransactionPaymentGroup;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransactionPaymentController extends Controller
{
    protected $transactionUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  TransactionUtil  $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $is_ecom = false, $is_util=false)
    {
        // pay by choice
        if ($request->input('contact_type_radio') == 'pay_by_choice' && !empty($request->input('transactions'))) {
            $transactions = $request->input('transactions');
            $transaction_amounts = array_filter($transactions, function ($amount) {
                return floatval($amount) > 0;
            });
            $transaction_data = [];
            foreach ($transaction_amounts as $id => $amount) {
                $transaction_data[] = [
                    'id' => $id,
                    'amount' => floatval($amount)
                ];
            }

            $output = ['success' => true, 'msg' => __('purchase.payment_added_success')];
            $paying= 0;

            $transaction_payment_groups = [];
            if ($is_ecom) {
                $business_id = $request->input('business_id');
            } else {
                $business_id = $request->session()->get('user.business_id');
            }

            $group_ref_no = $this->transactionUtil->setAndGetReferenceCount('transaction_payment_groups_count', $business_id);
            foreach ($transaction_data as $data) {
                $transaction_id = (int) $data['id'];
                $amount = (float) $data['amount'];
                if ($amount < 0) {
                    continue;
                }
                try {
                    $transaction = Transaction::where('business_id', $business_id)
                        ->where('id', $transaction_id)
                        ->where('status', '!=', 'void')
                        ->with(['contact'])
                        ->firstOrFail();

                    $transaction_before = $transaction->replicate();
                    if (!$is_ecom) {
                        if (! (auth()->user()->can('purchase.payments') || auth()->user()->can('hms.add_booking_payment') || auth()->user()->can('sell.payments') || auth()->user()->can('all_expense.access') || auth()->user()->can('view_own_expense') || auth()->user()->can('user.update'))) {
                            abort(403, 'Unauthorized action.');
                        }
                    }

                    if ($transaction->payment_status != 'paid') {
                        $inputs = $request->only([
                            'method',
                            'note',
                            'card_number',
                            'card_holder_name',
                            'card_transaction_number',
                            'card_type',
                            'card_month',
                            'card_year',
                            'card_security',
                            'cheque_number',
                            'bank_account_number'
                        ]);

                        if ($is_ecom) {
                            $inputs['paid_on'] = $request->input('paid_on');
                        } else {
                            $inputs['paid_on'] = now()->toDateTimeString();
                        }
                        $inputs['transaction_id'] = $transaction->id;
                        $inputs['amount'] = $this->transactionUtil->num_uf($amount);
                        if ($is_ecom) {
                            $inputs['created_by'] = $request->input('created_by');
                        } else {
                            $inputs['created_by'] = auth()->user()->id;
                        }
                        $inputs['payment_for'] = $transaction->contact_id;

                        if ($inputs['method'] == 'custom_pay_1') {
                            $inputs['transaction_no'] = $request->input('transaction_no_1');
                        } elseif ($inputs['method'] == 'custom_pay_2') {
                            $inputs['transaction_no'] = $request->input('transaction_no_2');
                        } elseif ($inputs['method'] == 'custom_pay_3') {
                            $inputs['transaction_no'] = $request->input('transaction_no_3');
                        }

                        if (! empty($request->input('account_id')) && $inputs['method'] != 'advance') {
                            $inputs['account_id'] = $request->input('account_id');
                        }
                        if ($is_ecom) {
                            $inputs['account_id'] = $request->input('contact_id');
                        }

                        $prefix_type = 'purchase_payment';
                        if (in_array($transaction->type, ['sell', 'sell_return'])) {
                            $prefix_type = 'sell_payment';
                        } elseif (in_array($transaction->type, ['expense', 'expense_refund'])) {
                            $prefix_type = 'expense_payment';
                        }

                        DB::beginTransaction();

                        $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type, $business_id);
                        //Generate reference number
                        $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count, $business_id, null,$is_ecom);

                        $inputs['business_id'] = $is_ecom ? $request->input('business_id') :  $request->session()->get('business.id');
                        $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
                        //Pay from advance balance
                        $payment_amount = $inputs['amount'];
                        $contact_balance = ! empty($transaction->contact) ? $transaction->contact->balance : 0;
                        if ($inputs['method'] == 'advance' && $inputs['amount'] > $contact_balance) {
                            throw new AdvanceBalanceNotAvailable(__('lang_v1.required_advance_balance_not_available'));
                        }

                        if (! empty($inputs['amount'])) {
                            $tp = TransactionPayment::create($inputs);

                            // erp custom store each invoice payment in transaction_payment_groups table
                            $individual_transaction = Transaction::where('business_id', $business_id)
                                    ->where('id', $transaction_id)
                                    ->first();
                                
                            if ($individual_transaction) {
                                $transaction_payment_groups[] = [
                                    'business_id' => $business_id,
                                        'transaction_id' => $transaction_id,
                                        'amount' => $amount,
                                        'payment_method_id' => $tp->id,
                                        'group_name' => $individual_transaction->type,
                                        'group_ref_no' => $group_ref_no,
                                        'contact_id' => $transaction->contact_id,
                                    ];
                            }


                            if (! empty($request->input('denominations'))) {
                                $this->transactionUtil->addCashDenominations($tp, $request->input('denominations'));
                            }

                            $inputs['transaction_type'] = $transaction->type;
                            event(new TransactionPaymentAdded($tp, $inputs));
                        }

                        //update payment status
                        $payment_status = $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                        $transaction->payment_status = $payment_status;

                        $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);

                        $paying +=$amount;
                        DB::commit();
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    $msg = __('messages.something_went_wrong');

                    if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) {
                        $msg = $e->getMessage();
                    } else {
                        \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
                    }

                    $output = ['success' => false, 'msg' => $msg];
                }
            }
            if (!empty($transaction_payment_groups)) {
                TransactionPaymentGroup::insert($transaction_payment_groups);
            }
            
            try {
                $total = (float) $request->input('amount');
                $remaining = $total - $paying;
                if ($remaining > 0) {
                    try {
                        $customer = Contact::find($request->input('contact_id'));
                        if ($customer) {
                            $customer->balance += $remaining;
                            $customer->save();
                        }
                    } catch (\Exception $e) {
                        \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
                        $output = ['success' => false, 'msg' => __('messages.something_went_wrong')];
                    }
                }
            } catch (\Throwable $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
                $output = ['success' => false, 'msg' => __('messages.something_went_wrong')];
            }
            
           
            if($is_ecom){
                return $output;
            }
            if($is_util){
                return $output;
            }
            return redirect()->back()->with(['status' => $output]);
        } else {
            // normal payment
            try {
                if ($is_ecom) {
                    $business_id = $request->input('business_id');
                } else {
                    $business_id = $request->session()->get('user.business_id');
                }
                $transaction_id = $request->input('transaction_id');
                $transaction = Transaction::where('business_id', $business_id)
                    ->where('id', $transaction_id)
                    ->where('status', '!=', 'void')
                    ->with(['contact'])
                    ->firstOrFail();

                $transaction_before = $transaction->replicate();

                if (!$is_ecom) {
                    if (! (auth()->user()->can('purchase.payments') || auth()->user()->can('hms.add_booking_payment') || auth()->user()->can('sell.payments') || auth()->user()->can('all_expense.access') || auth()->user()->can('view_own_expense') || auth()->user()->can('user.update'))) {
                        abort(403, 'Unauthorized action.');
                    }
                }

                if ($transaction->payment_status != 'paid') {
                    $inputs = $request->only([
                        'amount',
                        'method',
                        'note',
                        'card_number',
                        'card_holder_name',
                        'card_transaction_number',
                        'card_type',
                        'card_month',
                        'card_year',
                        'card_security',
                        'cheque_number',
                        'bank_account_number'
                    ]);

                    if ($is_ecom) {
                        $inputs['paid_on'] = $request->input('paid_on');
                    } else {
                        $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);
                    }
                    $inputs['transaction_id'] = $transaction->id;
                    $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
                    if ($is_ecom) {
                        $inputs['created_by'] = $request->input('created_by');
                    } else {
                        $inputs['created_by'] = auth()->user()->id;
                    }
                    $inputs['payment_for'] = $transaction->contact_id;

                    if ($inputs['method'] == 'custom_pay_1') {
                        $inputs['transaction_no'] = $request->input('transaction_no_1');
                    } elseif ($inputs['method'] == 'custom_pay_2') {
                        $inputs['transaction_no'] = $request->input('transaction_no_2');
                    } elseif ($inputs['method'] == 'custom_pay_3') {
                        $inputs['transaction_no'] = $request->input('transaction_no_3');
                    }

                    if (! empty($request->input('account_id')) && $inputs['method'] != 'advance') {
                        $inputs['account_id'] = $request->input('account_id');
                    }
                    if ($is_ecom) {
                        $inputs['account_id'] = $request->input('contact_id');
                    }

                    $prefix_type = 'purchase_payment';
                    if (in_array($transaction->type, ['sell', 'sell_return'])) {
                        $prefix_type = 'sell_payment';
                    } elseif (in_array($transaction->type, ['expense', 'expense_refund'])) {
                        $prefix_type = 'expense_payment';
                    }

                    DB::beginTransaction();

                    $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type, $business_id);
                    //Generate reference number
                    $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count, $business_id, null, $is_ecom);

                    $inputs['business_id'] = $is_ecom ? $request->input('business_id') : $request->session()->get('business.id');
                    $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

                    //Pay from advance balance
                    $payment_amount = $inputs['amount'];
                    if ($payment_amount < 0) {
                        if ($is_ecom || $is_util) {
                            return [
                                'success' => false,
                                'msg' => 'Payment amount cannot be negative'
                            ];
                        } else {
                            return redirect()->back()->with(['status' => ['success' => false, 'msg' => 'Payment amount cannot be negative']]);
                        }
                    }
                    $contact_balance = ! empty($transaction->contact) ? $transaction->contact->balance : 0;
                    if ($inputs['method'] == 'advance' && $inputs['amount'] > $contact_balance && $transaction->type != 'sell_return') {
                        throw new AdvanceBalanceNotAvailable(__('lang_v1.required_advance_balance_not_available'));
                    }

                    if (! empty($inputs['amount'])) {
                        $tp = TransactionPayment::create($inputs);

                        if (! empty($request->input('denominations'))) {
                            $this->transactionUtil->addCashDenominations($tp, $request->input('denominations'));
                        }

                        $inputs['transaction_type'] = $transaction->type;
                        event(new TransactionPaymentAdded($tp, $inputs));
                    }

                    //update payment status
                    $payment_status = $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                    $transaction->payment_status = $payment_status;

                    $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);

                    DB::commit();
                }

                $output = [
                    'success' => true,
                    'msg' => __('purchase.payment_added_success'),
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                $msg = __('messages.something_went_wrong');

                if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) {
                    $msg = $e->getMessage();
                } else {
                    \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
                }

                $output = [
                    'success' => false,
                    'msg' => $msg,
                ];
            }
        }
        if($is_ecom){
            return $output;
        }
        if($is_util){
            return $output;
        }
        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments') || auth()->user()->can('hms.add_booking_payment'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $transaction = Transaction::where('id', $id)
                ->with(['contact', 'business', 'transaction_for'])
                ->first();
            $payments_query = TransactionPayment::where('transaction_id', $id);

            $accounts_enabled = false;
            if ($this->moduleUtil->isModuleEnabled('account')) {
                $accounts_enabled = true;
                $payments_query->with(['payment_account']);
            }

            $payments = $payments_query->get();
            $location_id = ! empty($transaction->location_id) ? $transaction->location_id : null;
            $payment_types = $this->transactionUtil->payment_types($location_id, true);

            return view('transaction_payment.show_payments')
                ->with(compact('transaction', 'payments', 'payment_types', 'accounts_enabled'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->json(['success' => false, 'msg' => 'Payment edit is Restricted from Our System']);
        if (! auth()->user()->can('edit_purchase_payment') && ! auth()->user()->can('edit_sell_payment') && !auth()->user()->can('hms.edit_booking_payment')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $payment_line = TransactionPayment::with(['denominations'])->where('method', '!=', 'advance')->findOrFail($id);

            $transaction = Transaction::where('id', $payment_line->transaction_id)
                ->where('business_id', $business_id)
                ->where('status', '!=', 'void')
                ->with(['contact', 'location'])
                ->first();
            if(!$transaction){
                return [
                    'success' => 0,
                    'msg' => 'Transaction not found or Voided'
                ];
            }
            $payment_types = $this->transactionUtil->payment_types($transaction->location_id);

            //Accounts
            $accounts = $this->moduleUtil->accountsDropdown($business_id, true, false, true);

            return view('transaction_payment.edit_payment_row')
                ->with(compact('transaction', 'payment_types', 'payment_line', 'accounts'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return redirect()->back()->with(['status' => ['success' => false, 'msg' => 'Payment edit is Restricted from Our System']]);
        if (! auth()->user()->can('edit_purchase_payment') && ! auth()->user()->can('edit_sell_payment') && ! auth()->user()->can('all_expense.access') && ! auth()->user()->can('view_own_expense') && !auth()->user()->can('hms.edit_booking_payment') && !auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $inputs = $request->only([
                'amount',
                'method',
                'note',
                'card_number',
                'card_holder_name',
                'card_transaction_number',
                'card_type',
                'card_month',
                'card_year',
                'card_security',
                'cheque_number',
                'bank_account_number',
            ]);
            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);
            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
            if ($inputs['amount'] < 0) {
                return redirect()->back()->with(['status' => ['success' => false, 'msg' => 'Payment amount cannot be negative']]);
            }
            
            if ($inputs['method'] == 'custom_pay_1') {
                $inputs['transaction_no'] = $request->input('transaction_no_1');
            } elseif ($inputs['method'] == 'custom_pay_2') {
                $inputs['transaction_no'] = $request->input('transaction_no_2');
            } elseif ($inputs['method'] == 'custom_pay_3') {
                $inputs['transaction_no'] = $request->input('transaction_no_3');
            }

            if (! empty($request->input('account_id'))) {
                $inputs['account_id'] = $request->input('account_id');
            }

            $payment = TransactionPayment::where('method', '!=', 'advance')->findOrFail($id);

            if (! empty($request->input('denominations'))) {
                $this->transactionUtil->updateCashDenominations($payment, $request->input('denominations'));
            }

            //Update parent payment if exists
            if (! empty($payment->parent_id)) {
                $parent_payment = TransactionPayment::find($payment->parent_id);
                $parent_payment->amount = $parent_payment->amount - ($payment->amount - $inputs['amount']);

                $parent_payment->save();
            }

            $business_id = $request->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
            // erp voided PR
                ->where('status', '!=', 'void')
                ->find($payment->transaction_id);

            $transaction_before = $transaction->replicate();
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (! empty($document_name)) {
                $inputs['document'] = $document_name;
            }

            DB::beginTransaction();

            $payment->update($inputs);

            //update payment status
            $payment_status = $this->transactionUtil->updatePaymentStatus($payment->transaction_id);
            $transaction->payment_status = $payment_status;

            $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);

            DB::commit();

            //event
            event(new TransactionPaymentUpdated($payment, $transaction->type));

            $output = [
                'success' => true,
                'msg' => __('purchase.payment_updated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(['success' => false, 'msg' => 'Payment delete is Restricted from Our System']);
        if (! auth()->user()->can('delete_purchase_payment') && ! auth()->user()->can('delete_sell_payment') && ! auth()->user()->can('all_expense.access') && ! auth()->user()->can('view_own_expense') && !auth()->user()->can('hms.delete_booking_payment') && !auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $payment = TransactionPayment::findOrFail($id);

                DB::beginTransaction();

                if (! empty($payment->transaction_id)) {
                    TransactionPayment::deletePayment($payment);
                } else { //advance payment
                    $adjusted_payments = TransactionPayment::where(
                        'parent_id',
                        $payment->id
                    )
                        ->get();

                    $total_adjusted_amount = $adjusted_payments->sum('amount');

                    //Get customer advance share from payment and deduct from advance balance
                    $total_customer_advance = $payment->amount - $total_adjusted_amount;
                    if ($total_customer_advance > 0) {
                        $this->transactionUtil->updateContactBalance($payment->payment_for, $total_customer_advance, 'deduct');
                    }

                    //Delete all child payments
                    foreach ($adjusted_payments as $adjusted_payment) {
                        //Make parent payment null as it will get deleted
                        $adjusted_payment->parent_id = null;
                        TransactionPayment::deletePayment($adjusted_payment);
                    }

                    //Delete advance payment
                    TransactionPayment::deletePayment($payment);
                }

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('purchase.payment_deleted_success'),
                ];
            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Adds new payment to the given transaction.
     *
     * @param  int  $transaction_id
     * @return \Illuminate\Http\Response
     */
    public function addPayment($transaction_id)
    {
        if (! auth()->user()->can('purchase.payments') && ! auth()->user()->can('sell.payments') && ! auth()->user()->can('all_expense.access') && ! auth()->user()->can('view_own_expense') && !auth()->user()->can('hms.add_booking_payment') && !auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
            // erp voided PR
                ->where('status', '!=', 'void')
                ->with(['contact', 'location'])
                ->findOrFail($transaction_id);

            $cid = request()->session()->get('user.id');
            if ($transaction->isEditable == false && $transaction->editingSalesRep != $cid) {
                $user =  User::find($transaction->editingSalesRep);
                $output = [
                    'status' => 0,
                    'view' => '',
                    'msg' => 'This Order locked by ' . $user->first_name . ' ' . $user->last_name,
                ];
                return json_encode($output);
            } else {
                $transaction->isEditable = false;
                $transaction->editingSalesRep =  $cid;
                $transaction->save();
            }
            if ($transaction->payment_status != 'paid') {
                $show_advance = in_array($transaction->type, ['sell_return', 'purchase_return']) ? true : false;
                $payment_types = $this->transactionUtil->payment_types($transaction->location, $show_advance);

                $paid_amount = $this->transactionUtil->getTotalPaid($transaction_id);
                $due_amount_2 = 0;
                if($transaction->type != 'sell_return' && $transaction->type != 'purchase_return'){
                    $return_transaction = Transaction::where('return_parent_id', $transaction->id)->first();
                    if($return_transaction){
                          $return_total= $return_transaction->final_total;
                          $return_paid = $this->transactionUtil->getTotalPaid($return_transaction->id);
                          $due_amount_2 = $return_total - $return_paid;
                    }
                }else{
                    $sell_transaction = Transaction::where('id', $transaction->return_parent_id)->first();
                    if($sell_transaction){
                        $sell_total= $sell_transaction->final_total;
                        $sell_paid = $this->transactionUtil->getTotalPaid($sell_transaction->id);
                        $due_amount_2 = $sell_total - $sell_paid;
                    }
                }
                $amount = ($transaction->final_total - $paid_amount)-$due_amount_2;
                if ($amount < 0) {
                    $amount = 0;
                }

                $amount_formated = $this->transactionUtil->num_f($amount);

                $payment_line = new TransactionPayment();
                $payment_line->amount = $amount;
                $payment_line->method = 'cash';
                $payment_line->paid_on = \Carbon::now()->toDateTimeString();

                //Accounts
                $accounts = $this->moduleUtil->accountsDropdown($business_id, true, false, true);

                $view = view('transaction_payment.payment_row')
                    ->with(compact('transaction', 'payment_types', 'payment_line', 'amount_formated', 'accounts'))->render();

                $output = [
                    'status' => 'due',
                    'view' => $view,
                ];
            } else {
                $output = [
                    'status' => 'paid',
                    'view' => '',
                    'msg' => __('purchase.amount_already_paid'),
                ];
            }

            return json_encode($output);
        }
    }

    /**
     * Shows contact's payment due modal
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function getPayContactDue($contact_id)
    {
        try {
            $customer = Contact::find($contact_id);
            if ($customer->contact_status == 'inactive') {
                return response([
                        'success' => false,
                        'msg' => $customer ? 'Account is deactivated' : 'Account not found',
                ]);
            }
            if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
                abort(403, 'Unauthorized action.');
            }

            // Previously this logic only ran for AJAX requests, which caused
            // a blank page when the route was opened directly via a normal link.
            // Run it for all requests so both AJAX and full-page loads work.
            $business_id = request()->session()->get('user.business_id');

                $due_payment_type = request()->input('type');
                // erp voided PR

                $query = Contact::where('contacts.id', $contact_id)
                    ->leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id');
                if ($due_payment_type == 'purchase') {
                    $query
                    ->where('t.status', '!=', 'void');
                    $query->select(
                        DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                        DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                        'contacts.name',
                        'contacts.supplier_business_name',
                        'contacts.id as contact_id'
                    );
                } elseif ($due_payment_type == 'purchase_return') {
                    $query->select(
                        DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                        DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                        'contacts.name',
                        'contacts.supplier_business_name',
                        'contacts.id as contact_id'
                    );
                } elseif ($due_payment_type == 'sell') {
                    $query->select(
                        DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                        DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                        'contacts.name',
                        'contacts.supplier_business_name',
                        'contacts.id as contact_id'
                    );
                    $query->addSelect(
                        DB::raw("SUM(IF(t.type = 'sell_return',  final_total, 0)) as total_sell_return"),
                        DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid")
                    );
                } elseif ($due_payment_type == 'sell_return') {
                    $query->select(
                        DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                        DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                        'contacts.name',
                        'contacts.supplier_business_name',
                        'contacts.id as contact_id'
                    );
                }

                //Query for opening balance details
                $query->addSelect(
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
                );
                $contact_details = $query->first();

                $payment_line = new TransactionPayment();
                if ($due_payment_type == 'purchase') {
                    $contact_details->total_purchase = empty($contact_details->total_purchase) ? 0 : $contact_details->total_purchase;
                    $payment_line->amount = $contact_details->total_purchase -
                        $contact_details->total_paid;
                } elseif ($due_payment_type == 'purchase_return') {
                    $payment_line->amount = $contact_details->total_purchase_return -
                        $contact_details->total_return_paid;
                } elseif ($due_payment_type == 'sell') {
                    $contact_details->total_invoice = empty($contact_details->total_invoice) ? 0 : $contact_details->total_invoice;

                    $payment_line->amount = $contact_details->total_invoice -
                        $contact_details->total_paid;
                } elseif ($due_payment_type == 'sell_return') {
                    $payment_line->amount = $contact_details->total_sell_return -
                        $contact_details->total_return_paid;
                }

                //If opening balance due exists add to payment amount
                $contact_details->opening_balance = ! empty($contact_details->opening_balance) ? $contact_details->opening_balance : 0;
                $contact_details->opening_balance_paid = ! empty($contact_details->opening_balance_paid) ? $contact_details->opening_balance_paid : 0;
                $ob_due = $contact_details->opening_balance - $contact_details->opening_balance_paid;
                if ($ob_due > 0) {
                    $payment_line->amount += $ob_due;
                }

                $amount_formated = $this->transactionUtil->num_f($payment_line->amount);

                $contact_details->total_paid = empty($contact_details->total_paid) ? 0 : $contact_details->total_paid;

                $payment_line->method = 'cash';
                $payment_line->paid_on = \Carbon::now()->toDateTimeString();
                $advanceAmount = 0;
                if($contact_id){
                    $advanceAmount = Contact::where('id', $contact_id)->first()->balance;
                    if($advanceAmount > 0){
                        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
                    } else {
                        $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);
                    }
                } else {
                    $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);
                }
                //Accounts
                $accounts = $this->moduleUtil->accountsDropdown($business_id, true);


                $pending_transactions = [];
                if (in_array($due_payment_type, ['purchase', 'sell', 'purchase_return', 'sell_return'])) {
                    $type = $due_payment_type;
                    $statusCheck = $type === 'sell' ? 'final' : null;

                    $transactions = Transaction::where('contact_id', $contact_id)
                        ->where('type', $type)
                        // erp voided PR exclude bcz on void final total reduced 
                        ->where('status', '!=', 'void')
                        ->when($statusCheck, function ($query) use ($statusCheck) {
                            return $query->where('status', $statusCheck);
                        })
                        ->where(function ($q) {
                            $q->where('payment_status', '!=', 'paid')
                                ->orWhereNull('payment_status');
                        })
                        ->where('status', '!=', 'draft') // Exclude draft transactions
                        ->with(['payment_lines' => function ($query) {
                            $query->select('transaction_id', DB::raw('SUM(amount) as total_paid'))
                                ->groupBy('transaction_id');
                        }])
                        ->select('id', 'ref_no', 'invoice_no', 'final_total', 'payment_status', 'type', 'status', 'transaction_date', 'pay_term_type', 'pay_term_number')
                        ->orderBy('transaction_date', 'asc')
                        ->get()
                        ->map(function ($transaction) use ($type) {
                            $total_paid = $transaction->payment_lines->first()->total_paid ?? 0;
                            $due_amount = $transaction->final_total - $total_paid;
                            
                            // Calculate due_date
                            $due_date = null;
                            if (!empty($transaction->transaction_date)) {
                                $due_date = \Carbon\Carbon::parse($transaction->transaction_date);
                                if (!empty($transaction->pay_term_type) && !empty($transaction->pay_term_number)) {
                                    if ($transaction->pay_term_type == 'days') {
                                        $due_date->addDays($transaction->pay_term_number);
                                    } elseif ($transaction->pay_term_type == 'months') {
                                        $due_date->addMonths($transaction->pay_term_number);
                                    }
                                }
                            }
                            
                            return (object) [
                                'id'               => $transaction->id,
                                'invoice_no'       => $type === 'sell' ? $transaction->invoice_no : $transaction->ref_no,
                                'payment_status'   => ucfirst($transaction->payment_status ?? 'Unpaid'),
                                'final_total'      => $transaction->final_total,
                                'total_paid'       => $total_paid,
                                'due_amount'       => $due_amount,
                                'return_due'       => $this->getReturnDue($transaction->id),
                                'status'           => $transaction->status,
                                'due_date'         => $due_date
                            ];
                        })
                        ->filter(function ($transaction) {
                            return $transaction->status !== 'draft' && $transaction->status !== 'quotation';
                        });
                    $pending_transactions = $transactions;
                }
                
            // Load full contact with relationships for customer details
            $contact = Contact::with('customerGroup')->find($contact_id);
            
            return view('transaction_payment.pay_supplier_due_modal')
                ->with(compact('contact_details', 'advanceAmount', 'payment_types', 'pending_transactions', 'payment_line', 'due_payment_type', 'ob_due', 'amount_formated', 'accounts', 'pending_transactions', 'contact'));
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ], 500);
        }
    }
    public function getPayContactDueB2B()
    {
        $user = Auth::guard('api')->user();
        
        $customer = Contact::find($user->id);
        if ($customer->contact_status == 'inactive') {
            return response([
                    'success' => false,
                    'msg' => $customer ? 'Account is deactivated' : 'Account not found',
            ]);
        }

            $due_payment_type = 'sell';
            // erp voided PR

            $query = Contact::where('contacts.id', $user->id)
                ->leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id');
                $query->select(
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                );
                $query->addSelect(
                    DB::raw("SUM(IF(t.type = 'sell_return',  final_total, 0)) as total_return_due"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid")
                );
            $contact_details = $query->first();

            $payment_line = new TransactionPayment();
            if ($due_payment_type == 'sell') {
                $contact_details->total_invoice = empty($contact_details->total_invoice) ? 0 : $contact_details->total_invoice;

                $payment_line->amount = $contact_details->total_invoice -
                $contact_details->total_paid;
            } else { // sell_return
                $payment_line->amount = $contact_details->total_return_due -
                    $contact_details->total_return_paid;
            }

            //If opening balance due exists add to payment amount

            $amount_formated =$payment_line->amount;
            $contact_details->total_paid = empty($contact_details->total_paid) ? 0 : $contact_details->total_paid;

            $payment_line->method = 'cash';
            $payment_line->paid_on = \Carbon::now()->toDateTimeString();
            $advanceAmount = 0;
            $advanceAmount = Contact::where('id', $user->id)->first()->balance;

            $pending_transactions = [];
            if (in_array($due_payment_type, ['purchase', 'sell', 'purchase_return', 'sell_return'])) {
                $type = $due_payment_type;
                $statusCheck = $type === 'sell' ? 'final' : null;

                $transactions = Transaction::where('contact_id', $user->id)
                    ->where('type', $type)
                    // erp voided PR exclude bcz on void final total reduced 
                    ->where('status', '!=', 'void')
                    ->when($statusCheck, function ($query) use ($statusCheck) {
                        return $query->where('status', $statusCheck);
                    })
                    ->where(function ($q) {
                        $q->where('payment_status', '!=', 'paid')
                            ->orWhereNull('payment_status');
                    })
                    ->where('status', '!=', 'draft') // Exclude draft transactions
                    ->with(['payment_lines' => function ($query) {
                        $query->select('transaction_id', DB::raw('SUM(amount) as total_paid'))
                            ->groupBy('transaction_id');
                    }])
                    ->select('id', 'ref_no', 'invoice_no', 'final_total', 'payment_status', 'type', 'status')
                    ->orderBy('transaction_date', 'asc')
                    ->get()
                    ->map(function ($transaction) use ($type) {
                        $total_paid = $transaction->payment_lines->first()->total_paid ?? 0;
                        $due_amount = $transaction->final_total - $total_paid;
                        return (object) [
                            'id'               => $transaction->id,
                            'invoice_no'       => $type === 'sell' ? $transaction->invoice_no : $transaction->ref_no,
                            'payment_status'   => ucfirst($transaction->payment_status ?? 'Unpaid'),
                            'final_total'      => $transaction->final_total,
                            'total_paid'       => $total_paid,
                            'due_amount'       => $due_amount,
                            'return_due'       => $this->getReturnDue($transaction->id),
                            'status'           => $transaction->status
                        ];
                    })
                    ->filter(function ($transaction) {
                        return $transaction->status !== 'draft' && $transaction->status !== 'quotation';
                    });
                $pending_transactions = $transactions;
            }
            return response()->json([
                'success' => true,
                'contact_details' => $contact_details,
                'wallet_balance' => $advanceAmount,
                'pending_transactions' => $pending_transactions,
                'payment_line' => $payment_line,
                'due_payment_type' => $due_payment_type,
                'total_due' => $amount_formated-$contact_details->total_return_due,
                'pending_transactions' => $pending_transactions
            ]);
        
    }

    public function payContactDueB2B(Request $request)
    {
        try {
            // Get authenticated customer (always from API guard for e-commerce)
            $user = Auth::guard('api')->user();
            if (!$user) {
                return [
                    'success' => false,
                    'msg' => 'User not authenticated'
                ];
            }

            // Always use business_id = 1 for e-commerce
            $business_id = 1;
            $customer = Contact::find($user->id);
            
            if (!$customer) {
                return [
                    'success' => false,
                    'msg' => 'Customer not found'
                ];
            }

            // Validate input
            $transaction_ids = $request->input('transaction_ids', []);
            $transaction_number = $request->input('transaction_number');
            $paid_amount = $request->input('paid_amount', 0);
            $method = $request->input('method', 'card');

            if (empty($transaction_ids) || !is_array($transaction_ids)) {
                return [
                    'success' => false,
                    'msg' => 'Transaction IDs are required and must be an array'
                ];
            }

            if ($paid_amount <= 0) {
                return [
                    'success' => false,
                    'msg' => 'Payment amount must be greater than zero'
                ];
            }

            // Verify all transaction IDs belong to this customer (can be sell or sell_return)
            $transactions = Transaction::whereIn('id', $transaction_ids)
                ->where('contact_id', $customer->id)
                ->where('business_id', $business_id)
                ->whereIn('type', ['sell', 'sell_return'])
                ->where('status', '!=', 'void')
                ->get();

            if ($transactions->count() !== count($transaction_ids)) {
                return [
                    'success' => false,
                    'msg' => 'Some transactions do not belong to this customer or are invalid'
                ];
            }

            // Calculate amounts for each transaction
            $transaction_amounts = [];
            $total_payment_needed = 0;

            foreach ($transactions as $transaction) {
                $due_amount = $transaction->final_total - $transaction->payment_lines()->sum('amount');
                
                if ($due_amount > 0) {
                    $transaction_amounts[$transaction->id] = $due_amount;
                    $total_payment_needed += $due_amount;
                }
            }

            // Validate that provided amount matches what's needed
            if ($paid_amount != $total_payment_needed) {
                return [
                    'success' => false,
                    'msg' => "Payment amount mismatch. Required: {$total_payment_needed}, Provided: {$paid_amount}"
                ];
            }

            if ($total_payment_needed <= 0) {
                return [
                    'success' => false,
                    'msg' => 'No payment is due for the selected transactions'
                ];
            }

            // Generate group reference for tracking
            $group_ref_no = $this->transactionUtil->setAndGetReferenceCount('transaction_payment_groups_count', $business_id);
            $transaction_payment_groups = [];
            $payment_details = [];
            $paid_on = now()->toDateTimeString();

            // Manually pay each specific transaction (like store method does for pay_by_choice)
            foreach ($transaction_amounts as $transaction_id => $amount) {
                
                DB::beginTransaction();
                
                try {
                    $transaction = Transaction::where('business_id', $business_id)
                        ->where('id', $transaction_id)
                        ->where('status', '!=', 'void')
                        ->firstOrFail();

                    if ($transaction->payment_status != 'paid') {
                        // Prepare payment inputs
                        $inputs = [
                            'method' => $method,
                            'card_transaction_number' => $transaction_number,
                            'paid_on' => $paid_on,
                            'transaction_id' => $transaction->id,
                            'amount' => $amount,
                            'created_by' => $customer->id,
                            'payment_for' => $transaction->contact_id,
                            'business_id' => $business_id,
                        ];

                        // Determine payment type prefix
                        $prefix_type = 'sell_payment';
                        if (in_array($transaction->type, ['sell_return'])) {
                            $prefix_type = 'sell_payment';
                        }

                        // Generate payment reference number
                        $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type, $business_id);
                        $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count, $business_id, null, true);

                        // Create payment record
                        $tp = TransactionPayment::create($inputs);

                        // Store in transaction_payment_groups table
                        $transaction_payment_groups[] = [
                            'business_id' => $business_id,
                            'transaction_id' => $transaction_id,
                            'amount' => $amount,
                            'payment_method_id' => $tp->id,
                            'group_name' => $transaction->type,
                            'group_ref_no' => $group_ref_no,
                            'contact_id' => $transaction->contact_id,
                        ];

                        // Fire event
                        $inputs['transaction_type'] = $transaction->type;
                        event(new \App\Events\TransactionPaymentAdded($tp, $inputs));

                        // Update payment status
                        $payment_status = $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                        $transaction->payment_status = $payment_status;
                        $transaction->save();

                        // Add to payment details
                        $payment_details[] = [
                            'transaction_id' => $transaction->id,
                            'invoice_no' => $transaction->invoice_no,
                            'type' => $transaction->type,
                            'amount' => $amount,
                            'payment_id' => $tp->id,
                            'payment_ref_no' => $tp->payment_ref_no
                        ];

                        // Activity log
                        activity()
                            ->performedOn($tp)
                            ->causedBy($user)
                            ->withProperties([
                                'customized' => [
                                    'old_value' => 0,
                                    'new_value' => $tp->amount,
                                    'mid' => ' added amount (B2B)',
                                    'modal_ref_no' => $tp->payment_ref_no,
                                ]
                            ])
                            ->useLog('payment_added')
                            ->tap(function($activity) use ($business_id) {
                                $activity->business_id = $business_id;
                                $activity->save();
                            })
                            ->log('payment_added');

                        DB::commit();
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
                    
                    return [
                        'success' => false,
                        'msg' => 'Payment failed for transaction ' . $transaction_id . ': ' . $e->getMessage(),
                    ];
                }
            }

            // Insert all payment groups
            if (!empty($transaction_payment_groups)) {
                \App\Models\TransactionPaymentGroup::insert($transaction_payment_groups);
            }

            return [
                'success' => true,
                'msg' => __('purchase.payment_added_success'),
                'total_amount_paid' => $total_payment_needed,
                'payment_details' => $payment_details,
                'group_ref_no' => 'GRP-' . $group_ref_no,
            ];

        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return [
                'success' => false,
                'msg' => 'Payment failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process invoice payment multiple orders using registered billing and shipping details
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processOrderPayment(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'msg' => 'User not authenticated'
                ], 401);
            }

            $business_id = 1;
            $customer = Contact::find($user->id);
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Customer not found'
                ], 404);
            }
            $validator = Validator::make($request->all(), [
                'order_ids' => 'required|array',
                'order_ids.*' => 'required|integer',
                'payment_method' => 'required|string|in:card,electronic_check',
                'payment_amounts' => 'required|array',
                'payment_amounts.*' => 'required|numeric|min:0.01',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $order_ids = $request->input('order_ids');
            $payment_amounts = $request->input('payment_amounts');
            $payment_method = $request->input('payment_method');
            $transaction_number = $request->input('transaction_number', '');
            
            $billingAddress = CustomerAddress::where('contact_id', $customer->id)
                ->where('address_type', 'billing')
                ->orderBy('created_at', 'desc')
                ->first();

            $shippingAddress = CustomerAddress::where('contact_id', $customer->id)
                ->where('address_type', 'shipping')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$billingAddress) {
                $billingAddress = (object)[
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'company' => $customer->supplier_business_name,
                    'address_line_1' => $customer->address_line_1,
                    'address_line_2' => $customer->address_line_2,
                    'city' => $customer->city,
                    'state' => $customer->state,
                    'zip_code' => $customer->zip_code,
                    'country' => $customer->country ?? 'US',
                ];
            }

            if (!$shippingAddress) {
                $shippingAddress = (object)[
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'company' => $customer->supplier_business_name,
                    'address_line_1' => $customer->shipping_address1 ?? $customer->address_line_1,
                    'address_line_2' => $customer->shipping_address2 ?? $customer->address_line_2,
                    'city' => $customer->shipping_city ?? $customer->city,
                    'state' => $customer->shipping_state ?? $customer->state,
                    'zip_code' => $customer->shipping_zip ?? $customer->zip_code,
                    'country' => $customer->shipping_country ?? $customer->country ?? 'US',
                ];
            }
            $unique_order_ids = array_unique($order_ids);
            $order_id_to_amount_map = [];
            
            foreach ($order_ids as $index => $order_id) {
                if (!isset($order_id_to_amount_map[$order_id])) {
                    $order_id_to_amount_map[$order_id] = 0;
                }
                $order_id_to_amount_map[$order_id] += $payment_amounts[$index] ?? 0;
            }
            $transactions = Transaction::whereIn('id', $unique_order_ids)
                ->where('contact_id', $customer->id)
                ->where('business_id', $business_id)
                ->whereIn('type', ['sell', 'sell_return'])
                ->where('status', '!=', 'void')
                ->get();
            
            if ($transactions->count() !== count($unique_order_ids)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Some orders do not belong to this customer or are invalid'
                ], 400);
            }
            
            $transaction_amounts = [];
            $total_payment_needed = 0;
            $validation_errors = [];
            
            foreach ($transactions as $transaction) {
                $total_paid = $this->transactionUtil->getTotalPaid($transaction->id);
                $due_amount = $transaction->final_total - $total_paid;
                $requested_amount = $order_id_to_amount_map[$transaction->id] ?? 0;
                
                if ($due_amount > 0) {
                    if (abs($requested_amount - $due_amount) > 0.01) {
                        $validation_errors[] = [
                            'order_id' => $transaction->id,
                            'invoice_no' => $transaction->invoice_no,
                            'required_amount' => $due_amount,
                            'provided_amount' => $requested_amount,
                            'final_total' => $transaction->final_total,
                            'total_paid' => $total_paid
                        ];
                    } else {
                        $transaction_amounts[$transaction->id] = $due_amount;
                        $total_payment_needed += $due_amount;
                    }
                } elseif ($requested_amount > 0) {
                    $validation_errors[] = [
                        'order_id' => $transaction->id,
                        'invoice_no' => $transaction->invoice_no,
                        'message' => 'Order is already fully paid',
                        'final_total' => $transaction->final_total,
                        'total_paid' => $total_paid,
                        'due_amount' => $due_amount
                    ];
                }
            }
            
            if (!empty($validation_errors)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Payment amount validation failed',
                    'errors' => $validation_errors
                ], 400);
            }

            if ($total_payment_needed <= 0) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No payment is due for the selected orders'
                ], 400);
            }

            $group_ref_no = $this->transactionUtil->setAndGetReferenceCount('transaction_payment_groups_count', $business_id);
            $transaction_payment_groups = [];
            $payment_details = [];
            $paid_on = now()->toDateTimeString();
            $transactions_map = $transactions->keyBy('id');

            foreach ($transaction_amounts as $transaction_id => $amount) {
                DB::beginTransaction();
                
                try {
                    $transaction = $transactions_map[$transaction_id];

                    if ($transaction->payment_status != 'paid') {
                        $ref_count = $this->transactionUtil->setAndGetReferenceCount('sell_payment', $business_id);
                        
                        $inputs = [
                            'method' => $payment_method,
                            'card_transaction_number' => $transaction_number,
                            'paid_on' => $paid_on,
                            'transaction_id' => $transaction->id,
                            'amount' => $amount,
                            'created_by' => $customer->id,
                            'payment_for' => $transaction->contact_id,
                            'business_id' => $business_id,
                            'payment_ref_no' => $this->transactionUtil->generateReferenceNumber('sell_payment', $ref_count, $business_id, null, true),
                        ];

                        $tp = TransactionPayment::create($inputs);

                        $transaction_payment_groups[] = [
                            'business_id' => $business_id,
                            'transaction_id' => $transaction_id,
                            'amount' => $amount,
                            'payment_method_id' => $tp->id,
                            'group_name' => $transaction->type,
                            'group_ref_no' => $group_ref_no,
                            'contact_id' => $transaction->contact_id,
                        ];

                        $inputs['transaction_type'] = $transaction->type;
                        event(new TransactionPaymentAdded($tp, $inputs));

                        $payment_status = $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                        $transaction->payment_status = $payment_status;
                        $transaction->save();

                        $payment_details[] = [
                            'transaction_id' => $transaction->id,
                            'invoice_no' => $transaction->invoice_no,
                            'type' => $transaction->type,
                            'amount' => $amount,
                            'payment_id' => $tp->id,
                            'payment_ref_no' => $tp->payment_ref_no
                        ];

                        activity()
                            ->performedOn($tp)
                            ->causedBy($customer)
                            ->withProperties([
                                'customized' => [
                                    'old_value' => 0,
                                    'new_value' => $tp->amount,
                                    'mid' => ' added amount (Invoice Payment)',
                                    'modal_ref_no' => $tp->payment_ref_no,
                                ]
                            ])
                            ->useLog('payment_added')
                            ->tap(function($activity) use ($business_id) {
                                $activity->business_id = $business_id;
                                $activity->save();
                            })
                            ->log('payment_added');
                        \Log::info('Payment added successfully for order ' . $transaction->invoice_no);
                        DB::commit();
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
                    
                    return response()->json([
                        'success' => false,
                        'msg' => 'Payment failed for order ' . $transaction->invoice_no . ': ' . $e->getMessage(),
                    ], 500);
                }
            }

            if (!empty($transaction_payment_groups)) {
                \App\Models\TransactionPaymentGroup::insert($transaction_payment_groups);
            }

            return response()->json([
                'success' => true,
                'msg' => 'Payment processed successfully',
                'total_amount_paid' => $total_payment_needed,
                'payment_details' => $payment_details,
                'group_ref_no' => 'GRP-' . $group_ref_no
            ]);

        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => 'Payment failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getReturnDue($transaction_id)
    {
        $return_transaction = Transaction::where('return_parent_id', $transaction_id)->first();
        if($return_transaction){
            $paid_amount = $this->transactionUtil->getTotalPaid($return_transaction->id);
            return $return_transaction->final_total - $paid_amount;
        }
        return 0;
    }
    
    /**
     * Adds Payments for Contact due
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response | mixed
     */
    public function postPayContactDue(Request $request, $is_ecom = false)
    {

        if ($is_ecom) {
        } else {
            if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
                abort(403, 'Unauthorized action.');
            }
        }

        // Validate that payment amount is not negative
        if ($request->input('amount') < 0) {
            if ($is_ecom) {
                return [
                    'success' => false,
                    'msg' => 'Payment amount cannot be negative'
                ];
            } else {
                return redirect()->back()->with(['status' => ['success' => false, 'msg' => 'Payment amount cannot be negative']]);
            }
        }

        // Advance Payment Logic Validation
        if($request->input('method') =='advance'){
            $currentAdvance = Contact::where('id',$request->input('contact_id'))->first()->balance;
            $payedAmount = $request->input('transactions')?? [0=>0];
            $payedAmount = array_sum($payedAmount);
            
            // For sell returns, we don't need to check advance balance since we're adding to wallet
            $due_payment_type = $request->input('due_payment_type');
            $isSellReturn = ($due_payment_type == 'sell_return');
            
            if(!$isSellReturn && ($payedAmount > $currentAdvance || $payedAmount <= 0)){
                if($is_ecom){
                    return [
                        'success' => false,
                        'msg' => $payedAmount <= 0 ? 'Nothing to pay' : 'Advance amount is not enough'
                    ];
                }
                return redirect()->back()->with(['status' => ['success' => false, 'msg' => $payedAmount <= 0 ? 'Nothing to pay' : 'Advance amount is not enough']]);
            }
            $request->merge(['amount' => $payedAmount]);
        }

        // For non-advance payments:
        // - pay_by_choice should follow the sum of row allocations
        // - pay_by_oldest should respect the main amount input so overpay
        //   can be stored as open balance (contact balance)
        if ($request->input('method') !== 'advance') {
            $transactions = $request->input('transactions', []);
            $payedAmount = 0;
            if (is_array($transactions) && !empty($transactions)) {
                $payedAmount = array_sum($transactions);
            }

            $rawAmount = $request->input('amount');
            $inputAmount = $rawAmount !== null ? $this->transactionUtil->num_uf($rawAmount) : 0;
            $contactTypeRadio = $request->input('contact_type_radio');

            if ($contactTypeRadio === 'pay_by_choice' || $inputAmount <= 0) {
                if ($payedAmount > 0) {
                    $request->merge(['amount' => $payedAmount]);
                }
            } else {
                // pay_by_oldest or default: keep main amount (allow overpayment)
                if ($payedAmount > $inputAmount) {
                    $request->merge(['amount' => $payedAmount]);
                } else {
                    $request->merge(['amount' => $inputAmount]);
                }
            }
        }

        if ($request->input('contact_type_radio') == 'pay_by_choice') {
            $output = $this->store($request, $is_ecom,true);
            if (is_array($output)) {
                return redirect()->back()->with(['status' => $output]);
            }
            // return redirect()->back();
        } else{
            // Validate: do not allow payment when there is no amount (e.g. customer has zero balance)
            $amountToPay = $request->input('amount');
            $amountValue = $amountToPay !== null && $amountToPay !== '' ? $this->transactionUtil->num_uf($amountToPay) : 0;
            if ($amountValue <= 0) {
                $message = __('lang_v1.no_outstanding_balance_to_receive_payment');
                if ($message === 'lang_v1.no_outstanding_balance_to_receive_payment') {
                    $message = 'This customer has no outstanding balance. Payment cannot be recorded for zero balance.';
                }
                if ($is_ecom) {
                    return ['success' => false, 'msg' => $message];
                }
                return redirect()->back()->with(['status' => ['success' => false, 'msg' => $message]]);
            }

            try {
                DB::beginTransaction();
                if ($is_ecom) {
                    $business_id = $request->input('business_id');
                } else {
                    $business_id = request()->session()->get('business.id');
                }
    
                $tp = $this->transactionUtil->payContact($request, true, $is_ecom);
                
                // erp custom store payment distribution in transaction_payment_groups table for pay_by_oldest
                if ($tp && $tp->id && $request->has('contact_type_radio')) {
                    $due_payment_type = $request->input('due_payment_type');
                    if (empty($due_payment_type)) {
                        $contact = Contact::find($request->input('contact_id'));
                        $due_payment_type = $contact->type == 'supplier' ? 'purchase' : 'sell';
                    }
                    $transaction_payment_groups = [];
                    $paying_transactions = $request->input('transactions');
                    if (! is_array($paying_transactions)) {
                        $paying_transactions = [];
                    }
                    $group_ref_no = $this->transactionUtil->setAndGetReferenceCount('transaction_payment_groups_count', $business_id);
                    $paymentList = TransactionPayment::where('parent_id', $tp->id)->get();

                    foreach ($paying_transactions as $id => $amount) {
                        $transaction = Transaction::find($id);
                        $transaction_payment = $paymentList->where('transaction_id', $transaction->id)->first();
                        
                        if ($amount > 0) {
                            $transaction_payment_groups[] = [
                                'business_id' => $business_id,
                                'transaction_id' => $transaction->id,
                                'amount' => $amount,
                                'payment_method_id' => $transaction_payment->id,
                                'group_name' => $transaction->type,
                                'group_ref_no' => $group_ref_no,
                                'contact_id' => $transaction->contact_id,
                            ];
                        }
                    }
                    if (!empty($transaction_payment_groups)) {
                        \App\Models\TransactionPaymentGroup::insert($transaction_payment_groups);
                    }
                }

                
                if ($is_ecom) {
                    $pos_settings = [];
                } else {
                    $pos_settings = ! empty(session()->get('business.pos_settings')) ? json_decode(session()->get('business.pos_settings'), true) : [];
                }
                $enable_cash_denomination_for_payment_methods = ! empty($pos_settings['enable_cash_denomination_for_payment_methods']) ? $pos_settings['enable_cash_denomination_for_payment_methods'] : [];
                //add cash denomination
                
                if ($tp && in_array($tp->method, $enable_cash_denomination_for_payment_methods) && ! empty($request->input('denominations')) && ! empty($pos_settings['enable_cash_denomination_on']) && $pos_settings['enable_cash_denomination_on'] == 'all_screens') {
                    $denominations = [];
    
                    foreach ($request->input('denominations') as $key => $value) {
                        if (! empty($value)) {
                            $denominations[] = [
                                'business_id' => $business_id,
                                'amount' => $key,
                                'total_count' => $value,
                            ];
                        }
                    }
    
                    if (! empty($denominations)) {
                        $tp->denominations()->createMany($denominations);
                    }
                }
    
                DB::commit();
    
                $output = [
                    'success' => true,
                    'msg' => __('purchase.payment_added_success'),
                ];
                // custom activity log , own activity log
                if ($tp) {
                    activity()
                        ->performedOn($tp)
                        ->causedBy(auth()->user())
                        ->withProperties([
                            'customized' => [
                                'old_value' => 0,
                                'new_value' => $tp->amount,
                                'mid' => ' added amount ',
                                'modal_ref_no' => $tp->payment_ref_no,
                            ]
                        ])
                        ->useLog('payment_added')
                        ->tap(function($activity) {
                            $activity->business_id = request()->session()->get('user.business_id');
                            $activity->save();
                        })
                        ->log('payment_added');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
    
                $output = [
                    'success' => false,
                    'msg' => 'File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage(),
                ];
            }
        }
        
        if ($is_ecom) {
            return $output;
        }
        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * view details of single..,
     * payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewPayment($payment_id)
    {
        if (! (auth()->user()->can('sell.payments') ||
            auth()->user()->can('purchase.payments') ||
            auth()->user()->can('edit_sell_payment') ||
            auth()->user()->can('delete_sell_payment') ||
            auth()->user()->can('edit_purchase_payment') ||
            auth()->user()->can('delete_purchase_payment') ||
            auth()->user()->can('hms.add_booking_payment')
        )) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('business.id');
            $single_payment_line = TransactionPayment::findOrFail($payment_id);

            $transaction = null;
            if (! empty($single_payment_line->transaction_id)) {
                $transaction = Transaction::where('id', $single_payment_line->transaction_id)
                    ->with(['contact', 'location', 'transaction_for'])
                    ->first();
            } else {
                $child_payment = TransactionPayment::where('business_id', $business_id)
                    ->where('parent_id', $payment_id)
                    ->with(['transaction', 'transaction.contact', 'transaction.location', 'transaction.transaction_for'])
                    ->first();
                $transaction = ! empty($child_payment) ? $child_payment->transaction : null;
            }

            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

            return view('transaction_payment.single_payment_view')
                ->with(compact('single_payment_line', 'transaction', 'payment_types'));
        }
    }

    /**
     * Retrieves all the child payments of a parent payments
     * payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showChildPayments($payment_id)
    {
        if (! (auth()->user()->can('sell.payments') ||
            auth()->user()->can('purchase.payments') ||
            auth()->user()->can('edit_sell_payment') ||
            auth()->user()->can('delete_sell_payment') ||
            auth()->user()->can('edit_purchase_payment') ||
            auth()->user()->can('delete_purchase_payment')
        )) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('business.id');

            $child_payments = TransactionPayment::where('business_id', $business_id)
                ->where('parent_id', $payment_id)
                ->with(['transaction', 'transaction.contact'])
                ->get();

            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

            return view('transaction_payment.show_child_payments')
                ->with(compact('child_payments', 'payment_types'));
        }
    }

    /**
     * Retrieves list of all opening balance payments.
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function getOpeningBalancePayments($contact_id)
    {
        if (! (auth()->user()->can('sell.payments') ||
            auth()->user()->can('purchase.payments') ||
            auth()->user()->can('edit_sell_payment') ||
            auth()->user()->can('delete_sell_payment') ||
            auth()->user()->can('edit_purchase_payment') ||
            auth()->user()->can('delete_purchase_payment')
        )) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $query = TransactionPayment::leftjoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'opening_balance')
                ->where('t.contact_id', $contact_id)
                ->where('transaction_payments.business_id', $business_id)
                ->select(
                    'transaction_payments.amount',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    'transaction_payments.id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number'
                )
                ->groupBy('transaction_payments.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            return Datatables::of($query)
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) {
                    $method = __('lang_v1.' . $row->method);
                    if ($row->method == 'cheque') {
                        $method .= '<br>(' . __('lang_v1.cheque_no') . ': ' . $row->cheque_number . ')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>(' . __('lang_v1.card_transaction_no') . ': ' . $row->card_transaction_number . ')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>(' . __('lang_v1.bank_account_no') . ': ' . $row->bank_account_number . ')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method = __('lang_v1.custom_payment_1') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method = __('lang_v1.custom_payment_2') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method = __('lang_v1.custom_payment_3') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    }

                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="display_currency paid-amount" data-orig-value="' . $row->amount . '" data-currency_symbol = true>' . $row->amount . '</span>';
                })
                ->addColumn('action', '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary view_payment" data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'viewPayment\'], [$id]) }}"><i class="fas fa-eye"></i> @lang("messages.view")
                    </button> <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info edit_payment" 
                    data-href="{{action([\App\Http\Controllers\TransactionPaymentController::class, \'edit\'], [$id]) }}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    &nbsp; <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error delete_payment" 
                    data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'destroy\'], [$id]) }}"
                    ><i class="fa fa-trash" aria-hidden="true"></i> @lang("messages.delete")</button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-accent" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['amount', 'method', 'action'])
                ->make(true);
        }
    }

    /**
     * Get contact payment group
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response | mixed
     */
    public function getContactPaymentGroup($contact_id){
        // {"type":"table","name":"transaction_payment_groups","database":"erptest2","data":
        //     [
        //     {"id":"19","group_name":"sell","business_id":"1","transaction_id":"23","payment_method_id":"30","amount":"301.0000","group_ref_no":"9","created_at":null,"updated_at":null},
        //     {"id":"20","group_name":"sell","business_id":"1","transaction_id":"24","payment_method_id":"31","amount":"1.0000","group_ref_no":"9","created_at":null,"updated_at":null}
        
        //     ]    so total payment of 302 
        // table data group_ref_nos , group_name, sum of amount  
        if(request()->ajax()){
            try {
                $query = TransactionPaymentGroup::where('contact_id', $contact_id)
                    ->select('group_ref_no', 'group_name', \DB::raw('SUM(amount) as total_amount'))
                    ->groupBy('group_ref_no', 'group_name')
                    ->get();
                
                
                return DataTables::of($query)
                    ->addColumn('edit_payment', function($row){
                        return '<a href="#" data-href="/payment-group/'.$row->group_ref_no.'" class="btn btn-primary btn-modal" data-container=".view_modal">Edit</a>';
                    })
                    ->addColumn('view_payment', function($row){
                        return '<a href="#" data-href="/payment-group/'.$row->group_ref_no.'" class="btn btn-primary btn-modal" data-container=".view_modal">View</a>';
                    })
                    ->rawColumns(['edit_payment', 'view_payment'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        
        return response()->json(['error' => 'Not an AJAX request'], 400);
    }
    /**
     * Get payment group
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response | mixed
     */
    public function getPaymentGroup($id){
        $business_id = request()->session()->get('business.id');
       
        $paymentGroup = TransactionPaymentGroup::where('group_ref_no', $id)->get();
        $group_amount = $paymentGroup->sum('amount');
        $transactionIds = $paymentGroup->pluck('transaction_id');
        $transactions = Transaction::with('contact','payment_lines')
        ->whereIn('id', $transactionIds)->get();

        $data = [];
        $transactionrows=[];
        foreach($transactions as $transaction){
            $transactionrows[] = [
                'transaction_id' => $transaction->id,
                'invoice_no' => $transaction->invoice_no,
                'total_amount' => $transaction->final_total,
                'total_paid_amount' => $transaction->payment_lines->sum('amount'),
                'paid_amount' => $paymentGroup->where('transaction_id', $transaction->id)->first()->amount,
                'payment_method_id' => $paymentGroup->where('transaction_id', $transaction->id)->first()->payment_method_id,
                'transaction_payment_group_id' => $paymentGroup->where('transaction_id', $transaction->id)->first()->id,
            ];
        }
        $data['transaction_rows'] = $transactionrows;
        $data['group_amount'] = $group_amount;
        $data['group_ref_no'] = $id;

        // Get payment methods for the dropdown
        $payment_methods = $this->transactionUtil->payment_types(null, true, $business_id);

        return view('transaction_payment.payment_group_view', compact('data', 'payment_methods'));
    }

    /**
     * This just allow to swap the payment if amount remaining will be added to walled, overall credited amount will be consistent
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response | mixed
     */
    public function updatePaymentGroup(Request $request, $id){
        // dd($request->all());
        $business_id = request()->session()->get('business.id');
        
        $validator = Validator::make($request->all(), [
            'transaction_rows' => 'required|array',
            
        ]);
        if($validator->fails()){
            return response()->json(['success' => false, 'msg' => $validator->errors()]);
        }

        try {
            // fetch all transaction payment by group id 
            $paymentGroup = TransactionPaymentGroup::where('group_ref_no', $id)
                ->where('business_id', $business_id)
                ->get();

            if ($paymentGroup->isEmpty()) {
                return response()->json(['success' => false, 'msg' => 'Payment group not found']);
            }

            // check sum of amount of all transaction payment that have same group id 
            // so we will validate if sum of amount of all transaction payment that have same group id is equal to group amount
            $originalGroupAmount = $paymentGroup->sum('amount');
            
            // Get the updated amounts from request
            $updatedAmounts = $request->input('transaction_rows', []);
            $newGroupAmount = array_sum(array_column($updatedAmounts, 'paid_amount'));

            // VALIDATION 1: Group total amount cannot go beyond group total paid amount
            if ($newGroupAmount > $originalGroupAmount) {
                return response()->json(['success' => false, 'msg' => 'Total payment amount cannot exceed the original group amount of ' . number_format($originalGroupAmount, 2)]);
            }

            // VALIDATION 2: Per payment line paid amount cannot go beyond total amount
            foreach ($paymentGroup as $payment) {
                $transactionId = $payment->transaction_id;
                if (isset($updatedAmounts[$transactionId])) {
                    $newAmount = floatval($updatedAmounts[$transactionId]);
                    if ($newAmount > 0) {
                        // Get the transaction to check its total amount
                        $transaction = \App\Transaction::find($transactionId);
                        if ($transaction && $newAmount > $transaction->final_total) {
                            return response()->json([
                                'success' => false, 
                                'msg' => 'Payment amount for invoice ' . $transaction->invoice_no . ' cannot exceed the total amount of ' . number_format($transaction->final_total, 2)
                            ]);
                        }
                    }
                }
            }

            // now check if given amount is less than group amount then we will add the difference to wallet
            // here user can change the amount of transaction payment mean payment one 300 to 200 and payment two 100 to 200 but total amount will be 300
            // so we will add the difference to wallet if we have 
            $difference = $originalGroupAmount - $newGroupAmount;
            // dd($updatedAmounts);

            foreach($updatedAmounts as $updatedAmount){
                $transactionId = $updatedAmount['payment_line_id'];
                $paymentLine =TransactionPayment::where('id', $transactionId)->first();
                if($paymentLine){
                    $paymentLine->amount = $updatedAmount['paid_amount'];
                    $paymentLine->save();
                }
                $paymentGroupLine = $paymentGroup->where('id', $updatedAmount['transaction_payment_group_id'])->first();
                if($paymentGroupLine){
                    $paymentGroupLine->amount = $updatedAmount['paid_amount'];
                    $paymentGroupLine->save();
                }
            }
            
            // If there's a difference, add it to the contact's advance balance
            if ($difference > 0) {
                $contactId = $paymentGroup->first()->contact_id;
                $contact = Contact::find($contactId);
                
                if ($contact) {
                    // Add the difference to the contact's advance balance
                    $this->transactionUtil->updateContactBalance($contact, $difference, 'add');
                }
            }

            return response()->json(['success' => true, 'msg' => 'Payment group updated successfully']);
            
        } catch (\Exception $e) {
            \Log::error('Error updating payment group: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'Error updating payment group: ' . $e->getMessage()]);
        }
    }
}
