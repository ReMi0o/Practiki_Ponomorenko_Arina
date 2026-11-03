<?php
class BookingRepository
{
    private PDO $db;
    private const SELECT = '
        SELECT b.*, w.title AS workshop, w.starts_at, w.location, s.title AS status,
               u.fio, u.login, u.phone, u.email, f.stars, f.message AS feedback
        FROM bookings b
        JOIN workshops w        ON w.workshop_id = b.workshop_id
        JOIN booking_statuses s ON s.status_id   = b.status_id
        JOIN users u            ON u.user_id     = b.user_id
        LEFT JOIN feedback f    ON f.booking_id  = b.booking_id';

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function add(int $userId, array $d): int
    {
        $st = $this->db->prepare(
            'INSERT INTO bookings (user_id, workshop_id, payment, room, wishes) VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([$userId, (int)$d['workshop_id'], Options::PAYMENT[$d['payment']],
                      Options::ROOMS[$d['room']], trim($d['wishes'] ?? '')]);
        return (int)$this->db->lastInsertId();
    }

    public function ofUser(int $userId): array
    {
        $st = $this->db->prepare(self::SELECT . ' WHERE b.user_id = ? ORDER BY b.created DESC, b.booking_id DESC');
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare(self::SELECT . ' WHERE b.booking_id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    /** Все заявки; если передан статус — только с этим статусом */
    public function all(?int $statusId = null): array
    {
        if ($statusId) {
            $st = $this->db->prepare(self::SELECT . ' WHERE b.status_id = ? ORDER BY b.booking_id DESC');
            $st->execute([$statusId]);
            return $st->fetchAll();
        }
        return $this->db->query(self::SELECT . ' ORDER BY b.booking_id DESC')->fetchAll();
    }

    /** Количество заявок по каждому статусу — для счётчиков фильтра */
    public function countByStatus(): array
    {
        return $this->db->query('SELECT status_id, COUNT(*) AS n FROM bookings GROUP BY status_id')
                        ->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function freeSeats(array $workshop): int
    {
        $st = $this->db->prepare('SELECT COUNT(*) FROM bookings WHERE workshop_id = ?');
        $st->execute([$workshop['workshop_id']]);
        return (int)$workshop['seats'] - (int)$st->fetchColumn();
    }

    /** Отзыв можно оставить только по завершённой заявке и только один раз */
    public function canLeaveFeedback(array $b): bool
    {
        return $b['status'] === 'Завершено' && $b['feedback'] === null;
    }

    public function addFeedback(int $bookingId, int $stars, string $message): void
    {
        $st = $this->db->prepare('INSERT INTO feedback (booking_id, stars, message) VALUES (?, ?, ?)');
        $st->execute([$bookingId, $stars, trim($message)]);
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
