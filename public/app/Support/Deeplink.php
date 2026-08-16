<?php
// app/Support/Deeplink.php
namespace App\Support;

class Deeplink
{
    // لو عندك سكيما مختلفة غيّرها هنا
    public static function for(string $subjectMorph, int $id, ?string $slug = null): array
    {
        // subjectMorph: booking|order|chat_room ...
        $slugPart = $slug ? "/{$slug}" : '';
        $deeplink = "app://{$subjectMorph}{$slugPart}?id={$id}";

        // لو عندك route ويب
        $web = url("/{$subjectMorph}{$slugPart}".($slug ? '' : "/{$id}"));

        return [$deeplink, $web];
    }
}
