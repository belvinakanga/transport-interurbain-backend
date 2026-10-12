<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodeReinitialisationMotDePasse extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nomUtilisateur,
        public string $code,
        public int $minutesValidite,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre code de réinitialisation Tokende',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.code-reinitialisation',
            with: [
                'nomUtilisateur' => $this->nomUtilisateur,
                'code' => $this->code,
                'minutesValidite' => $this->minutesValidite,
            ],
        );
    }
}
