<?php

use PHPUnit\Framework\TestCase;

class SimpleDBTest extends TestCase {
    private $db;

    protected function setUp(): void {
        $dsn = 'sqlite::memory:';
        $username = 'test_user';
        $password = 'root_password';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $pdo = new PDO($dsn);
        $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, status TEXT)');
        $pdo->exec('INSERT INTO users (name, status) VALUES ("John Doe", "active")');
        $pdo->exec('INSERT INTO users (name, status) VALUES ("Jane Doe", "inactive")');

        $this->db = new SimpleDB($dsn, $username, $password, $options);
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