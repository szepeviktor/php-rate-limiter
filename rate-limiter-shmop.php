<?php

namespace SzepeViktor\WordPress\Waf;

rate_limit([
    'id' => getenv('RATE_LIMIT_ID'),
    'interval' => getenv('RATE_LIMIT_INTERVAL'),
    'prefix' => 'ratelimit:waf:',
]);

/**
 * @param array{id?: string|false, interval?: string|false, prefix?: string} $options
 */
function rate_limit(array $options): void
{
    $key = (string)($options['id'] ?? false);
    $interval = (int)($options['interval'] ?? false);
    $prefix = $options['prefix'] ?? 'ratelimit:';

    if ($key === '' || $interval <= 0) {
        return;
    }

    $now = time();
    $shm = @shmop_open(crc32($prefix . $key), 'c', 0600, 4);

    if ($shm === false) {
        // error_log(
        return;
    }

    $data = shmop_read($shm, 0, 4);

    if (strlen($data) !== 4) {
        // error_log(
        return;
    }

    $last = unpack('N', $data);

    if ($last === false) {
        // error_log(
        return;
    }

    $is_request_allowed = $now - $last[1] >= $interval;

    if ($is_request_allowed) {
        shmop_write($shm, pack('N', $now), 0);
    }

    shmop_close($shm);

    if (!$is_request_allowed) {
        http_response_code(429);
        header('Retry-After: ' . $interval);
        echo 'Too Many Requests';
        exit;
    }
}
