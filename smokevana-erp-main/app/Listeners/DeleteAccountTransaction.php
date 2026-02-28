<?php

namespace App\Listeners;

use App\AccountTransaction;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;

class DeleteAccountTransaction
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
    public function handle($event)
    {
        //Add contact advance if exists
        if ($event->transactionPayment->method == 'advance') {
            // Check if this is a return transaction
            $isReturn = false;
            
            // Check if the payment has is_return flag
            if (isset($event->transactionPayment->is_return) && $event->transactionPayment->is_return == 1) {
                $isReturn = true;
            }
            
            // Check if this is a sell return transaction by looking at the related transaction
            if ($event->transactionPayment->transaction_id) {
                $transaction = \App\Transaction::find($event->transactionPayment->transaction_id);
                if ($transaction && $transaction->type == 'sell_return') {
                    $isReturn = true;
                }
            }
            
            // For returns with advance payment, deduct from wallet instead of adding
            // (since the original payment was added to wallet for returns)
            if ($isReturn) {
                $this->transactionUtil->updateContactBalance($event->transactionPayment->payment_for, $event->transactionPayment->amount, 'deduct');
            } else {
                // Normal advance payment - add back to wallet when deleted
                $this->transactionUtil->updateContactBalance($event->transactionPayment->payment_for, $event->transactionPayment->amount);
            }
        }

        if (! $this->moduleUtil->isModuleEnabled('account')) {
            return true;
        }

        AccountTransaction::where('account_id', $event->transactionPayment->account_id)
                        ->where('transaction_payment_id', $event->transactionPayment->id)
                        ->delete();
    }
}
