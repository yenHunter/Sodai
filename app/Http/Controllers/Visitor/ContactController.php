<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Visitor\ContactRequest;
use App\Mail\Admin\ContactMessageMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('visitor.pages.contact');
    }

    public function store(ContactRequest $request)
    {
        $data = $request->validated();

        $recipient = setting('notification', 'admin_alert_email')
            ?: setting('company', 'email')
            ?: config('mail.from.address');

        if (empty($recipient)) {
            Log::error('Contact form: no admin recipient is configured.');

            return redirect()
                ->route('visitor.contact')
                ->withInput()
                ->with('error', 'Sorry, we cannot accept messages right now. Please try again later.');
        }

        Mail::to($recipient)->send(new ContactMessageMail(
            senderName: trim($data['first_name'].' '.$data['last_name']),
            senderEmail: $data['email'],
            senderPhone: $data['phone'],
            messageBody: $data['message'],
        ));

        return redirect()
            ->route('visitor.contact')
            ->with('success', 'Thanks for reaching out! We will get back to you shortly.');
    }
}
