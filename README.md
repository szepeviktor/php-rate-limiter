# PHP rate limiter

Set rate limit ID and interval in the webserver.

```apache
SetEnvIf User-Agent "GPTBot/1\.2" RATE_LIMIT_ID=GPTBot RATE_LIMIT_INTERVAL=10
```

Configure Redis conncetion in `rate-limiter.php`.

Start the rate limiter early in PHP.

```php
require __DIR__ . '/rate-limiter.php';
``
