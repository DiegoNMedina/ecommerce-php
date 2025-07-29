<?php

namespace Models;

class Product extends Model {
    protected string $table = 'products';

    public function findWithCategories(int $id): ?array {
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

    public function findByCategory(int $categoryId, int $limit = 10, int $offset = 0): array {
        // Verificar si es una categoría principal
        $categoryCheck = $this->db->prepare("SELECT parent_id FROM categories WHERE id = ?");
        $categoryCheck->execute([$categoryId]);
        $categoryInfo = $categoryCheck->fetch();
        
        if ($categoryInfo && $categoryInfo['parent_id'] === null) {
            // Es categoría principal: incluir productos de subcategorías
            $sql = "SELECT p.*, COUNT(DISTINCT com.id) as comment_count, AVG(com.rating) as avg_rating
                    FROM products p
                    JOIN product_categories pc ON p.id = pc.product_id
                    JOIN categories c ON pc.category_id = c.id
                    LEFT JOIN comments com ON p.id = com.product_id
                    WHERE c.id = ? OR c.parent_id = ?
                    GROUP BY p.id
                    ORDER BY AVG(com.rating) DESC, p.visits DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId, $categoryId, $limit, $offset]);
        } else {
            // Es subcategoría: productos directos solamente
            $sql = "SELECT p.*, COUNT(DISTINCT com.id) as comment_count, AVG(com.rating) as avg_rating
                    FROM products p
                    JOIN product_categories pc ON p.id = pc.product_id
                    LEFT JOIN comments com ON p.id = com.product_id
                    WHERE pc.category_id = ?
                    GROUP BY p.id
                    ORDER BY AVG(com.rating) DESC, p.visits DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId, $limit, $offset]);
        }
        
        return $stmt->fetchAll();
    }

    public function search(string $query, int $limit = 10, int $offset = 0): array {
        $searchTerm = "%{$query}%";
        $sql = "SELECT p.*, COUNT(DISTINCT com.id) as comment_count, AVG(com.rating) as avg_rating,
                GROUP_CONCAT(DISTINCT c.id, ':', c.name SEPARATOR '|') as categories_data
                FROM products p
                LEFT JOIN comments com ON p.id = com.product_id
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE p.model LIKE ? OR p.brand LIKE ? OR p.specifications LIKE ?
                GROUP BY p.id
                ORDER BY AVG(com.rating) DESC, p.visits DESC
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

    public function getRandomProducts(int $limit = 10): array {
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

    public function getMostVisited(int $limit = 10): array {
        $sql = "SELECT p.*, COUNT(DISTINCT com.id) as comment_count, AVG(com.rating) as avg_rating
                FROM products p
                LEFT JOIN comments com ON p.id = com.product_id
                GROUP BY p.id
                ORDER BY AVG(com.rating) DESC, p.visits DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function incrementVisits(int $id): bool {
        $sql = "UPDATE products SET visits = visits + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function calculateInstallments(float $price, int $months): float {
        $stmt = $this->db->prepare("CALL calculate_installments(?, ?, @monthly_payment)");
        $stmt->execute([$price, $months]);

        $result = $this->db->query("SELECT @monthly_payment as payment")->fetch();
        return (float) $result['payment'];
    }

    public function getComments(int $productId, int $limit = 10, int $offset = 0): array {
        $sql = "SELECT * FROM comments 
                WHERE product_id = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function addComment(int $productId, string $name, string $comment, int $rating): int {
        $sql = "INSERT INTO comments (product_id, name, comment, rating) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId, $name, $comment, $rating]);
        return (int) $this->db->lastInsertId();
    }

    public function getBestSelling(int $limit = 10, int $offset = 0): array {
        $sql = "SELECT p.*, COUNT(DISTINCT com.id) as comment_count, AVG(com.rating) as avg_rating,
                GROUP_CONCAT(DISTINCT c.id, ':', c.name SEPARATOR '|') as categories_data
                FROM products p
                LEFT JOIN comments com ON p.id = com.product_id
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE p.sales_count > 0
                GROUP BY p.id
                ORDER BY AVG(com.rating) DESC, p.sales_count DESC, p.visits DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
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

    public function getTotalBestSelling(): int {
        $sql = "SELECT COUNT(*) as total FROM products WHERE sales_count > 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) $result['total'];
    }

    public function incrementSales(int $id, int $quantity = 1): bool {
        $sql = "UPDATE products SET sales_count = sales_count + ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantity, $id]);
    }

    public function findAllWithRating(int $limit = 10, int $offset = 0): array {
        $sql = "SELECT p.*, COUNT(DISTINCT com.id) as comment_count, AVG(com.rating) as avg_rating
                FROM products p
                LEFT JOIN comments com ON p.id = com.product_id
                GROUP BY p.id
                ORDER BY AVG(com.rating) DESC, p.visits DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function incrementLikes(int $id): bool {
        $sql = "UPDATE products SET likes = likes + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getLikes(int $id): int {
        $sql = "SELECT likes FROM products WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return (int) ($result['likes'] ?? 0);
    }
}