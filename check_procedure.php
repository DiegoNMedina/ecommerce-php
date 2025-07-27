<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1:8889;dbname=ecommerce_php', 'root', 'root');
    
    // Verificar si el procedimiento existe
    $stmt = $pdo->query("SHOW PROCEDURE STATUS WHERE Name = 'calculate_installments'");
    $result = $stmt->fetchAll();
    
    if (empty($result)) {
        echo "PROCEDURE NOT FOUND\n";
        
        // Crear el procedimiento
        $createProcedure = "
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
        ";
        
        echo "Creating procedure...\n";
        $pdo->exec($createProcedure);
        echo "Procedure created successfully\n";
    } else {
        echo "PROCEDURE EXISTS\n";
        print_r($result);
    }
    
    // Probar el procedimiento
    echo "\nTesting procedure with price=1000, months=12:\n";
    $stmt = $pdo->prepare("CALL calculate_installments(?, ?, @monthly_payment)");
    $stmt->execute([1000, 12]);
    
    $result = $pdo->query("SELECT @monthly_payment as payment")->fetch();
    echo "Monthly payment: " . $result['payment'] . "\n";
    
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
?>