<?php

namespace App\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentFailedToCustomer extends Mailable
{
    use SerializesModels;

    /**
     * Public Variables.
     */
    public $invoice;

    public $company;

    public $customer;

    /**
     * Create a new message instance.
     *
     * @param mixed $invoice
     * @param mixed $subject
     * @param mixed $message
     */
    public function __construct($invoice, $subject, $message)
    {
        $this->invoice = $invoice;
        $this->company = $invoice->company;
        $this->customer = $invoice->customer;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->replaceTags($this->subject);
        $mail_content = $this->replaceTags($this->message);

        return $this->subject($subject)
            ->view('emails.mails.payment_receipt_to_customer')
            ->with([
                'subject' => $subject,
                'mail_content' => $mail_content,
            ]);
    }

    /**
     * Build the message.
     *
     * @param mixed $text
     *
     * @return $this
     */
    public function replaceTags($text)
    {
        $invoice_url = route('customer_portal.invoices.details', ['customer' => $this->customer->uid, 'invoice' => $this->invoice->uid]);
        $tag_list = [
            '{company.name}' => $this->company->name,
            '{customer.display_name}' => $this->customer->display_name,
            '{customer.contact_name}' => $this->customer->contact_name,
            '{customer.email}' => $this->customer->email,
            '{customer.phone}' => $this->customer->phone,
            '{invoice.number}' => $this->invoice->invoice_number,
            '{invoice.link}' => '<a href="' . $invoice_url . '">' . $invoice_url . '</a>',
            '{invoice.date}' => $this->invoice->formatted_invoice_date,
            '{invoice.due_date}' => $this->invoice->formatted_expiry_date,
            '{invoice.reference}' => $this->invoice->reference_number,
            '{invoice.notes}' => $this->invoice->notes,
            '{invoice.sub_total}' => money($this->invoice->sub_total, $this->invoice->currency_code)->format(),
            '{invoice.total}' => money($this->invoice->total, $this->invoice->currency_code)->format(),
        ];
        foreach ($tag_list as $tag => $value) {
            $text = str_replace($tag, $value, $text);
        }

        return $text;
    }
}
