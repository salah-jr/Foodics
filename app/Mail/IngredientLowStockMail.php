<?php

namespace App\Mail;

use App\Models\Ingredient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class IngredientLowStockMail extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(public Ingredient $ingredient,){}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('system@example.com', 'Store'),
            to: [
                new Address('merchant@example.com', 'Merchant Name'),
            ],
            subject: $this->ingredient->name . ' Low Stock',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
