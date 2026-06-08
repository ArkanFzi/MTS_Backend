<?php

namespace Modules\Auth\F31_ForgotPassword\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $resetUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reset Password - ' . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.reset-password',
            with: ['resetUrl' => $this->resetUrl]
        );
    }
}