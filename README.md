# SimpleDB - simple Mysql abstraction class

**! Warning - current branch has development solutions whcih can not be used  for production. Use released version until its ready!**

![PHPUnit](https://img.shields.io/github/actions/workflow/status/PJanisio/simpledb/php.yml?branch=main&label=tests&logo=phpunit)

## Development version 2.X.X

### Main goals

- usage of PDO
- less methods
- compatibility from PHP 7.4 - 8.3 ensured

## Example usage

```php
$host = 'localhost';
$port = 3306;
$dbName = 'my_database';
$username = 'username';
$password = 'password';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$db = new SimpleDB($host, $port, $dbName, $username, $password);
```

## Example Insert

```php
$db->insert('users', ['name' => 'Alice', 'status' => 'active']);
echo 'Last Insert ID: ' . $db->getLastInsertId();

```

## Example Update

```php
$db->update('users', ['status' => 'inactive'], 'name = ?', ['Alice']);

```

## Example Delete

```php
$db->delete('users', 'name = ?', ['Alice']);

```

## Example Transaction

```php
$db->beginTransaction();
try {
    $db->insert('users', ['name' => 'Bob', 'status' => 'active']);
    $db->update('users', ['status' => 'inactive'], 'name = ?', ['Bob']);
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo 'Transaction failed: ' . $e->getMessage();
}

```

## Example Connection Test

```php

if ($db->isConnected()) {
    echo 'Connected to the database';
} else {
    echo 'Not connected to the database';
}

```
