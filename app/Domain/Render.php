<?php

namespace App\Domain;

use function Termwind\render;

class Render {
    public static function green(string $text): void
    {
        render(<<<HTML
                <div class="px-1 font-bold bg-green-500">{$text}</div>
            HTML);
    }

    public static function text(string $text): void
    {
        render(<<<HTML
                <div class="px-1">{$text}</div>
            HTML);
    }

    public static function list(array $items): void {
        $longest_key_length = 0;

        if(count($items) === 0) {
            return;
        }

        foreach ($items as $key => $value) {
            $longest_key_length = max($longest_key_length, strlen($key));

        }

        $longest_key_length += 3;

        foreach ($items as $key => $value) {
            render(<<<HTML
                <div>
                    <div class="px-1 font-bold text-right bg-green-500 w-{$longest_key_length}">{$key}:</div>
                    <em class="ml-1">{$value}</em>
                </div>
            HTML);
        }
    }
}
