-- Script completo de base de datos con seguimiento de ventas integrado
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS ecommerce_computers;
USE ecommerce_computers;

-- Tabla de categorías
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Tabla de productos (con sales_count incluido desde el inicio)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model VARCHAR(100) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    specifications TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    visits INT NOT NULL DEFAULT 0,
    likes INT NOT NULL DEFAULT 0,
    sales_count INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de relación productos-categorías
CREATE TABLE product_categories (
    product_id INT,
    category_id INT,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Tabla de accesorios
CREATE TABLE accessories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Tabla de comentarios
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    name VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Tabla de órdenes/ventas
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de items de órdenes
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Procedimiento almacenado para calcular mensualidades
DELIMITER //
CREATE PROCEDURE calculate_installments(
    IN product_price DECIMAL(10,2),
    IN months INT,
    OUT monthly_payment DECIMAL(10,2)
)
BEGIN
    DECLARE annual_interest DECIMAL(5,2) DEFAULT 10.0;
    DECLARE monthly_interest DECIMAL(10,4);
    
    SET monthly_interest = (annual_interest / 12) / 100;
    SET monthly_payment = (product_price * monthly_interest * POW(1 + monthly_interest, months)) /
                         (POW(1 + monthly_interest, months) - 1);
END //
DELIMITER ;

-- Trigger para actualizar sales_count cuando se agrega un item a una orden
DELIMITER //
CREATE TRIGGER update_sales_count AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products 
    SET sales_count = sales_count + NEW.quantity 
    WHERE id = NEW.product_id;
END //
DELIMITER ;

-- Vista para productos aleatorios con mensualidades
CREATE VIEW random_products_installments AS
SELECT 
    p.*,
    p.price / 6 as monthly_6,
    p.price / 12 as monthly_12
FROM products p
ORDER BY RAND()
LIMIT 10;

-- Vista para productos más vendidos
CREATE VIEW best_selling_products AS
SELECT 
    p.*,
    p.price / 6 as monthly_6,
    p.price / 12 as monthly_12,
    COUNT(DISTINCT c.id) as comment_count,
    AVG(c.rating) as avg_rating
FROM products p
LEFT JOIN comments c ON p.id = c.product_id
WHERE p.sales_count > 0
GROUP BY p.id
ORDER BY p.sales_count DESC, p.visits DESC;

-- Datos de prueba para simular ventas (se ejecutarán después de que se inserten productos)
-- Nota: Estos UPDATE se deben ejecutar después de poblar la base de datos con productos
-- UPDATE products SET sales_count = FLOOR(RAND() * 100) + 1 WHERE id <= 50;
-- UPDATE products SET sales_count = FLOOR(RAND() * 50) + 1 WHERE id > 50 AND id <= 100;
-- UPDATE products SET sales_count = FLOOR(RAND() * 25) + 1 WHERE id > 100 AND id <= 200;