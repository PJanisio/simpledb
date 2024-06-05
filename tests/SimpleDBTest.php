<?php

namespace SimpleDB\Tests;

use SimpleDB\SimpleDB;
use PHPUnit\Framework\TestCase;

class SimpleDBTest extends TestCase {
    private $db;

    public function setUp(): void {
        // Set up a SimpleDB instance for testing
        $this->db = new SimpleDB('localhost', '3306', 'test_database', 'root', 'password');
    }

    public function testQuery() {
        $stmt = $this->db->query('SELECT * FROM users');
        $this->assertInstanceOf('PDOStatement', $stmt);
    }

    public function testFetch() {
        $result = $this->db->fetch('SELECT * FROM users WHERE id = ?', [1]);
        $this->assertIsArray($result);
    }

    public function testFetchAll() {
        $result = $this->db->fetchAll('SELECT * FROM users');
        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
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
