<?php

namespace App\Services;

use App\Mail\TaskDeletedMail;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailService
{
    /**
     * Notifications are best-effort. A mail outage must not fail the API write
     * that already succeeded. Local setups use the log mailer (see .env.example).
     */
    public function notifyTaskUpdated(Task $task): void
    {
        if (! $task->responsible_email) {
            Log::info('Skipped task update email because the task has no responsible_email.', [
                'task_id' => $task->id,
            ]);

            return;
        }

        try {
            Mail::send('emails.task_updated', ['task' => $task], function ($message) use ($task) {
                $message->to($task->responsible_email)
                    ->subject("Update in the task: {$task->title}");
            });
        } catch (Throwable $exception) {
            Log::warning('Task update email failed.', [
                'task_id' => $task->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function notifyTaskDeleted(Task $task): void
    {
        if (! $task->responsible_email) {
            Log::info('Skipped task deleted email because the task has no responsible_email.', [
                'task_id' => $task->id,
            ]);

            return;
        }

        try {
            Mail::to($task->responsible_email)->send(new TaskDeletedMail($task->title));
        } catch (Throwable $exception) {
            Log::warning('Task deleted email failed.', [
                'task_id' => $task->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
