<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Пробуем разные варианты хоста
    $hosts = ['db', 'postgres', 'database', 'rentcar-db', 'localhost'];
    $connected = false;
    
    foreach ($hosts as $host) {
        try {
            $pdo = new PDO(
                "pgsql:host=$host;dbname=rentcar_db",
                'rentcar',
                'rentcar'
            );
            echo "Connected successfully to host: $host\n";
            $connected = true;
            break;
        } catch (PDOException $e) {
            echo "Failed to connect to $host: " . $e->getMessage() . "\n";
        }
    }
    
    if (!$connected) {
        throw new Exception('Could not connect to any database host');
    }
    
    // Проверка таблиц
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\nTables: " . implode(', ', $tables) . "\n";
    
    // Проверка данных
    if (in_array('locations', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM locations");
        $count = $stmt->fetchColumn();
        echo "Locations count: " . $count . "\n";
    }
    
    if (in_array('cities', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM cities");
        $count = $stmt->fetchColumn();
        echo "Cities count: " . $count . "\n";
    }
    
    // Проверка API через curl
    echo "\nTesting API...\n";
    $ch = curl_init('http://localhost/api/v1/locations/frontend');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode === 0) {
        echo "CURL Error: " . $error . "\n";
    } else {
        echo "HTTP Code: " . $httpCode . "\n";
        if ($httpCode === 200) {
            echo "Response: " . substr($response, 0, 500) . "\n";
        } else {
            echo "Error Response: " . $response . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
