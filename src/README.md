# SimpleDB - simple Mysql abstraction class

## Development version 2.X.X

## Example

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

