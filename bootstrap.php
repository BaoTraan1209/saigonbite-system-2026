<?php
// Load simple KEY=VALUE environment file for local development.
$envFile = __DIR__.'/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim(trim($value), "\"'");
        if (getenv($key) === false) putenv($key.'='.$value);
    }
}
spl_autoload_register(function(string $class): void {
    $prefix='App\\'; if(!str_starts_with($class,$prefix)) return;
    $relative=substr($class,strlen($prefix));$file=__DIR__.'/app/'.str_replace('\\','/',$relative).'.php';
    if(file_exists($file)) require $file;
});
date_default_timezone_set('Asia/Ho_Chi_Minh');
