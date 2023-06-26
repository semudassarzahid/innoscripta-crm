<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent
{
    use Dispatchable, SerializesModels;

    public $user;
    public $emailSlug;
    public $variables;

    public function __construct($user, $emailSlug, $variables)
    {
        $this->user = $user;
        $this->emailSlug = $emailSlug;
        $this->variables = $variables;
    }
}