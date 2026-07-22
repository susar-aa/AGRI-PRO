<?php
namespace Core;

class Helper {
    public static function baseUrl(string $path = ''): string {
        $config = require __DIR__ . '/../config/app.php';
        $base = rtrim($config['base_url'], '/');
        $path = ltrim($path, '/');
        return $path ? "{$base}/{$path}" : $base;
    }

    public static function assetUrl(string $path = ''): string {
        return self::baseUrl('assets/' . ltrim($path, '/'));
    }

    public static function redirect(string $path): void {
        $url = (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) 
            ? $path 
            : self::baseUrl($path);
        header("Location: {$url}");
        exit;
    }

    public static function sanitize(?string $string): string {
        if ($string === null) return '';
        return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
    }

    public static function formatCurrency(float $amount, bool $showSymbol = true): string {
        $formatted = number_format($amount, 2, '.', ',');
        return $showSymbol ? "LKR {$formatted}" : $formatted;
    }

    public static function formatDate(?string $dateStr, string $format = 'Y-m-d'): string {
        if (!$dateStr) return '';
        $timestamp = strtotime($dateStr);
        return $timestamp ? date($format, $timestamp) : $dateStr;
    }
}
