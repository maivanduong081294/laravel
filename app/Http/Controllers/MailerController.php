<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail; 

class MailerController extends Controller
{
    //

    public function send($email,$subject,$view,$data=[]) {
        $params = [
            'email' => $email,
            'subject' => $subject,
        ];
        Mail::send($view, $data, function($message) use($params){
            $message->to($params['email']);
            $message->subject($params['subject']);
        });
    }
}
