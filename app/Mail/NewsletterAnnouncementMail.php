<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterAnnouncementMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $announcementSubject,
        public string $body,
        public string $unsubscribeUrl,
    ) {}

    public function build(): self
    {
        return $this->subject($this->announcementSubject)
            ->view('emails.newsletter-announcement');
    }
}
