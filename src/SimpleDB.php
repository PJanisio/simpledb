<?php

namespace SimpleDB;

use PDO;
use PDOException;
use Exception;
use PDOStatement; // Import PDOStatement class

class SimpleDB {
    private $pdo;
    private $queryCount = 0;
    private $queries = [];

    public function __construct(string $host, string $port, string $dbName, string $username, string $password, array $options = []) {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbName";
        try {
            $defaultOptions = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            $options = array_replace($defaultOptions, $options);

            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public function query(string $sql, array $params = []): PDOStatement {
        try {
            $startTime = microtime(true);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;

            $this->queryCount++;
            $this->queries[] = [
                'query' => $sql,
                'params' => $params,
                'execution_time' => $executionTime
            ];

            return $stmt;
        } catch (PDOException $e) {
            throw new Exception('Query failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public function fetch(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params)->fetch();
        return $result === false ? null : $result;
    }

    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    public function execute(string $sql): SimpleDB\PDOStatement { // Return SimpleDB\PDOStatement
        try {
            $startTime = microtime(true);
            $stmt = $this->pdo->query($sql);
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;

            $this->queryCount++;
            $this->queries[] = [
                'query' => $sql,
                'execution_time' => $executionTime
            ];

            return new class($stmt) extends SimpleDB\PDOStatement { // Return SimpleDB\PDOStatement
                private $stmt;

                public function __construct(PDOStatement $stmt) { // Accept PDOStatement as argument
                    $this->stmt = $stmt;
                }

                // Implement the necessary methods from PDOStatement
            };
        } catch (PDOException $e) {
            throw new Exception('Query execution failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public function rowCount(PDOStatement $stmt): int {
        return $stmt->rowCount();
    }

    public function queryCount(): array {
        return [
            'count' => $this->queryCount,
            'queries' => $this->queries
        ];
    }

    public function insert(string $table, array $data): bool {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        return $this->query($sql, array_values($data))->rowCount() > 0;
    }

    public function update(string $table, array $data, string $where, array $params = []): bool {
        $setClause = implode(", ", array_map(fn($key) => "$key = ?", array_keys($data)));
        $sql = "UPDATE $table SET $setClause WHERE $where";
        return $this->query($sql, array_merge(array_values($data), $params))->rowCount() > 0;
    }

    public function delete(string $table, string $where, array $params = []): bool {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params)->rowCount() > 0;
    }

    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool {
        return $this->pdo->commit();
    }

    public function rollBack(): bool {
        return $this->pdo->rollBack();
    }

    public function getLastInsertId(): string {
        return $this->pdo->lastInsertId();
    }

    public function isConnected(): bool {
        try {
            $this->pdo->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
