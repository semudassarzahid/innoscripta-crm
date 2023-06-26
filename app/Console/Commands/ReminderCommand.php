<?php

namespace App\Console\Commands;

use App\Events\NotificationEvent;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\User;

protected $signature = 'send:reminder';

protected $description = 'Send reminder for due reminders';

class SendReminderEmails extends Command
{
    public function handle()
    {
        $now = Carbon::now();

        $reminders = Reminder::where('reminder_time', '<=', $now)->get();

        foreach ($reminders as $reminder) {
            event(new NotificationEvent(User::find($reminder->user_id),'ReminderNotification', ['type' => $reminder->type]));
        }

        $this->info('Reminder sent successfully.');
    }
}