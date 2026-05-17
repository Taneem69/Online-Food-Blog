<?php

class User
{
    public static function findByEmail(PDO $db, string $email)
    {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function findById(PDO $db, int $id)
    {
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function emailExists(PDO $db, string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
        }

        return (bool) $stmt->fetch();
    }

    public static function create(PDO $db, string $name, string $email, string $password, string $role): bool
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare(
            "INSERT INTO users (name, email, password_hash, role)
             VALUES (?, ?, ?, ?)"
        );

        return $stmt->execute([
            $name,
            $email,
            $passwordHash,
            $role
        ]);
    }

    public static function updateProfile(
        PDO $db,
        int $id,
        string $name,
        string $email,
        ?string $profilePicture = null
    ): bool {
        if ($profilePicture !== null) {
            $stmt = $db->prepare(
                "UPDATE users
                 SET name = ?, email = ?, profile_picture = ?
                 WHERE id = ?"
            );

            return $stmt->execute([
                $name,
                $email,
                $profilePicture,
                $id
            ]);
        }

        $stmt = $db->prepare(
            "UPDATE users
             SET name = ?, email = ?
             WHERE id = ?"
        );

        return $stmt->execute([
            $name,
            $email,
            $id
        ]);
    }

    public static function updatePassword(PDO $db, int $id, string $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $db->prepare(
            "UPDATE users
             SET password_hash = ?
             WHERE id = ?"
        );

        return $stmt->execute([
            $passwordHash,
            $id
        ]);
    }

    public static function setRememberToken(PDO $db, int $id, ?string $tokenHash): bool
    {
        $stmt = $db->prepare(
            "UPDATE users
             SET remember_token = ?
             WHERE id = ?"
        );

        return $stmt->execute([
            $tokenHash,
            $id
        ]);
    }

    public static function clearRememberToken(PDO $db, int $id): bool
    {
        $stmt = $db->prepare(
            "UPDATE users
             SET remember_token = NULL
             WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }

    public static function getAllMembers(PDO $db): array
    {
        $stmt = $db->prepare(
            "SELECT id, name, email, role, profile_picture, created_at
             FROM users
             WHERE role = 'member'
             ORDER BY created_at DESC"
        );

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function deleteById(PDO $db, int $id): bool
    {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}