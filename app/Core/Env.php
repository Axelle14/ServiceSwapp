<?php
declare(strict_types=1);

namespace App\Core;

class Env
{
    private static bool $loaded = false;

    /**
     * Load environment variables.
     *
     * Local:
     *   Loads from the .env file.
     *
     * Azure:
     *   Uses App Service environment variables if no .env exists.
     */
    public static function load(string $path): void
    {
        if (self::$loaded) {
            return;
        }

        // If no .env file exists, check whether we're running with
        // environment variables already provided by Azure App Service.
        if (!file_exists($path)) {

            if (
                getenv('APP_ENV') !== false ||
                isset($_ENV['APP_ENV']) ||
                isset($_SERVER['APP_ENV'])
            ) {
                self::$loaded = true;
                return;
            }

            throw new \RuntimeException(".env file not found at: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {

            $line = trim($line);

            // Ignore blank lines
            if ($line === '') {
                continue;
            }

            // Ignore comments
            if (str_starts_with($line, '#')) {
                continue;
            }

            // Ignore invalid lines
            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value);

            // Remove surrounding quotes
            $value = trim($value, "\"'");

            // Don't overwrite existing environment variables
            if (getenv($key) === false) {
                putenv("$key=$value");
            }

            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }

        self::$loaded = true;
    }

    /**
     * Get an environment variable.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);

        if ($value !== false) {
            return $value;
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return $default;
    }

    /**
     * Get a required environment variable.
     */
    public static function required(string $key): string
    {
        $value = self::get($key);

        if ($value === null || $value === '') {
            throw new \RuntimeException("Required environment variable '{$key}' is not set.");
        }

        return (string)$value;
    }
}