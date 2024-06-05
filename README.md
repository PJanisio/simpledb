# SimpleDB - simple Mysql abstraction class

**! Warning - current branch has development solutions which can not be used  for production. Use released version until its ready!**

![PHPUnit](https://img.shields.io/github/actions/workflow/status/PJanisio/simpledb/php.yml?branch=master&label=tests&logo=phpunit)

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

----

===Changelog===

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
