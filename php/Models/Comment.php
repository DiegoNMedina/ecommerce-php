<?php

namespace Models;

class Comment extends Model {
    protected string $table = 'comments';

    /**
     * Obtener todos los comentarios de un producto
     */
    public function findByProduct(int $productId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM comments 
            WHERE product_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener comentarios de un producto con paginación
     */
    public function findByProductPaginated(int $productId, int $limit = 10, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT * FROM comments 
            WHERE product_id = ? 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$productId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Contar comentarios de un producto
     */
    public function countByProduct(int $productId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM comments WHERE product_id = ?");
        $stmt->execute([$productId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Obtener el promedio de calificación de un producto
     */
    public function getAverageRating(int $productId): float {
        $stmt = $this->db->prepare("SELECT AVG(rating) FROM comments WHERE product_id = ?");
        $stmt->execute([$productId]);
        $average = $stmt->fetchColumn();
        return $average ? round((float) $average, 1) : 0.0;
    }

    /**
     * Obtener estadísticas de calificaciones de un producto
     */
    public function getRatingStats(int $productId): array {
        $stmt = $this->db->prepare("
            SELECT 
                rating,
                COUNT(*) as count
            FROM comments 
            WHERE product_id = ? 
            GROUP BY rating 
            ORDER BY rating DESC
        ");
        $stmt->execute([$productId]);
        $stats = $stmt->fetchAll();
        
        // Inicializar array con todas las calificaciones
        $result = [];
        for ($i = 5; $i >= 1; $i--) {
            $result[$i] = 0;
        }
        
        // Llenar con los datos reales
        foreach ($stats as $stat) {
            $result[$stat['rating']] = (int) $stat['count'];
        }
        
        return $result;
    }

    /**
     * Crear un nuevo comentario
     */
    public function createComment(int $productId, string $name, string $comment, int $rating): int {
        $data = [
            'product_id' => $productId,
            'name' => $name,
            'comment' => $comment,
            'rating' => $rating
        ];
        
        return $this->create($data);
    }

    /**
     * Validar datos de comentario
     */
    public function validateComment(array $data): array {
        $errors = [];
        
        if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
        }
        
        if (empty($data['comment']) || strlen(trim($data['comment'])) < 10) {
            $errors['comment'] = 'El comentario debe tener al menos 10 caracteres';
        }
        
        if (empty($data['rating']) || !in_array((int)$data['rating'], [1, 2, 3, 4, 5])) {
            $errors['rating'] = 'La calificación debe ser entre 1 y 5 estrellas';
        }
        
        return $errors;
    }

    /**
     * Obtener comentarios recientes del sitio
     */
    public function getRecentComments(int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT 
                c.*,
                p.model as product_name,
                p.brand as product_brand
            FROM comments c
            JOIN products p ON c.product_id = p.id
            ORDER BY c.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}