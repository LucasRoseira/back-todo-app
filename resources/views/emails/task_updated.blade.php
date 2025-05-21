<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <style>
        .container {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .content {
            margin-top: 10px;
            color: #444;
        }

        .label {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="title">Task Updated</div>
        <div class="content">
            <p><span class="label">Title:</span> {{ $task->title }}</p>
            <p><span class="label">Status:</span> {{ ucfirst($task->status) }}</p>
            <p><span class="label">Responsible:</span> {{ $task->responsible ?? 'Not specified' }}</p>
            <p><span class="label">Due Date:</span> {{ $task->due_date ?? 'No due date' }}</p>
            <p><span class="label">Description:</span><br> {{ $task->description ?? 'No description' }}</p>
        </div>
    </div>
</body>

</html>