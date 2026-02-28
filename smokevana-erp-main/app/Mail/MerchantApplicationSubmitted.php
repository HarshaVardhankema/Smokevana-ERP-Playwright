<?php

namespace App\Mail;

use App\Models\MerchantApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MerchantApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(MerchantApplication $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject('New Merchant Application Request')
                    ->view('emails.merchant_application_submitted')
                    ->with([
                        'application' => $this->application
                    ]);
    }

    public function attachments()
    {
        return [
            Attachment::fromStorageDisk('public', 'uploads/merchant_documents/' . $this->application->voided_check_path),
            Attachment::fromStorageDisk('public', 'uploads/merchant_documents/' . $this->application->driver_license_path),
            $this->application->processing_statements_path ? Attachment::fromStorageDisk('public', 'uploads/merchant_documents/' . $this->application->processing_statements_path) : null,
        ];
    }
} 