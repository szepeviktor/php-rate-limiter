# PHP rate limiter

## Features

- As simple as it can be
- Atomic: no race condition
- Helps intelligent bots with `Retry-After` HTTP header

## Requirements

- PHP
- `redis` extension
- Redis server

## Usage

Set rate limit ID and interval (in seconds) in the webserver.

```apache
SetEnvIf User-Agent "GPTBot/1\.2" RATE_LIMIT_ID=GPTBot RATE_LIMIT_INTERVAL=10
```

Configure Redis connection in `rate-limiter.php`.

Start the rate limiter early in PHP.

```php
require __DIR__ . '/rate-limiter.php';
```

## Alternative backend

Shared Memory (uses `shmop` extension): `rate-limiter-shmop.php`

List shared memory segments as root: `ipcs -m`
