<?php

namespace Modules\Auth\F31_ForgotPassword\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\F31_ForgotPassword\Mail\ResetPasswordMail;

class SendResetPasswordEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public string $email,
        public string $resetUrl
    ) {}

    public function handle(): void
    {
        Mail::to($this->email)->send(new ResetPasswordMail($this->resetUrl));
    }
}