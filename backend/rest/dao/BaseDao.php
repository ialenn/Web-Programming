<?php
require_once __DIR__ . '/../config.php';

class BaseDao {
    protected $table;
    protected $connection;

    public function __construct($table) {
        $this->table = $table;
        $this->connection = Database::connect();
    }

    public function get_all() {
        $stmt = $this->connection->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function get_by_id($id) {
        $stmt = $this->connection->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function insert($data) {
        if (empty($data) || !is_array($data)) {
            throw new InvalidArgumentException("Insert data must be a non-empty array.");
        }
        $cols = implode(',', array_keys($data));
        $placeholders = ':' . implode(',:', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($cols) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);

        return $this->connection->lastInsertId();
    }

    public function update($id, $data) {
        if (empty($data) || !is_array($data)) {
            throw new InvalidArgumentException("Update data must be a non-empty array.");
        }
        $pairs = [];
        foreach ($data as $key => $val) {
            $pairs[] = "$key = :$key";
        }
        $set = implode(', ', $pairs);

        $data['id'] = $id;
        $sql = "UPDATE {$this->table} SET $set WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);
        return true;
    }

    public function delete($id) {
        $stmt = $this->connection->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return true;
    }
}
?>