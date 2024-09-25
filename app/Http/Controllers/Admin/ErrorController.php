<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function UserNotAuthorized(){
        return view('admin-panel.errors.user-not-authorized');
    }
}
