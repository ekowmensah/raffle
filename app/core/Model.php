<?php

namespace App\Core;

class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function findAll()
    {
        $this->db->query("SELECT * FROM {$this->table}");
        return $this->db->resultSet();
    }

    public function findById($id)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        try {
            $this->db->query($sql);

            foreach ($data as $key => $value) {
                $this->db->bind(':' . $key, $value);
            }

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (\PDOException $e) {
            error_log("Model create error in table {$this->table}: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Data: " . json_encode($data));
            throw $e; // Re-throw to be caught by controller
        }
    }

    public function update($id, $data)
    {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= $key . ' = :' . $key . ', ';
        }
        $fields = rtrim($fields, ', ');

        $this->db->query("UPDATE {$this->table} SET {$fields} WHERE id = :id");
        $this->db->bind(':id', $id);

        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }

        return $this->db->execute();
    }

    public function delete($id)
    {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function where($conditions)
    {
        $where = '';
        foreach ($conditions as $key => $value) {
            $where .= $key . ' = :' . $key . ' AND ';
        }
        $where = rtrim($where, ' AND ');

        $this->db->query("SELECT * FROM {$this->table} WHERE {$where}");

        foreach ($conditions as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }

        return $this->db->resultSet();
    }
}
