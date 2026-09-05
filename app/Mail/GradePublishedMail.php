<?php

namespace App\Mail;

use App\Models\InternshipAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GradePublishedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $assignment;

    public function __construct(InternshipAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sertifikat dan Nilai Magang Anda Telah Terbit!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.grade-published',
        );
    }
}
