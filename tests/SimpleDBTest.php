<?php

use PHPUnit\Framework\TestCase;
use SimpleDB\SimpleDB;

class SimpleDBTest extends TestCase {
    private $db;

    protected function setUp(): void {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dbName = getenv('DB_NAME');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASS');

        $this->db = new SimpleDB($host, $port, $dbName, $username, $password);
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

    // Add more test methods as needed
}
