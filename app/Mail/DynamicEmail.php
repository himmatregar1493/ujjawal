<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynamicEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;  // Declare public properties for subject and message
    public $message;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function __construct($subject, $message)
    {
        $this->subject = $subject;  // Assign values to class properties
        $this->message = $message;
        
       
    }

    public function build()
        {
            $subjects = 'Default subject';
            $toEmail = 'himmat@gateway-international.in';
            $toName = 'Himmat'; // recipient's name
            
           
            return $this->from('himmat@gateway-international.in', 'Your Name') // sender's email and name
                        ->to($toEmail, $toName) // recipient's email and name
                        ->subject($subjects)
                        ->view('admin-panel.email.dynamic_email');
                        dd("sdflkjdslkf ");
        }
}