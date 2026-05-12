<?php
require_once __DIR__ . '/../config/database.php';

class RestaurantModel {

    public function getAll(): array {
        $stmt = getDB()->query(
            "SELECT r.*, COUNT(m.id) AS item_count
             FROM restaurants r
             LEFT JOIN menu_items m ON m.restaurant_id = r.id
             GROUP BY r.id
             ORDER BY r.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = getDB()->prepare("SELECT * FROM restaurants WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = getDB()->prepare(
            "INSERT INTO restaurants (name, location, area, short_background, goals)
             VALUES (:name, :location, :area, :short_background, :goals)"
        );
        $stmt->execute([
            ':name'             => $data['name'],
            ':location'         => $data['location'],
            ':area'             => $data['area'],
            ':short_background' => $data['short_background'],
            ':goals'            => $data['goals'],
        ]);
        return (int) getDB()->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = getDB()->prepare(
            "UPDATE restaurants
             SET name=:name, location=:location, area=:area,
                 short_background=:short_background, goals=:goals
             WHERE id=:id"
        );
        return $stmt->execute([
            ':name'             => $data['name'],
            ':location'         => $data['location'],
            ':area'             => $data['area'],
            ':short_background' => $data['short_background'],
            ':goals'            => $data['goals'],
            ':id'               => $id,
        ]);
    }

    public function delete(int $id): bool {
        // menu_items cascade via FK
        $stmt = getDB()->prepare("DELETE FROM restaurants WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countAll(): int {
        return (int) getDB()->query("SELECT COUNT(*) FROM restaurants")->fetchColumn();
    }
}
