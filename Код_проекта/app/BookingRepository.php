<?php
class BookingRepository
{
    private PDO $db;
    private const SELECT = '
        SELECT b.*, w.title AS workshop, w.starts_at, w.location, s.title AS status,
               u.fio, u.login, u.phone, u.email
        FROM bookings b
        JOIN workshops w        ON w.workshop_id = b.workshop_id
        JOIN booking_statuses s ON s.status_id   = b.status_id
        JOIN users u            ON u.user_id     = b.user_id';

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function add(int $userId, array $d): int
    {
        $st = $this->db->prepare(
            'INSERT INTO bookings (user_id, workshop_id, payment, room, wishes) VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([$userId, (int)$d['workshop_id'], trim($d['payment']), trim($d['room']), trim($d['wishes'] ?? '')]);
        return (int)$this->db->lastInsertId();
    }

    public function ofUser(int $userId): array
    {
        $st = $this->db->prepare(self::SELECT . ' WHERE b.user_id = ? ORDER BY b.created DESC, b.booking_id DESC');
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query(self::SELECT . ' ORDER BY b.booking_id DESC')->fetchAll();
    }

    public function statuses(): array
    {
        return $this->db->query('SELECT * FROM booking_statuses ORDER BY status_id')->fetchAll();
    }

    public function changeStatus(int $bookingId, int $statusId): void
    {
        $st = $this->db->prepare('UPDATE bookings SET status_id = ? WHERE booking_id = ?');
        $st->execute([$statusId, $bookingId]);
    }
}
