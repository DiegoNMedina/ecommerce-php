<?php

namespace Install;

require_once 'config.php';

class DatabaseInitializer {
    private \PDO $pdo;
    private array $brands = ['Dell', 'HP', 'Lenovo', 'Apple', 'Asus', 'Acer', 'MSI', 'Samsung'];
    private array $categories = [
        'Laptops' => ['Gaming', 'Business', 'Student', 'Ultrabook'],
        'Desktops' => ['Gaming PC', 'Workstation', 'All-in-One', 'Mini PC'],
        'Components' => ['Processors', 'Graphics Cards', 'Memory', 'Storage'],
        'Accessories' => ['Monitors', 'Keyboards', 'Mice', 'Headsets']
    ];

    public function __construct() {
        try {
            $this->pdo = new \PDO(
                Config::getDsn(),
                Config::DB_USER,
                Config::DB_PASS,
                Config::getPdoOptions()
            );
        } catch (\PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function initialize(): void {
        $this->createCategories();
        $this->createProducts();
        $this->createComments();
        $this->createAccessories();
        echo "Database initialized successfully!\n";
    }

    private function createCategories(): void {
        foreach ($this->categories as $mainCategory => $subCategories) {
            $stmt = $this->pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$mainCategory]);
            $parentId = $this->pdo->lastInsertId();

            foreach ($subCategories as $subCategory) {
                $stmt = $this->pdo->prepare("INSERT INTO categories (name, parent_id) VALUES (?, ?)");
                $stmt->execute([$subCategory, $parentId]);
            }
        }
    }

    private function createProducts(): void {
        $specs = [
            'low' => [
                'CPU' => ['Intel i3', 'AMD Ryzen 3'],
                'RAM' => ['8GB DDR4'],
                'Storage' => ['256GB SSD', '1TB HDD'],
                'Display' => ['15.6" HD']
            ],
            'mid' => [
                'CPU' => ['Intel i5', 'AMD Ryzen 5'],
                'RAM' => ['16GB DDR4'],
                'Storage' => ['512GB SSD', '1TB SSD'],
                'Display' => ['15.6" FHD']
            ],
            'high' => [
                'CPU' => ['Intel i7', 'AMD Ryzen 7'],
                'RAM' => ['32GB DDR4'],
                'Storage' => ['1TB NVMe SSD'],
                'Display' => ['15.6" 4K']
            ]
        ];

        for ($i = 0; $i < 2000; $i++) {
            $brand = $this->brands[array_rand($this->brands)];
            $tier = array_rand($specs);
            switch($tier) {
                case 'low':
                    $price = rand(30000, 50000) / 100;
                    break;
                case 'mid':
                    $price = rand(50000, 80000) / 100;
                    break;
                case 'high':
                    $price = rand(80000, 150000) / 100;
                    break;
                default:
                    $price = rand(30000, 50000) / 100;
            }

            $specList = $specs[$tier];
            $specifications = json_encode([
                'CPU' => $specList['CPU'][array_rand($specList['CPU'])],
                'RAM' => $specList['RAM'][array_rand($specList['RAM'])],
                'Storage' => $specList['Storage'][array_rand($specList['Storage'])],
                'Display' => $specList['Display'][array_rand($specList['Display'])]
            ]);

            $stmt = $this->pdo->prepare(
                "INSERT INTO products (model, brand, specifications, price, stock) 
                VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                "Model-" . uniqid(),
                $brand,
                $specifications,
                $price,
                rand(0, 100)
            ]);

            $productId = $this->pdo->lastInsertId();
            $this->assignCategories($productId);
        }
    }

    private function assignCategories(int $productId): void {
        $categoryIds = $this->pdo->query("SELECT id FROM categories WHERE parent_id IS NOT NULL")
            ->fetchAll(\PDO::FETCH_COLUMN);
        
        $selectedCategories = array_rand(array_flip($categoryIds), 3);
        foreach ($selectedCategories as $categoryId) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)"
            );
            $stmt->execute([$productId, $categoryId]);
        }
    }

    private function createComments(): void {
        $comments = [
            "Excelente computadora, cumple con todas mis expectativas.",
            "Buena relación calidad-precio.",
            "El rendimiento es impresionante.",
            "Perfecta para gaming y trabajo.",
            "La batería dura mucho tiempo.",
            "Muy satisfecho con la compra.",
            "El diseño es elegante y moderno.",
            "Recomiendo ampliamente este equipo.",
            "La calidad de construcción es excelente.",
            "Funciona perfectamente para mis necesidades."
        ];

        $products = $this->pdo->query("SELECT id FROM products")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($products as $productId) {
            $numComments = rand(2, 5);
            for ($i = 0; $i < $numComments; $i++) {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO comments (product_id, name, comment, rating) VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([
                    $productId,
                    "Usuario" . rand(1000, 9999),
                    $comments[array_rand($comments)],
                    rand(3, 5)
                ]);
            }
        }
    }

    private function createAccessories(): void {
        $accessories = [
            'Monitors' => [
                ['name' => 'Monitor 24" FHD', 'price' => 199.99],
                ['name' => 'Monitor 27" 4K', 'price' => 399.99],
                ['name' => 'Monitor 32" Gaming', 'price' => 499.99]
            ],
            'Keyboards' => [
                ['name' => 'Teclado Mecánico RGB', 'price' => 89.99],
                ['name' => 'Teclado Inalámbrico', 'price' => 49.99],
                ['name' => 'Teclado Gaming', 'price' => 129.99]
            ],
            'Mice' => [
                ['name' => 'Mouse Gaming', 'price' => 59.99],
                ['name' => 'Mouse Inalámbrico', 'price' => 29.99],
                ['name' => 'Mouse Ergonómico', 'price' => 79.99]
            ],
            'Headsets' => [
                ['name' => 'Auriculares Gaming', 'price' => 99.99],
                ['name' => 'Auriculares Bluetooth', 'price' => 79.99],
                ['name' => 'Auriculares con Micrófono', 'price' => 69.99]
            ]
        ];

        $categoryStmt = $this->pdo->prepare("SELECT id FROM categories WHERE name = ?");

        foreach ($accessories as $category => $items) {
            $categoryStmt->execute([$category]);
            $categoryId = $categoryStmt->fetchColumn();

            foreach ($items as $item) {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO accessories (name, price, category_id, description) 
                    VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([
                    $item['name'],
                    $item['price'],
                    $categoryId,
                    "Descripción detallada de " . $item['name']
                ]);
            }
        }
    }
}

// Run the initializer
$initializer = new DatabaseInitializer();
$initializer->initialize();