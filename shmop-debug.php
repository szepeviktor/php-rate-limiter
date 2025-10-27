<?php

/**
 * shmop debugger
 *
 * Usage: https://example.com/shmop-debug.php?id=RATE_LIMIT_ID
 *
 * @author Viktor Szépe <viktor@szepe.net>
 * @link https://github.com/szepeviktor/php-rate-limiter
 */

$id = $_GET['id'] ?? '';
$prefix = 'ratelimit:waf:';

if ($id === '') {
    http_response_code(400);
    echo 'Missing RATE_LIMIT_ID parameter.';
    exit;
}

$shm = @shmop_open(crc32($prefix . $id), 'a', 0, 0);
if (!$shm) {
    echo 'No shared memory segment found for ID: ' . htmlspecialchars($id);
    exit;
}

$rate_limit = shmop_read($shm, 0, 4);
$last = unpack('N', $rate_limit)[1];

echo "<pre>";
echo "RATE_LIMIT_ID: " . htmlspecialchars($id) . "\n";
echo "Last request: {$last} (" . date('Y-m-d H:i:s', $last) . ")\n";
echo "</pre>";

shmop_close($shm);
