<?php

namespace App\Repository;

use PDO;

class Task
{
    public function __construct(private PDO $pdo)
    {
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists(): void
    {
        $this->pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    status TEXT DEFAULT 'Pendente',
    description TEXT,
    dueDate TEXT,
    starred BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
SQL);
    }

    public function getAll(): array
    {
        $sql = <<<SQLSELECT
SELECT id, title, 
       status, 
       description, 
       dueDate, 
       starred, 
       created_at 
  FROM tasks 
 ORDER BY created_at DESC
SQLSELECT;
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get(int $id): array
    {
        $sql = <<<SQLGET
SELECT id, title, status, description, dueDate, starred, created_at  
  FROM tasks 
 WHERE id = :id
SQLGET;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(string $title): array
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO tasks (title) VALUES (:title)"
        );
        $stmt->execute(["title" => $title]);
        $newId = $this->pdo->lastInsertId();
        return $this->get($newId);
    }
}
