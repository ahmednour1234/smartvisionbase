<?php

namespace App\Support;

class UserInitials
{
    public static function generate(?string $name): string
    {
        if (empty($name)) {
            return 'U';
        }

        $name = trim($name);
        $words = preg_split('/\s+/', $name);

        if (count($words) >= 2) {
            $first = mb_substr($words[0], 0, 1, 'UTF-8');
            $second = mb_substr($words[1], 0, 1, 'UTF-8');
            return mb_strtoupper($first . $second, 'UTF-8');
        }

        $initials = mb_substr($name, 0, 2, 'UTF-8');
        return mb_strtoupper($initials, 'UTF-8');
    }
}
