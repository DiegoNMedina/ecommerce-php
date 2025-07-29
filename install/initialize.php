<?php

namespace Install;

require_once 'config.php';

class DatabaseInitializer {
    private \PDO $pdo;
    private array $brands = ['Dell', 'HP', 'Lenovo', 'Apple', 'Asus', 'Acer', 'MSI', 'Samsung'];
    private array $categories = [
        'Laptops' => ['Gaming', 'Empresariales', 'Estudiantes', 'Ultrabook'],
        'Computadoras de Escritorio' => ['PC Gaming', 'Estaciones de Trabajo', 'Todo en Uno', 'Mini PC'],
        'Componentes' => ['Procesadores', 'Tarjetas Gráficas', 'Memoria', 'Almacenamiento'],
        'Accesorios' => ['Monitores', 'Teclados', 'Ratones', 'Auriculares']
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
                    $price = rand(10000, 25000);
                    break;
                case 'mid':
                    $price = rand(25000, 45000);
                    break;
                case 'high':
                    $price = rand(45000, 60000);
                    break;
                default:
                    $price = rand(10000, 25000);
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
            "Excelente computadora, cumple con todas mis expectativas. La velocidad de procesamiento es increíble.",
            "Buena relación calidad-precio. Recomiendo esta marca sin dudarlo.",
            "El rendimiento es impresionante, especialmente para juegos y aplicaciones pesadas.",
            "Perfecta para gaming y trabajo. La pantalla tiene colores muy vivos.",
            "La batería dura mucho tiempo, ideal para trabajar fuera de casa.",
            "Muy satisfecho con la compra. El envío fue rápido y el empaque excelente.",
            "El diseño es elegante y moderno. Se ve muy profesional en la oficina.",
            "Recomiendo ampliamente este equipo. Vale cada peso invertido.",
            "La calidad de construcción es excelente. Se siente muy sólida y duradera.",
            "Funciona perfectamente para mis necesidades de diseño gráfico.",
            "Superó mis expectativas. El sistema operativo corre muy fluido.",
            "Excelente para programación. Compila proyectos grandes sin problemas.",
            "La memoria RAM es suficiente para multitarea intensiva.",
            "El almacenamiento SSD hace que todo sea súper rápido.",
            "Perfecta para edición de video. Renderiza sin lag.",
            "El teclado es muy cómodo para escribir durante horas.",
            "La conectividad es excelente, todos los puertos funcionan perfecto.",
            "Muy silenciosa, casi no se escucha el ventilador.",
            "La temperatura se mantiene controlada incluso con uso intensivo.",
            "Fácil de configurar, llegó lista para usar.",
            "El soporte técnico de la marca es muy bueno.",
            "Excelente para estudiantes, corre todos los programas necesarios.",
            "La webcam tiene buena calidad para videollamadas.",
            "Los altavoces suenan mejor de lo esperado.",
            "Muy portable, perfecta para llevar a todas partes.",
            "La pantalla no refleja, se puede usar en cualquier lugar.",
            "Arranque súper rápido, en segundos está lista.",
            "Perfecta para streaming, no se cuelga ni se ralentiza.",
            "La tarjeta gráfica maneja juegos en alta calidad.",
            "Excelente inversión, durará muchos años.",
            "El precio es justo para las especificaciones que ofrece.",
            "Muy intuitiva de usar, ideal para principiantes.",
            "La garantía me da mucha tranquilidad.",
            "Llegó en perfectas condiciones, muy bien empacada.",
            "El manual de usuario es claro y fácil de entender.",
            "Perfecta para trabajar con hojas de cálculo complejas.",
            "Los juegos se ven espectaculares en esta pantalla.",
            "Muy eficiente energéticamente, consume poco.",
            "La actualización de drivers fue automática y sin problemas.",
            "Excelente para videoconferencias profesionales."
        ];

        $products = $this->pdo->query("SELECT id FROM products")->fetchAll(\PDO::FETCH_COLUMN);
        $totalComments = 0;
        $targetComments = 10000;

        // Asegurar mínimo 2 comentarios por producto
        foreach ($products as $productId) {
            $minComments = 2;
            for ($i = 0; $i < $minComments; $i++) {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO comments (product_id, name, comment, rating) VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([
                    $productId,
                    "Usuario" . rand(1000, 9999),
                    $comments[array_rand($comments)],
                    rand(3, 5)
                ]);
                $totalComments++;
            }
        }

        // Agregar comentarios adicionales hasta llegar a 10,000
        while ($totalComments < $targetComments) {
            $randomProductId = $products[array_rand($products)];
            $stmt = $this->pdo->prepare(
                "INSERT INTO comments (product_id, name, comment, rating) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $randomProductId,
                "Usuario" . rand(1000, 9999),
                $comments[array_rand($comments)],
                rand(1, 5) // Rango completo de calificaciones
            ]);
            $totalComments++;
        }

        echo "Total de comentarios creados: {$totalComments}\n";
    }

    private function createAccessories(): void {
        $accessories = [
            'Monitores' => [
                ['name' => 'Monitor 24" FHD', 'price' => 3500],
                ['name' => 'Monitor 27" 4K', 'price' => 7000],
                ['name' => 'Monitor 32" Gaming', 'price' => 8500]
            ],
            'Teclados' => [
                ['name' => 'Teclado Mecánico RGB', 'price' => 1500],
                ['name' => 'Teclado Inalámbrico', 'price' => 850],
                ['name' => 'Teclado Gaming', 'price' => 2200]
            ],
            'Ratones' => [
                ['name' => 'Mouse Gaming', 'price' => 1000],
                ['name' => 'Mouse Inalámbrico', 'price' => 500],
                ['name' => 'Mouse Ergonómico', 'price' => 1350]
            ],
            'Auriculares' => [
                ['name' => 'Auriculares Gaming', 'price' => 1700],
                ['name' => 'Auriculares Bluetooth', 'price' => 1350],
                ['name' => 'Auriculares con Micrófono', 'price' => 1200]
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