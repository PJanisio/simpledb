<?php

class SimpleDB {
    private $pdo;
    private $queryCount = 0;
    private $queries = [];
    
    public function __construct(string $dsn, string $username, string $password, array $options = []) {
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

    /**
     * Executes a query and returns the statement.
     *
     * @param string $sql The SQL query.
     * @param array $params Parameters to bind to the query.
     * @return PDOStatement The executed statement.
     * @throws Exception If the query execution fails.
     */
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

    /**
     * Fetches a single row from the database.
     *
     * @param string $sql The SQL query.
     * @param array $params Parameters to bind to the query.
     * @return array|null The fetched row, or null if no row was found.
     */
    public function fetch(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params)->fetch();
        return $result === false ? null : $result;
    }

    /**
     * Fetches all rows from the database.
     *
     * @param string $sql The SQL query.
     * @param array $params Parameters to bind to the query.
     * @return array The fetched rows.
     */
    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Executes a raw SQL query without fetching results automatically.
     *
     * @param string $sql The raw SQL query.
     * @return PDOStatement The executed statement.
     * @throws Exception If the query execution fails.
     */
    public function execute(string $sql): PDOStatement {
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

            return $stmt;
        } catch (PDOException $e) {
            throw new Exception('Query execution failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Returns the number of results from the last executed query.
     *
     * @param PDOStatement $stmt The PDO statement.
     * @return int The number of rows.
     */
    public function rowCount(PDOStatement $stmt): int {
        return $stmt->rowCount();
    }

    /**
     * Returns the total number of queries executed and details of each query.
     *
     * @return array An array containing the query count and the query details.
     */
    public function queryCount(): array {
        return [
            'count' => $this->queryCount,
            'queries' => $this->queries
        ];
    }
}
