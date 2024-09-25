<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
class EmailController extends Controller
{
    public static function extractEmailDetails($sentMessage)
    {   

       
        $emailDetails = [
            'body' => '',
            'subject' => "",
            'email_to' => "",
            'attachments' => []
        ];

        $emailDetails['subject'] = $sentMessage->getOriginalMessage()->getHeaders()->get('subject')->getValue();
        $emailDetails['email_to'] = $sentMessage->getOriginalMessage()->getHeaders()->get('to')->getAddresses()[0]->getAddress();
        $emailDetails['body'] = $sentMessage->getOriginalMessage()->getHtmlBody();
        
        if (method_exists($sentMessage, 'attachments')) {
            $attachments = $sentMessage->attachments();
            foreach ($attachments as $attachment) {
                $emailDetails['attachments'][] = [
                    'name' => $attachment->getName(),
                    'path' => $attachment->getPathname()
                ];
            }
        }
        return $emailDetails;
    }

    public static function storeEmailLog($applicationId, $email, $body, $subject, $status,$attachments)
    {

        EmailLog::create([
            'application_id' => $applicationId,
            'email_to' => $email,
            'body' => $body,
            'subject' => $subject,
            'attachments' => json_encode([]), // Store attachments info as JSON if applicable
            'status' => $status
        ]);
        return true;
    }
}
