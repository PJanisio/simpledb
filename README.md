# SimpleDB - simple Mysql abstraction class

## Development version 2.X.X

### Main goals:

- usage of PDO
- less methods
- compatibility from PHP 7.4 - 8.3 ensured

## Example usage

```php
try {
    // Create an instance of SimpleDB
    $db = new SimpleDB($dsn, $username, $password, $options);

    // Example usage
    $stmt = $db->execute('SELECT * FROM users');
    while ($row = $stmt->fetch()) {
        print_r($row);
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

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