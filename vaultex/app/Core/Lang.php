<?php

namespace App\Core;

class Lang
{
    private static array $strings = [];
    private static string $current = 'ru';

    public static function load(string $locale): void
    {
        self::$current = $locale;
        $file = ROOT . "/lang/{$locale}.json";
        if (file_exists($file)) {
            self::$strings = json_decode(file_get_contents($file), true) ?? [];
        }
    }

    public static function get(string $key, array $replace = []): string
    {
        $str = self::$strings[$key] ?? $key;
        foreach ($replace as $k => $v) {
            $str = str_replace(':' . $k, $v, $str);
        }
        return $str;
    }

    public static function getCurrent(): string
    {
        return self::$current;
    }

    public static function set(string $locale): void
    {
        if (in_array($locale, ['ru', 'en'])) {
            self::load($locale);
        }
    }
}
