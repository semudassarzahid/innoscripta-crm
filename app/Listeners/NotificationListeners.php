<?php

namespace App\Listeners;

use App\Events\NotificationEvent;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationlListener
{
    public function handle(NotificationEvent $event)
    {
        $user = $event->user;
        $emailSlug = $event->emailSlug;
        $variables = $event->variables;

        // Retrieve email subject and body from the database using the email slug
        $email = Email::where('slug', $emailSlug)->first();

        if(!$email){
            return;
        }

        if ($user) {
            $subject = $email->subject;
            $body = $email->email_body;

            // Replace variables in the email body
            foreach ($variables as $key => $value) {
                $body = str_replace("{{$key}}", $value, $body);
            }

            // Send the email
            Mail::raw($body, function ($message) use ($subject, $user) {
                $message->to($email->recipient);
                $message->subject($subject);
            });
        }

        if($user->device_token !== ''){
            // Send the push notification
            $messaging = app(FirebaseMessaging::class);
            $deviceTokens = $user->device_token;

            $subject = $email->subject;
            $body = $email->push_body;

            // Replace variables in the email body
            foreach ($variables as $key => $value) {
                $body = str_replace("{{$key}}", $value, $body);
            }

            $notification = Notification::create($subject, $body);

            $message = CloudMessage::withTarget('token', $deviceTokens)
                ->withNotification($notification)
                ->withData($data);

            $messaging->send($message);
        }


    }
}