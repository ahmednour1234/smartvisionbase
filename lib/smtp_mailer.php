<?php
declare(strict_types=1);

/**
 * Minimal SMTP sender (PHP-only).
 * Supports SSL (465) or TLS (STARTTLS 587) and AUTH LOGIN.
 * HTML email with a plain-text fallback.
 *
 * No external libraries, no DB changes.
 */

function smtp_send(string $to, string $subject, string $html, array $cc = [], array $bcc = [], array $smtp = []): bool {
    $host = (string)($smtp['host'] ?? 'localhost');
    $port = (int)($smtp['port'] ?? 465);
    $user = (string)($smtp['user'] ?? '');
    $pass = (string)($smtp['pass'] ?? '');
    $secure = strtolower((string)($smtp['secure'] ?? 'ssl')); // ssl|tls
    $fromEmail = (string)($smtp['from_email'] ?? ($smtp['from'] ?? $user));
    $fromName  = (string)($smtp['from_name'] ?? 'Smart Vision CRM');

    $rcpts = array_values(array_filter(array_merge([$to], $cc, $bcc), fn($x) => is_string($x) && trim($x) !== ''));
    $rcpts = array_values(array_unique(array_map('trim', $rcpts)));
    if (!$rcpts) return false;

    $timeout = 20;
    $remote = ($secure === 'ssl') ? "ssl://{$host}:{$port}" : "{$host}:{$port}";
    $fp = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
    if (!$fp) return false;
    stream_set_timeout($fp, $timeout);

    $read = function() use ($fp): string {
        $data = '';
        while (!feof($fp)) {
            $line = fgets($fp, 515);
            if ($line === false) break;
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $data;
    };
    $write = function(string $cmd) use ($fp): void {
        fwrite($fp, $cmd . "
");
    };
    $expect = function(array $codes) use ($read): bool {
        $resp = $read();
        if ($resp === '') return false;
        $code = (int)substr($resp, 0, 3);
        return in_array($code, $codes, true);
    };

    if (!$expect([220])) { fclose($fp); return false; }

    $write("EHLO smartvision-crm");
    if (!$expect([250])) {
        $write("HELO smartvision-crm");
        if (!$expect([250])) { fclose($fp); return false; }
    }

    if ($secure === 'tls') {
        $write("STARTTLS");
        if (!$expect([220])) { fclose($fp); return false; }
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) { fclose($fp); return false; }
        $write("EHLO smartvision-crm");
        if (!$expect([250])) { fclose($fp); return false; }
    }

    if ($user !== '' && $pass !== '') {
        $write("AUTH LOGIN");
        if (!$expect([334])) { fclose($fp); return false; }
        $write(base64_encode($user));
        if (!$expect([334])) { fclose($fp); return false; }
        $write(base64_encode($pass));
        if (!$expect([235])) { fclose($fp); return false; }
    }

    $mailFrom = $fromEmail ?: $user;
    $write("MAIL FROM:<{$mailFrom}>");
    if (!$expect([250])) { fclose($fp); return false; }

    foreach ($rcpts as $r) {
        $write("RCPT TO:<{$r}>");
        if (!$expect([250, 251])) { fclose($fp); return false; }
    }

    $write("DATA");
    if (!$expect([354])) { fclose($fp); return false; }

    $boundary = 'b' . bin2hex(random_bytes(8));
    $plain = strip_tags($html);
    $plain = preg_replace("/\n{3,}/", "\n\n", $plain);

    $headers = [];
    $headers[] = "From: " . mb_encode_mimeheader($fromName, 'UTF-8') . " <{$mailFrom}>";
    $headers[] = "To: <{$to}>";
    if ($cc) $headers[] = "Cc: " . implode(', ', $cc);
    $headers[] = "Subject: " . mb_encode_mimeheader($subject, 'UTF-8');
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";

    $body = [];
    $body[] = "--{$boundary}";
    $body[] = "Content-Type: text/plain; charset=UTF-8";
    $body[] = "Content-Transfer-Encoding: 8bit";
    $body[] = "";
    $body[] = $plain;
    $body[] = "";
    $body[] = "--{$boundary}";
    $body[] = "Content-Type: text/html; charset=UTF-8";
    $body[] = "Content-Transfer-Encoding: 8bit";
    $body[] = "";
    $body[] = $html;
    $body[] = "";
    $body[] = "--{$boundary}--";
    $body[] = "";

    $msg = implode("
", array_merge($headers, [""], $body));
    $msg = preg_replace("/\r\n\./", "\r\n..", $msg);

    fwrite($fp, $msg . "
.
");
    if (!$expect([250])) { fclose($fp); return false; }

    $write("QUIT");
    fclose($fp);
    return true;
}
