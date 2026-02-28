<?php

namespace App\Listeners;

use App\AccountTransaction;
use App\Events\TransactionPaymentAdded;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;

class AddAccountTransaction
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
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TransactionPaymentAdded $event)
    {
        //echo "<pre>";print_r($event->transactionPayment->toArray());exit;
        if ($event->transactionPayment->method == 'advance') {
            // Check if this is a return transaction
            $isReturn = false;
            
            // Check if this is a sell return transaction
            if (isset($event->formInput['transaction_type']) && $event->formInput['transaction_type'] == 'sell_return') {
                $isReturn = true;
            }
            
            // Check if this is a return payment (negative amount or return flag)
            if (isset($event->formInput['is_return']) && $event->formInput['is_return'] == 1) {
                $isReturn = true;
            }
            
            // For returns with advance payment, add to wallet instead of deducting
            if ($isReturn) {
                $this->transactionUtil->updateContactBalance($event->transactionPayment->payment_for, $event->transactionPayment->amount, 'add');
            } else {
                // Normal advance payment - deduct from wallet
                $this->transactionUtil->updateContactBalance($event->transactionPayment->payment_for, $event->transactionPayment->amount, 'deduct');
            }
        }

        if (! $this->moduleUtil->isModuleEnabled('account', $event->transactionPayment->business_id)) {
            return true;
        }

        // //Create new account transaction
        if (! empty($event->formInput['account_id']) && $event->transactionPayment->method != 'advance') {
            $type = ! empty($event->transactionPayment->payment_type) ? $event->transactionPayment->payment_type : AccountTransaction::getAccountTransactionType($event->formInput['transaction_type']);
            $account_transaction_data = [
                'amount' => $event->formInput['amount'],
                'account_id' => $event->formInput['account_id'],
                'type' => $type,
                'operation_date' => $event->transactionPayment->paid_on,
                'created_by' => $event->transactionPayment->created_by,
                'transaction_id' => $event->transactionPayment->transaction_id,
                'transaction_payment_id' => $event->transactionPayment->id,
            ];

            //If change return then set type as debit
            if ($event->formInput['transaction_type'] == 'sell' && isset($event->formInput['is_return']) && $event->formInput['is_return'] == 1) {
                $account_transaction_data['type'] = 'debit';
            }

            if ($event->formInput['transaction_type'] == 'hms_booking' && isset($event->formInput['is_return']) && $event->formInput['is_return'] == 1) {
                $account_transaction_data['type'] = 'debit';
            }

            AccountTransaction::createAccountTransaction($account_transaction_data);
        }
    }
}
