<?php

use PHPUnit\Framework\TestCase;

class SimpleDBTest extends TestCase {
    private $db;

    protected function setUp(): void {
        $dsn = 'sqlite::memory:';
        $username = '';
        $password = '';
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

    public function testFetchAll() {
        $stmt = $this->db->query('SELECT * FROM users WHERE status = ?', ['active']);
        $result = $stmt->fetchAll();
        $this->assertCount(1, $result);
        $this->assertEquals('John Doe', $result[0]['name']);
    }

    public function testFetch() {
        $result = $this->db->fetch('SELECT * FROM users WHERE id = ?', [1]);
        $this->assertNotNull($result);
        $this->assertEquals('John Doe', $result['name']);
    }

    public function testQueryFailure() {
        $this->expectException(Exception::class);
        $this->db->query('SELECT * FROM non_existing_table');
    }

    public function testExecute() {
        $stmt = $this->db->execute('SELECT * FROM users');
        $result = $stmt->fetchAll();
        $this->assertCount(2, $result);
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