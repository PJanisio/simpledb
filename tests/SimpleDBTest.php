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

    public function testExecute() {
        $stmt = $this->db->execute('CREATE TABLE test_table (id INT)');
        $this->assertInstanceOf('PDOStatement', $stmt);
    }

    public function testRowCount() {
        $stmt = $this->db->query('SELECT * FROM users');
        $rowCount = $this->db->rowCount($stmt);
        $this->assertIsInt($rowCount);
    }

    public function testQueryCount() {
        $queryCount = $this->db->queryCount();
        $this->assertIsArray($queryCount);
        $this->assertArrayHasKey('count', $queryCount);
        $this->assertArrayHasKey('queries', $queryCount);
    }

    public function testInsert() {
        $data = ['username' => 'john', 'email' => 'john@example.com'];
        $result = $this->db->insert('users', $data);
        $this->assertTrue($result);
    }

    public function testUpdate() {
        $data = ['username' => 'johnny'];
        $result = $this->db->update('users', $data, 'id = ?', [1]);
        $this->assertTrue($result);
    }

    public function testDelete() {
        $result = $this->db->delete('users', 'id = ?', [1]);
        $this->assertTrue($result);
    }

    public function testBeginTransaction() {
        $result = $this->db->beginTransaction();
        $this->assertTrue($result);
    }

    public function testCommit() {
        $result = $this->db->commit();
        $this->assertTrue($result);
    }

    public function testRollBack() {
        $result = $this->db->rollBack();
        $this->assertTrue($result);
    }

    public function testGetLastInsertId() {
        $lastInsertId = $this->db->getLastInsertId();
        $this->assertIsString($lastInsertId);
    }

    public function testIsConnected() {
        $result = $this->db->isConnected();
        $this->assertTrue($result);
    }
}