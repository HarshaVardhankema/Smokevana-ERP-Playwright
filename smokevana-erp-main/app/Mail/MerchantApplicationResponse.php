<?php

namespace App\Mail;

use App\Models\MerchantApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MerchantApplicationResponse extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(MerchantApplication $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        $subject = $this->application->status === 'approved' 
            ? 'Your Merchant Application Has Been Approved'
            : 'Your Merchant Application Status Update';

        return $this->subject($subject)
                    ->view('emails.merchant_application_response')
                    ->with([
                        'application' => $this->application
                    ]);
    }
} 