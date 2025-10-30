# PHP rate limiter

## Features

- As simple as it can be
- Atomic: no race condition
- Helps intelligent bots with `Retry-After` HTTP header

## Requirements

- PHP 7 or 8
- `redis` extension
- Redis server

## Installation

Download `rate-limiter.php`.

## Configuration

Set rate limit ID and interval (in seconds) in the webserver.

```apache
SetEnvIf User-Agent "^$" \
    RATE_LIMIT_ID=No-UA RATE_LIMIT_INTERVAL=10
SetEnvIfExpr "-R '47.74.0.0/15' || -R '47.76.0.0/14' || -R '47.80.0.0/13'" \
    RATE_LIMIT_ID=Alibaba-bot RATE_LIMIT_INTERVAL=10

SetEnvIf User-Agent "Amazonbot/\d+\.\d+" \
    RATE_LIMIT_ID=Amazon-bot RATE_LIMIT_INTERVAL=10
SetEnvIf User-Agent "ClaudeBot/\d+\.\d+" \
    RATE_LIMIT_ID=Claude-bot RATE_LIMIT_INTERVAL=10
SetEnvIf User-Agent "facebookexternalhit/\d+\.\d+|meta-externalagent/\d+\.\d+" \
    RATE_LIMIT_ID=Facebook-external RATE_LIMIT_INTERVAL=10
SetEnvIf User-Agent "GPTBot/\d+\.\d+" \
    RATE_LIMIT_ID=GPT-bot RATE_LIMIT_INTERVAL=10
```

Configure Redis connection in `rate-limiter.php`.

## Usage

Start the rate limiter early in PHP.

```php
require __DIR__ . '/rate-limiter.php';
```

## Alternative drivers

Shared Memory (uses `shmop` extension): `rate-limiter-shmop.php`

List shared memory segments as root: `ipcs -m`
