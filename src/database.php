<?php

class DatabaseResult
{
    private mysqli_stmt $stmt;
    private ?mysqli_result $result = null;
    private bool $hasResult = false;

    public function __construct(\mysqli_stmt $stmt)
    {
        $this->stmt = $stmt;

        $res = $stmt->get_result();
        $this->result = $res instanceof \mysqli_result ? $res : null;
        $this->hasResult = $this->result !== null;
    }

    public function all(): array
    {
        return $this->hasResult ? $this->result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function first(): ?array
    {
        return $this->hasResult ? $this->result->fetch_assoc() : null;
    }

    public function count(): int
    {
        return $this->hasResult ? $this->result->num_rows : $this->stmt->affected_rows;
    }

    public function rowsAffecteds(): int
    {
        return $this->stmt->affected_rows;
    }

    public function getId(): int
    {
        return $this->stmt->insert_id;
    }

    public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function close()
    {
        $this->stmt->close();
    }
}

class Database
{
    private $_connection;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $this->_connection = new mysqli("localhost", "root", "", "clients");
        if ($this->_connection->connect_errno) {
            throw new Exception("Error DB: " . $this->_connection->connect_error);
        }
        $this->_connection->set_charset('utf8');
    }

    private function detectTypes(array $bindings): string
    {
        $types = '';
        foreach ($bindings as $val) {
            if (is_int($val)) {
                $types .= 'i';
            } elseif (is_float($val)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        return $types;
    }

    public function prepare(string $sql, array $bindings = []): DatabaseResult
    {
        $stmt = $this->_connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error prepare: " . $this->_connection->error);
        }

        if (!empty($bindings)) {
            $types = $this->detectTypes($bindings);
            $stmt->bind_param($types, ...$bindings);
        }

        if (!$stmt->execute()) {
            throw new Exception("Error execute: " . $stmt->error);
        }

        return new DatabaseResult($stmt);
    }

    public function close()
    {
        $this->_connection->close();
    }
}
