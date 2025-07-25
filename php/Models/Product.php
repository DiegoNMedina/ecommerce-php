<?php

namespace Models;

class Product extends Model
{
    protected string $table = 'products';

    public function findWithCategories(int $id): ?array
    {
        $sql = "SELECT p.*, GROUP_CONCAT(c.name) as categories
                FROM products p
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE p.id = ?
                GROUP BY p.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByCategory(int $categoryId, int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT p.*, COUNT(DISTINCT com.id) as comment_count, AVG(com.rating) as avg_rating
                FROM products p
                JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN comments com ON p.id = com.product_id
                WHERE pc.category_id = ?
                GROUP BY p.id
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function search(string $query, int $limit = 10, int $offset = 0): array
    {
        $searchTerm = "%{$query}%";
        $sql = "SELECT p.*, COUNT(DISTINCT com.id) as comment_count, AVG(com.rating) as avg_rating,
                GROUP_CONCAT(DISTINCT c.id, ':', c.name SEPARATOR '|') as categories_data
                FROM products p
                LEFT JOIN comments com ON p.id = com.product_id
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE p.model LIKE ? OR p.brand LIKE ? OR p.specifications LIKE ?
                GROUP BY p.id
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit, $offset]);
        $products = $stmt->fetchAll();

        // Procesar las categorías para cada producto
        foreach ($products as &$product) {
            $product['categories'] = [];
            if (!empty($product['categories_data'])) {
                $categoriesData = explode('|', $product['categories_data']);
                foreach ($categoriesData as $categoryData) {
                    if (!empty($categoryData)) {
                        list($id, $name) = explode(':', $categoryData);
                        $product['categories'][] = ['id' => $id, 'name' => $name];
                    }
                }
            }
            unset($product['categories_data']);
        }

        return $products;
    }

    public function getRandomProducts(int $limit = 10): array
    {
        $sql = "SELECT p.*, COUNT(DISTINCT com.id) as comment_count, AVG(com.rating) as avg_rating
                FROM products p
                LEFT JOIN comments com ON p.id = com.product_id
                GROUP BY p.id
                ORDER BY RAND()
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getMostVisited(int $limit = 10): array
    {
        $sql = "SELECT p.*, COUNT(DISTINCT com.id) as comment_count, AVG(com.rating) as avg_rating
                FROM products p
                LEFT JOIN comments com ON p.id = com.product_id
                GROUP BY p.id
                ORDER BY p.visits DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function incrementVisits(int $id): bool
    {
        $sql = "UPDATE products SET visits = visits + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function calculateInstallments(float $price, int $months): float
    {
        $stmt = $this->db->prepare("CALL calculate_installments(?, ?, @monthly_payment)");
        $stmt->execute([$price, $months]);

        $result = $this->db->query("SELECT @monthly_payment as payment")->fetch();
        return (float) $result['payment'];
    }

    public function getComments(int $productId, int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT * FROM comments 
                WHERE product_id = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function addComment(int $productId, string $name, string $comment, int $rating): int
    {
        $sql = "INSERT INTO comments (product_id, name, comment, rating) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId, $name, $comment, $rating]);
        return (int) $this->db->lastInsertId();
    }
}
