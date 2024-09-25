<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmit extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     *
     * @param array $emailData
     * @return void
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('admin-panel\emails\application_submit') // Ensure this view is created
                    ->subject($this->emailData['subject'])
                    ->with([
                        'body' => $this->emailData['body']
                    ]);

        // Add attachments if present
        if (isset($this->emailData['attachments'])) {
            foreach ($this->emailData['attachments'] as $attachment) {
                $email->attach($attachment);
            }
        }

        return $email;
    }
}
