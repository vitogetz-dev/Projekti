<?php
    session_start();

    if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin')
    {
        header("Location: ../index.php");
        exit;
    }

    require '../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj']))
    {
        $naziv = $_POST['naziv'];
        $godina = $_POST['godina'] ?: null;
        $cijena = $_POST['cijena'];
        $zanrovi = $_POST['zanrovi'] ?? [];
        $slikaPath = null;

        // Upload slike
        if (!empty($_FILES['slika']['name'])) {
            $imeSlike = uniqid() . '_' . basename($_FILES['slika']['name']);
            $targetPath = '../img/' . $imeSlike;
            if (move_uploaded_file($_FILES['slika']['tmp_name'], $targetPath))
            {
                $slikaPath = 'img/' . $imeSlike;
            }
        }

        // Unos filma
        $stmt = $pdo->prepare("INSERT INTO filmovi (naziv, godina, cijena, slika) VALUES (?, ?, ?, ?)");
        $stmt->execute([$naziv, $godina, $cijena, $slikaPath]);

        $idFilma = $pdo->lastInsertId();

        // Povezivanje sa žanrovima
        $stmtZ = $pdo->prepare("INSERT INTO film_zanr (IDFilm, IDZanr) VALUES (?, ?)");
        foreach ($zanrovi as $zanrID) {
            $stmtZ->execute([$idFilma, $zanrID]);
        }

        header("Location: admin_panel.php");
        exit;
    } 
    else
    {
        header("Location: admin_panel.php");
        exit;
    }
?>