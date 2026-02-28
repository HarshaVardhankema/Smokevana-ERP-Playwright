<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentNoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $description;
    public $file_names;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $description=' ', $file_names = [])
    {
        $this->subject = $subject;
        $this->description = $description;
        $this->file_names = $file_names;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    // public function envelope()
    // {
    //     return new Envelope(
    //         subject:  $this->subject,
    //     );
    // }
    public function build()
    {
        $email = $this->subject($this->subject)
                      ->view('emails.documentNoteUserMail')
                      ->with(['description' => $this->description]);
        if (!empty($this->file_names)) {
            foreach ($this->file_names as $file_name) {
                $email->attach(public_path("uploads/media/{$file_name}"));
            }
        }
        return $email;
    }
    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    // public function content()
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    // /**
    //  * Get the attachments for the message.
    //  *
    //  * @return array
    //  */
    // public function attachments()
    // {
    //     return [];
    // }
}
