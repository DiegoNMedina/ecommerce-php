-- Script para agregar seguimiento de ventas al sistema existente
-- Ejecutar después de la instalación inicial

USE ecommerce_computers;

-- Agregar campo de ventas a la tabla products
ALTER TABLE products ADD COLUMN sales_count INT NOT NULL DEFAULT 0 AFTER likes;

-- Crear tabla de órdenes/ventas (opcional para un sistema más completo)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Crear tabla de items de órdenes
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

-- Insertar datos de prueba para simular ventas
-- Actualizar sales_count de algunos productos aleatoriamente
UPDATE products SET sales_count = FLOOR(RAND() * 100) + 1 WHERE id <= 50;
UPDATE products SET sales_count = FLOOR(RAND() * 50) + 1 WHERE id > 50 AND id <= 100;
UPDATE products SET sales_count = FLOOR(RAND() * 25) + 1 WHERE id > 100 AND id <= 200;

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