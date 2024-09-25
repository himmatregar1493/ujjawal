<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
{
    $data = [
        'username' => 'John Doe',
        'email' => 'john@example.com',
    ];

    $Testdata =  $this->subject('Welcome to Our Application')
                ->view('admin-panel.emails.dynamic_email', $data);
                return $Testdata;
                dd($Testdata);
}
}
