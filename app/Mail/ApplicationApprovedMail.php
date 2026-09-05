<?php

namespace App\Mail;

use App\Models\InternshipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(InternshipApplication $application)
    {
        $this->application = $application;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengajuan Magang Disetujui - ' . $this->application->application_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-approved',
        );
    }
}
