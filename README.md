# SimpleDB - simple Mysql abstraction class

![PHPUnit](https://img.shields.io/github/actions/workflow/status/PJanisio/simpledb/php.yml?branch=master&label=tests&logo=phpunit)

## Main goals

- preserve simple usage as in version 1.X.X
- use of PDO
- less methods better maintability
- compatibility from PHP 7.4 - 8.3

## Installation with composer

### With composer

Add the package to your composer.json file

```
"require": 
{

        "pjanisio/simpledb": "^2.4"
}
```

## Example of raw SQL statement execution

```php
// Example: Using the execute method to run a raw SQL statement
    $sql = "UPDATE `users` SET `email` = 'john.doe@example.com' WHERE `username` = 'john_doe'";
    $stmt = $db->execute($sql);

    echo "Number of affected rows: " . $db->rowCount($stmt) . "\n";

```

## Example usage (all basic methods)

```php
<?php

require 'path/to/SimpleDB.php'; // Adjust the path to where your SimpleDB.php is located

use SimpleDB\SimpleDB;

try {
    // Create a new instance of the SimpleDB class
    $db = new SimpleDB(dbName: 'my_database', username: 'username', password: 'password');

    // Check if the connection is successful
    if ($db->isConnected()) {
        echo "Connected to the database successfully.\n";
    } else {
        echo "Failed to connect to the database.\n";
    }

    // Example: Executing a query to fetch all rows from a table
    $sql = 'SELECT * FROM `users`';
    $result = $db->fetchAll($sql);

    echo "Data from users table:\n";
    foreach ($result as $row) {
        print_r($row);
    }

    // Example: Inserting a new record into the table
    $insertData = [
        'username' => 'john_doe',
        'email' => 'john.doe@example.com',
    ];

    if ($db->insert('users', $insertData)) {
        echo "Record inserted successfully.\n";
    } else {
        echo "Failed to insert record.\n";
    }

    // Example: Updating a record in the table
    $updateData = [
        'email' => 'john.new@example.com',
    ];
    $where = "username = 'john_doe'";

    if ($db->update('users', $updateData, $where)) {
        echo "Record updated successfully.\n";
    } else {
        echo "Failed to update record.\n";
    }

    // Example: Deleting a record from the table
    $where = "username = 'john_doe'";

    if ($db->delete('users', $where)) {
        echo "Record deleted successfully.\n";
    } else {
        echo "Failed to delete record.\n";
    }

    // Example: Truncating the users table
    if ($db->truncate('users')) {
        echo "Table truncated successfully.\n";
    } else {
        echo "Failed to truncate table.\n";
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}


```

----

===Changelog===
- new version *2.4.36* available (7 Jun 2024)
- new version *1.2.11* available (19 May 2024)
- new version *0.2.6* available (15 May 2024)
- new version *0.2.5* available (17 Mar 2018)
- new version *0.2.4* available (30 May 2015)
- new version *0.2.3* available (27 Mar 2012)
- new version 0.2.2a available (02 Nov 2011)
- new version 0.2.2 available (12 Nov 2010)
- new version 0.2.1 available (19 Feb 2010)
- new version 0.2.0 available (09 Jan 2010)
- new version 0.1.9 available (23 Nov 2009)
- new version 0.1.8 available (12 Nov 2009)
- repository clear and reset (10 Nov 2009)
- new version 0.1.7 available (10 Oct 2009)

----
