<?php

use PHPUnit\Framework\TestCase;
use SimpleDB\SimpleDB;

class SimpleDBTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dbName = getenv('DB_NAME');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASS');

        $this->db = new SimpleDB($host, $port, $dbName, $username, $password);

        // Initialize the database
        $this->initializeTestData();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
    }

    private function initializeTestData(): void
    {

        // Create the users table
        $this->db->execute('CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            email VARCHAR(255)
        )');

        // Insert initial data into the users table
        $this->db->execute('INSERT INTO users (name, email) VALUES ("Alice", "alice@example.com")');
        $this->db->execute('INSERT INTO users (name, email) VALUES ("Bob", "bob@example.com")');
    }


    private function cleanupTestData(): void
    {
        $this->db->execute('DROP TABLE users');
    }

    public function testFetchAll(): void
    {
        $result = $this->db->fetchAll('SELECT * FROM users');
        $this->assertCount(2, $result);
    }

    public function testFetch(): void
    {
        $result = $this->db->fetch('SELECT * FROM users WHERE id = ?', [1]);
        $this->assertNotNull($result);
        $this->assertEquals('Alice', $result['name']);
    }

    public function testExecute()
    {
        $stmt = $this->db->execute('CREATE TABLE test_table (id INT)');
        $this->assertInstanceOf('PDOStatement', $stmt);
    }

    public function testRowCount()
    {
        $stmt = $this->db->query('SELECT * FROM users');
        $rowCount = $this->db->rowCount($stmt);
        $this->assertIsInt($rowCount);
    }

    public function testQueryCount()
    {
        $queryCount = $this->db->queryCount();
        $this->assertIsArray($queryCount);
        $this->assertArrayHasKey('count', $queryCount);
        $this->assertArrayHasKey('queries', $queryCount);
    }

    public function testInsert()
    {
        $data = ['name' => 'john', 'email' => 'john@example.com'];
        $result = $this->db->insert('users', $data);
        $this->assertTrue($result);
    }

    public function testUpdate()
    {
        $data = ['name' => 'johnny'];
        $result = $this->db->update('users', $data, 'id = ?', [1]);
        $this->assertTrue($result);
    }

    public function testDelete()
    {
        $result = $this->db->delete('users', 'id = ?', [1]);
        $this->assertTrue($result);
    }

    public function testBeginTransaction()
    {
        $result = $this->db->beginTransaction();
        $this->assertTrue($result);
    }

    public function testCommit()
    {
        // Begin a transaction before performing any operation
        $this->db->beginTransaction();

        // Perform some operations here...
        $data = ['name' => 'test', 'email' => 'test@example.com'];
        $this->db->insert('users', $data);

        // Now try to commit the transaction
        $result = $this->db->commit();
        $this->assertTrue($result);
    }

    public function testRollBack()
    {
        // Begin a transaction before performing any operation
        $this->db->beginTransaction();

        // Perform some operations here...
        $data = ['name' => 'test', 'email' => 'test@example.com'];
        $this->db->insert('users', $data);

        // Now try to rollback the transaction
        $result = $this->db->rollBack();
        $this->assertTrue($result);
    }

    public function testGetLastInsertId()
    {
        $lastInsertId = $this->db->getLastInsertId();
        $this->assertIsString($lastInsertId);
    }

    public function testIsConnected()
    {
        $result = $this->db->isConnected();
        $this->assertTrue($result);
    }

    public function testTruncate()
    {
        // Truncate the users table
        $this->db->truncate('users');

        // Verify the table is empty after truncation
        $result = $this->db->fetchAll('SELECT * FROM users');
        $this->assertCount(0, $result);
    }



    public function testInvalidQuery()
    {
        // Test if an exception is thrown for an invalid SQL query
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Query failed');

        $this->db->query('INVALID SQL');
    }


    public function testInvalidConnection()
    {
        // Test if an exception is thrown for an invalid database connection
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Database connection failed');

        // Use invalid database credentials to create SimpleDB instance
        $db = new SimpleDB('invalid_host', 'invalid_port', 'invalid_db', 'invalid_user', 'invalid_password');
    }


}
