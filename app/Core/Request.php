<?php
namespace App\Core;
final class Request {
    public static function json(): array {
        $raw = file_get_contents('php://input');
        if (!$raw) return $_POST ?: [];
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
    public static function query(string $key, mixed $default = null): mixed { return $_GET[$key] ?? $default; }
}
