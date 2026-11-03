<?php
class WorkshopRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM workshops ORDER BY starts_at')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM workshops WHERE workshop_id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }
}
