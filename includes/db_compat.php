<?php
/**
 * PDO Compatibility Layer
 * Wraps PDO to mimic mysqli interface so existing code works unchanged.
 * Supports both PostgreSQL and MySQL via PDO drivers.
 */

class CompatDB {
    private $pdo;

    public function __construct($dsn, $user = '', $pass = '', $options = []) {
        $this->pdo = new PDO($dsn, $user, $pass, array_merge([
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ], $options));
    }

    public function query($sql) {
        try {
            $stmt = $this->pdo->query($sql);
            return new CompatResult($stmt);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }

    public function real_escape_string($str) {
        if (!is_string($str)) return $str;
        // Match mysqli behavior: escape special chars WITHOUT adding quotes
        // PDO::quote() adds quotes, so we strip them
        $quoted = $this->pdo->quote($str);
        // Remove surrounding quotes added by PDO::quote
        if (strlen($quoted) >= 2 && $quoted[0] === "'" && substr($quoted, -1) === "'") {
            return substr($quoted, 1, -1);
        }
        return $quoted;
    }

    public function escape_string($str) {
        return $this->real_escape_string($str);
    }

    public function set_charset($charset) {
        // Handled in DSN
    }

    public function connect_error() {
        return '';
    }

    public function insert_id() {
        return $this->pdo->lastInsertId();
    }

    public function get_pdo() {
        return $this->pdo;
    }

    public function beginTransaction() { return $this->pdo->beginTransaction(); }
    public function commit() { return $this->pdo->commit(); }
    public function rollBack() { return $this->pdo->rollBack(); }
}

class CompatResult {
    private $stmt;
    private $rows = null;
    private $pos = 0;

    public function __construct($stmt) {
        $this->stmt = $stmt;
    }

    public function fetch_assoc() {
        if ($this->rows === null) {
            $this->rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        if ($this->pos < count($this->rows)) {
            return $this->rows[$this->pos++];
        }
        return null;
    }

    public function fetch_array() {
        return $this->fetch_assoc();
    }

    public function fetch_all($mode = PDO::FETCH_ASSOC) {
        return $this->stmt->fetchAll($mode);
    }

    public function data_seek($pos) {
        $this->pos = $pos;
    }

    public function num_rows() {
        if ($this->rows === null) {
            $this->rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return count($this->rows);
    }

    // Allow ->num_rows property access
    public function __get($name) {
        if ($name === 'num_rows') {
            return $this->num_rows();
        }
        return null;
    }
}
