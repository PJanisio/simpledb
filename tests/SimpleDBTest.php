<?php

use PHPUnit\Framework\TestCase;

class SimpleDBTest extends TestCase {
    private $db;

    protected function setUp(): void {
        $dsn = 'mysql:host=localhost';
        $username = 'test_user';
        $password = 'root_password';
        $dbName = 'test_database';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $this->db = new SimpleDB($dsn, $username, $password, $dbName, $options);
        $this->initializeTestData();
    }

    protected function tearDown(): void {
        $this->cleanupTestData();
    }

    private function initializeTestData(): void {
        $this->db->execute('CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255))');
        $this->db->execute('INSERT INTO users (name) VALUES ("Alice")');
        $this->db->execute('INSERT INTO users (name) VALUES ("Bob")');
    }

    private function cleanupTestData(): void {
        $this->db->execute('DROP TABLE users');
    }

    public function testFetchAll(): void {
        $result = $this->db->fetchAll('SELECT * FROM users');
        $this->assertCount(2, $result);
    }

    public function testFetch(): void {
        $result = $this->db->fetch('SELECT * FROM users WHERE id = ?', [1]);
        $this->assertNotNull($result);
        $this->assertEquals('Alice', $result['name']);
    }

    public function testInsert() {
        $this->assertTrue($this->db->insert('users', ['name' => 'Alice', 'status' => 'active']));
        $this->assertEquals('3', $this->db->getLastInsertId());
    }

    public function testUpdate() {
        $this->assertTrue($this->db->update('users', ['status' => 'inactive'], 'name = ?', ['John Doe']));
    }

    public function testDelete() {
        $this->assertTrue($this->db->delete('users', 'name = ?', ['Jane Doe']));
    }

    public function testTransaction() {
        $this->db->beginTransaction();
        $this->db->insert('users', ['name' => 'Bob', 'status' => 'active']);
        $this->db->update('users', ['status' => 'inactive'], 'name = ?', ['Bob']);
        $this->assertTrue($this->db->commit());
    }

    public function testIsConnected() {
        $this->assertTrue($this->db->isConnected());
    }

    public function testRowCount() {
        $stmt = $this->db->query('SELECT * FROM users WHERE status = ?', ['active']);
        $this->assertEquals(1, $this->db->rowCount($stmt));
    }

    public function testQueryCount() {
        $this->db->execute('SELECT * FROM users');
        $queryInfo = $this->db->queryCount();
        $this->assertEquals(1, $queryInfo['count']);
        $this->assertNotEmpty($queryInfo['queries']);
        $this->assertEquals('SELECT * FROM users', $queryInfo['queries'][0]['query']);
    }
}