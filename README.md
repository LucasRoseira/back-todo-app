# Todo API

Laravel JSON API for categories and tasks. It is the backend for a Nuxt client running on `http://localhost:3000` (Vite's default port is also allowed).

Interactive OpenAPI docs: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation) after the app is running.

## Setup

Requirements: PHP 8.1+, Composer, and the SQLite extension (or MySQL if you switch drivers).

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve

Leave `DB_DATABASE` unset for SQLite. Laravel then uses `database/database.sqlite`. A relative path in `DB_DATABASE` is only resolved when that file already exists.
```

The API is then at `http://localhost:8000`. Seed data is three categories (Work, Personal, Study), eight tasks that cover pending, in progress, completed, due today, and overdue, plus a demo user.

Demo user (Sanctum only, not required for tasks or categories):

- email: `demo@example.com`
- password: `password`

Create a token when you want to call `GET /api/user`:

```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'demo@example.com')->first();
$user->createToken('nuxt')->plainTextToken;
```

Send it as `Authorization: Bearer {token}`.

### MySQL

Comment the SQLite lines in `.env` and set `DB_CONNECTION=mysql` with your host, database, username, and password. Then run `php artisan migrate --seed` again. Laravel Sail is in `require-dev` if you want containers (`php artisan sail:install` after `composer install`).

### Tests

```bash
php artisan test
```

PHPUnit uses an in-memory SQLite database, so it does not touch `database/database.sqlite`.

## Architecture

| Layer | Responsibility |
| --- | --- |
| Controllers | HTTP only: form request in, JSON resource out |
| Form requests | Validation and error messages |
| Services | Status transitions, history rows, mail side effects |
| Repositories | Eloquent queries, filters, eager loads |
| API resources | One response shape for each model |
| Models | Relations, casts, fillable attributes |

Interfaces for the task and category services and repositories are bound in `App\Providers\AppServiceProvider`. Controllers depend on those interfaces.

Routes live in `routes/api.php` under the `api` prefix, with `throttle:api` (60 requests per minute) and route-model binding. `LogRequestMiddleware` records method, URL, and payload (passwords excluded).

## Assumptions and trade-offs

- **Public task and category routes.** The Nuxt client calls this API with JSON and no cookie session. Putting Sanctum's `EnsureFrontendRequestsAreStateful` middleware on the API group would treat `localhost:3000` as a stateful SPA and reject writes that do not send `X-XSRF-TOKEN`. It stays off. `GET /api/user` still requires a Sanctum bearer token.
- **CORS.** `http://localhost:3000` and `http://127.0.0.1:3000` are allowed explicitly, and any `localhost` or `127.0.0.1` port matches as well, with credentials enabled so a later cookie-based client can work. `*` is not used, because credentialed CORS forbids it.
- **Paginator shape is unchanged.** Lists return Laravel's paginator document (`current_page`, `data`, `last_page`, `per_page`, `total`, plus the usual link fields). Single records are the object itself, not wrapped in `data`. API resources still transform every row; `JsonResource::withoutWrapping()` keeps that contract.
- **Deleting a category keeps its tasks.** `category_id` is set to null (`nullOnDelete`). Tasks are not cascade-deleted.
- **Overdue means due before today and not completed.** A task due today stays in `filter_type=today` for the whole day. `filter_type=today` includes every status.
- **Priority sort is high, then medium, then low, then due date.** Alphabetical `orderBy('priority')` put "low" before "medium".
- **Mail is best-effort.** Status or responsible changes notify `responsible_email` when that column is set. Failures are logged and do not roll back the write. The default mailer is `log`, so local setup does not need SMTP. There is no queue worker.
- **SQLite is the default.** MySQL `enum` columns are `varchar` on SQLite; form requests are the check that status and priority stay in the allowed set. Indexes on `status`, `priority`, `due_date`, and `(status, due_date)` support the list filters.
- **Lazy loading is disabled outside production.** A missing `with()` throws instead of hiding an N+1 query. List and show paths eager-load `category`. Category lists use `withCount('tasks')` and do not embed task rows.
- **No login endpoint.** The assessment front already owns categories and tasks. Auth is limited to the existing Sanctum `/api/user` probe plus a seeded user.

## Completed features

- Category CRUD with unique names, hex colors, and `tasks_count`
- Task CRUD with category, priority, status, due date, and responsible name/email
- Filters: title, description, status, priority, due date on or after, category, responsible name, and `filter_type` of `today`, `pending`, or `overdue`
- Pagination via `per_page` (1–100, default 10)
- Status history on create and whenever status changes (`GET /api/tasks/{task}/history`)
- `completed_at` set when status becomes `completed`, cleared when it leaves `completed`
- JSON error bodies for validation (422), auth (401), not found (404), and method (405)
- Seeders and OpenAPI annotations

## Limitations

- Task and category routes are not authenticated or owned by a user.
- `responsible_name` is a label, not a user foreign key. `responsible_email` is optional and only used for notifications.
- History records status changes only, not other field edits.
- List search is `LIKE %term%` and is not a full-text index.
- Mail is synchronous and skipped when `responsible_email` is empty.
- The welcome page at `/` is still the Laravel default. This project is the API.

## API

Send `Accept: application/json` and `Content-Type: application/json`. Base URL examples use `http://localhost:8000`.

### Categories

`GET /api/categories?per_page=10&name=Work`

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Work",
      "color": "#3b82f6",
      "tasks_count": 4,
      "created_at": "2026-10-02T12:00:00.000000Z",
      "updated_at": "2026-10-02T12:00:00.000000Z"
    }
  ],
  "last_page": 1,
  "per_page": 10,
  "total": 3
}
```

`POST /api/categories`

```json
{ "name": "Errands", "color": "#10b981" }
```

`201` returns the category object (same fields as a list row). `name` is required and unique. `color` is optional `#RGB` or `#RRGGBB`.

`GET /api/categories/{id}` returns one category.

`PUT /api/categories/{id}` accepts the same fields. Only fields you send are required to be present (`name` is optional on update).

`DELETE /api/categories/{id}` returns `204` and nulls `category_id` on that category's tasks.

Validation error (`422`):

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["A category with this name already exists."]
  }
}
```

### Tasks

`GET /api/tasks`

Query parameters:

| Name | Meaning |
| --- | --- |
| `per_page` | Page size, 1–100, default 10 |
| `filter_type` | `today`, `pending`, or `overdue` |
| `title`, `description`, `responsible_name` | Partial match |
| `status` | `pending`, `in_progress`, `completed` |
| `priority` | `low`, `medium`, `high` |
| `due_date` | Due on or after this date (`YYYY-MM-DD`) |
| `category_id` | Exact category |

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "Finish project proposal",
      "description": "Draft and submit the project proposal document.",
      "status": "pending",
      "priority": "high",
      "due_date": "2026-10-09",
      "completed_at": null,
      "category_id": 1,
      "category_name": "Work",
      "category": {
        "id": 1,
        "name": "Work",
        "color": "#3b82f6",
        "created_at": "2026-10-02T12:00:00.000000Z",
        "updated_at": "2026-10-02T12:00:00.000000Z"
      },
      "responsible_name": "Noah",
      "responsible_email": "noah@example.com",
      "created_at": "2026-10-02T12:00:00.000000Z",
      "updated_at": "2026-10-02T12:00:00.000000Z"
    }
  ],
  "last_page": 1,
  "per_page": 10,
  "total": 8
}
```

`POST /api/tasks`

```json
{
  "title": "Write tests",
  "description": "Cover filters and history",
  "status": "pending",
  "priority": "high",
  "due_date": "2026-10-10",
  "category_id": 1,
  "responsible_name": "Noah",
  "responsible_email": "noah@example.com"
}
```

`title` is required. Omitted `status` becomes `pending` and omitted `priority` becomes `medium`. `due_date` on create cannot be before today. `201` returns the task. A history row is stored for the initial status.

`GET /api/tasks/{id}` returns one task.

`PUT /api/tasks/{id}` (and `PATCH`) updates any subset of those fields plus `completed_at`. Setting `status` to `completed` fills `completed_at` when you do not send one. Any other status clears `completed_at`. A status change appends history and, when `responsible_email` is present, sends a notification.

`DELETE /api/tasks/{id}`

```json
{ "message": "Task deleted successfully" }
```

History rows are removed with the task.

`GET /api/tasks/{id}/history` returns an array, newest first:

```json
[
  {
    "id": 2,
    "task_id": 1,
    "status": "in_progress",
    "changed_at": "2026-10-02T12:00:00.000000Z",
    "created_at": "2026-10-02T12:00:00.000000Z",
    "updated_at": "2026-10-02T12:00:00.000000Z"
  }
]
```

Missing records:

```json
{ "message": "Resource not found." }
```

### Current user

`GET /api/user` with `Authorization: Bearer {token}` returns the Sanctum user. Without a token the response is `401`:

```json
{ "message": "Unauthenticated." }
```
