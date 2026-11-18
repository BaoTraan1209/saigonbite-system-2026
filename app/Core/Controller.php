<?php
namespace App\Core;
abstract class Controller {
    protected function input(): array { return Request::json(); }
    protected function ok(mixed $data, int $status = 200): never { Response::json($data, $status); }
    protected function fail(string $message, int $status = 400, array $errors = []): never { Response::error($message, $status, $errors); }
    // Demo-only role check. Replace X-User-Role with real session/JWT authentication before production.
    protected function requireRole(array $roles): void {
        $role = $_SERVER['HTTP_X_USER_ROLE'] ?? 'Admin';
        if (!in_array($role, $roles, true)) $this->fail('You do not have permission for this action.', 403);
    }
}
