<?php

namespace Models;

class Category extends Model {
    protected string $table = 'categories';

    public function getMainCategories(): array {
        $sql = "SELECT c.*, COUNT(pc.product_id) as product_count
                FROM categories c
                LEFT JOIN product_categories pc ON c.id = pc.category_id
                WHERE c.parent_id IS NULL
                GROUP BY c.id
                ORDER BY c.name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSubcategories(int $parentId): array {
        $sql = "SELECT c.*, COUNT(pc.product_id) as product_count
                FROM categories c
                LEFT JOIN product_categories pc ON c.id = pc.category_id
                WHERE c.parent_id = ?
                GROUP BY c.id
                ORDER BY c.name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$parentId]);
        return $stmt->fetchAll();
    }

    public function getCategoryPath(int $categoryId): array {
        $path = [];
        $currentId = $categoryId;

        while ($currentId) {
            $sql = "SELECT id, name, parent_id FROM categories WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$currentId]);
            $category = $stmt->fetch();

            if (!$category) {
                break;
            }

            array_unshift($path, $category);
            $currentId = $category['parent_id'];
        }

        return $path;
    }

    public function getCategoryTree(): array {
        // Obtener categorías principales (parent_id IS NULL)
        $sql = "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll();
        
        return array_map(function($category) {
            $category['children'] = $this->getSubcategories($category['id']);
            return $category;
        }, $categories);
    }

    public function getAccessories(int $categoryId): array {
        $sql = "SELECT a.* 
                FROM accessories a 
                WHERE a.category_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public function getRelatedProducts(int $categoryId, int $limit = 5): array {
        $sql = "SELECT DISTINCT p.*, COUNT(DISTINCT com.id) as comment_count
                FROM products p
                JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN comments com ON p.id = com.product_id
                WHERE pc.category_id = ?
                GROUP BY p.id
                ORDER BY RAND()
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId, $limit]);
        return $stmt->fetchAll();
    }

    public function getProductCount(int $categoryId): int {
        // Verificar si es una categoría principal (parent_id IS NULL)
        $categoryInfo = $this->find($categoryId);
        
        if ($categoryInfo && $categoryInfo['parent_id'] === null) {
            // Es categoría principal: contar productos de esta categoría Y sus subcategorías
            $sql = "SELECT COUNT(DISTINCT p.id) 
                    FROM products p 
                    JOIN product_categories pc ON p.id = pc.product_id 
                    JOIN categories c ON pc.category_id = c.id
                    WHERE c.id = ? OR c.parent_id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId, $categoryId]);
        } else {
            // Es subcategoría: contar solo productos directos
            $sql = "SELECT COUNT(DISTINCT p.id) 
                    FROM products p 
                    JOIN product_categories pc ON p.id = pc.product_id 
                    WHERE pc.category_id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId]);
        }
        
        return (int) $stmt->fetchColumn();
    }

    public function addProductToCategory(int $productId, int $categoryId): bool {
        $sql = "INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$productId, $categoryId]);
    }

    public function removeProductFromCategory(int $productId, int $categoryId): bool {
        $sql = "DELETE FROM product_categories WHERE product_id = ? AND category_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$productId, $categoryId]);
    }
}