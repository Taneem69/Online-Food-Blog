<?php
require_once __DIR__ . '/../config/database.php';

class MenuItemModel {

    public function getByRestaurant(int $restaurantId): array {
        $stmt = getDB()->prepare(
            "SELECT m.*, COUNT(r.id) AS review_count
             FROM menu_items m
             LEFT JOIN reviews r ON r.menu_item_id = m.id
             WHERE m.restaurant_id = ?
             GROUP BY m.id
             ORDER BY m.created_at DESC"
        );
        $stmt->execute([$restaurantId]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = getDB()->prepare(
            "SELECT m.*, res.name AS restaurant_name, res.id AS restaurant_id
             FROM menu_items m
             JOIN restaurants res ON res.id = m.restaurant_id
             WHERE m.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = getDB()->prepare(
            "INSERT INTO menu_items (restaurant_id, name, description, price, image_path)
             VALUES (:restaurant_id, :name, :description, :price, :image_path)"
        );
        $stmt->execute([
            ':restaurant_id' => $data['restaurant_id'],
            ':name'          => $data['name'],
            ':description'   => $data['description'],
            ':price'         => $data['price'],
            ':image_path'    => $data['image_path'] ?? null,
        ]);
        return (int) getDB()->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = getDB()->prepare(
            "UPDATE menu_items
             SET name=:name, description=:description, price=:price, image_path=:image_path
             WHERE id=:id AND restaurant_id=:restaurant_id"
        );
        return $stmt->execute([
            ':name'          => $data['name'],
            ':description'   => $data['description'],
            ':price'         => $data['price'],
            ':image_path'    => $data['image_path'] ?? null,
            ':id'            => $id,
            ':restaurant_id' => $data['restaurant_id'],
        ]);
    }

    public function delete(int $id): bool {
        $stmt = getDB()->prepare("DELETE FROM menu_items WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countAll(): int {
        return (int) getDB()->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();
    }

    public function countReviews(): int {
        return (int) getDB()->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
    }

    public function countFoodExperiencePosts(): int {
        return (int) getDB()->query("SELECT COUNT(*) FROM food_experience_posts")->fetchColumn();
    }
}
