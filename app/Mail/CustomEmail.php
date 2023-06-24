<?php

namespace App\Mail;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected $slug;

    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        // Fetch the email details based on the slug
        $email = Email::where('slug', $this->slug)->firstOrFail();

        // Return the email view with the subject and body
        return $this->view('emails.custom')
                    ->subject($email->subject)
                    ->with('body', $email->body);
    }
}
