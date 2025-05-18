<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $taskTitle;

    public function __construct(string $taskTitle)
    {
        $this->taskTitle = $taskTitle;
    }

    public function build()
    {
        return $this->subject('Task Deleted')
                    ->view('emails.task-deleted');
    }
}
