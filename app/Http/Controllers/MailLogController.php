<?php
namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;

class MailLogController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->only(['application_id', 'email_to', 'body', 'status']);
        $emailLog = EmailLog::create($data);

        return response()->json(['message' => 'Email log created successfully.', 'emailLog' => $emailLog], 201);
    }
}
