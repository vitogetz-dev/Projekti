<?php
    $host = 'localhost';
    $dbname = 'videoteka'; // Ime baze koju ćeš kasnije kreirati
    $username = 'root';
    $password = ''; // Prazno ako koristiš XAMPP s default postavkama

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        // Omogući PDO iznimke za lakše debugiranje
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Greška pri spajanju na bazu: " . $e->getMessage());
    }
?>