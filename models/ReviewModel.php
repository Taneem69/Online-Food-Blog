<?php
class ReviewModel
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function getByMenuItem(int $menuItemId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                r.id,
                r.comment,
                r.created_at,
                r.user_id,
                u.name AS reviewer_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.menu_item_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$menuItemId]);
        return $stmt->fetchAll();
    }
    public function getRestaurantReviews(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                r.id,
                r.comment,
                r.created_at,
                r.user_id,
                u.name AS reviewer_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.menu_item_id IS NULL
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                r.id,
                r.comment,
                r.created_at,
                r.user_id,
                u.name AS reviewer_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function add(?int $menuItemId, int $userId, string $comment): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO reviews (menu_item_id, user_id, comment, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$menuItemId, $userId, $comment]);
        return (int) $this->pdo->lastInsertId();
    }
    public function delete(int $reviewId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM reviews
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$reviewId, $userId]);
        return $stmt->rowCount() > 0;
    }
    public function findByIdAndUser(int $reviewId, int $userId): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT id, user_id
            FROM reviews
            WHERE id = ?
        ");
        $stmt->execute([$reviewId]);
        return $stmt->fetch();
    }
    public function countByMenuItem(int $menuItemId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) AS total
            FROM reviews
            WHERE menu_item_id = ?
        ");
        $stmt->execute([$menuItemId]);
        $result = $stmt->fetch();
        return (int) $result['total'];
    }
    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                r.id,
                r.comment,
                r.created_at,
                r.user_id,
                r.menu_item_id,
                u.name  AS reviewer_name,
                mi.name AS menu_item_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN menu_items mi ON r.menu_item_id = mi.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll();
    }
<<<<<<< Updated upstream
}
=======
}
>>>>>>> Stashed changes
