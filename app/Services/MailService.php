<?php
namespace App\Services;

use App\Mail\TaskDeletedMail;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendTaskDeletedEmail(string $to, string $taskTitle): void
    {
        Mail::to($to)->send(new TaskDeletedMail($taskTitle));
    }
}
