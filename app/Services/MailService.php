<?php

namespace App\Services;

use App\Mail\TaskDeletedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Task;

class MailService
{
    public function sendTaskDeletedEmail(string $to, string $taskTitle): void
    {
        Mail::to($to)->send(new TaskDeletedMail($taskTitle));
    }

    public function sendTaskUpdatedNotification(Task $task)
    {
        $recipient = $task->responsible_email ?? 'default@example.com';

        Mail::send('emails.task_updated', ['task' => $task], function ($message) use ($recipient, $task) {
            $message->to($recipient)
                ->subject("Atualização na Tarefa: {$task->title}");
        });
    }
}
