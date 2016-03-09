# MyVendor.Task

This is a BEAR.Sunday tutorial framework application. See http://bearsunday.github.io/manuals/1.0/ja/quick-api.html.

## Installation

```
composer install
composer setup
```

## Configuration

```
cp .env.dist .env
```

`.env` example

```
DB_DSN=mysql:host=localhost;dbname=task
DB_USER=root
DB_PASS=passowrd
DB_READ=slave1.example.com,slave2.example.com
```


## Tests

```
composer test
```

## Author

 * Akihito Koriyama
