<?php

namespace SzepeViktor\WordPress\Waf;

use Redis;

rate_limit([
    'id' => getenv('RATE_LIMIT_ID'),
    'interval' => getenv('RATE_LIMIT_INTERVAL'),
    'host' => 'localhost',
    'prefix' => 'ratelimit:waf:',
]);

/**
 * @param array{id?: string, interval?: int, host?: string, prefix?: string} $options
 */
function rate_limit(array $options): void
{
    $key = $options['id'] ?? null;
    $interval = (int)($options['interval'] ?? 0);
    $host = $options['host'] ?? 'localhost';
    $prefix = $options['prefix'] ?? 'ratelimit:';

    if (!$key || $interval <= 0) {
        return;
    }

    $key = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $key);
 
    try {
        $redis = new Redis();
        if (!$redis->connect($host, 6379)) {
            // error_log(
            return;
        }
    } catch (\RedisException $e) {
        // error_log(
        return;
    }

    $now = time();

    $lua = <<<LUA
local last = tonumber(redis.call("GET", KEYS[1]) or "0")
if tonumber(ARGV[1]) - last >= tonumber(ARGV[2]) then
    redis.call("SET", KEYS[1], ARGV[1])
    return 1
else
    return 0
end
LUA;

    try {
        $result = $redis->eval($lua, [$prefix . $key, $now, $interval], 1);
    } catch (\RedisException $e) {
        // error_log(
        return;
    }

    if ($result !== 1) {
        http_response_code(429);
        header('Retry-After: ' . $interval);
        echo 'Too Many Requests';
        exit;
    }
}
