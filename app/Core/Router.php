<?php
namespace App\Core;
final class Router {
    private array $routes = [];
    public function get(string $path, callable $handler): void { $this->add('GET', $path, $handler); }
    public function post(string $path, callable $handler): void { $this->add('POST', $path, $handler); }
    public function put(string $path, callable $handler): void { $this->add('PUT', $path, $handler); }
    public function delete(string $path, callable $handler): void { $this->add('DELETE', $path, $handler); }
    private function add(string $method, string $path, callable $handler): void { $this->routes[] = [$method, '#^'.preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', rtrim($path, '/')).'/?$#', $handler]; }
    public function dispatch(string $method, string $uri): void {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        foreach ($this->routes as [$m, $pattern, $handler]) {
            if ($m === $method && preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                try { $handler($params); } catch (\Throwable $e) {
                    error_log((string)$e);
                    Response::error(getenv('APP_DEBUG') === 'true' ? $e->getMessage() : 'Internal server error', 500);
                }
                return;
            }
        }
        Response::error('API route not found', 404);
    }
}
