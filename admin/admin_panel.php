<?php
    session_start();
    if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin')
    {
        header("Location: ../index.php");
        exit;
    }

    require '../includes/db.php';
    $poruka = '';

    // ažuriranje filma
    if (isset($_POST['azuriraj']))
    {
        $id = $_POST['film_id'];
        $naziv = $_POST['naziv'];
        $godina = $_POST['godina'] ?: null;
        $cijena = $_POST['cijena'];
        $zanrovi = $_POST['zanrovi'] ?? [];

        $slika = $_POST['stara_slika'];
        if (!empty($_FILES['slika']['name']))
        {
            $imeSlike = uniqid() . '_' . basename($_FILES['slika']['name']);
            $target = '../img/' . $imeSlike;
            if (move_uploaded_file($_FILES['slika']['tmp_name'], $target))
            {
                $slika = 'img/' . $imeSlike;
            }
        }

        $stmt = $pdo->prepare("UPDATE filmovi SET naziv=?, godina=?, cijena=?, slika=? WHERE IDFilm=?");
        $stmt->execute([$naziv, $godina, $cijena, $slika, $id]);

        $pdo->prepare("DELETE FROM film_zanr WHERE IDFilm = ?")->execute([$id]);
        foreach ($zanrovi as $zanrID)
        {
            $pdo->prepare("INSERT INTO film_zanr (IDFilm, IDZanr) VALUES (?, ?)")->execute([$id, $zanrID]);
        }

        $poruka = "Film ažuriran.";
    }

    // brisanje filma
    if (isset($_GET['obrisi']))
    {
        $id = $_GET['obrisi'];
        $pdo->prepare("DELETE FROM filmovi WHERE IDFilm = ?")->execute([$id]);
        $poruka = "Film obrisan.";
    }

    $filmovi = $pdo->query("SELECT * FROM filmovi")->fetchAll();
    $zanrovi = $pdo->query("SELECT * FROM zanrovi ORDER BY naziv")->fetchAll();
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <meta charset="UTF-8">
        <title>Admin Panel</title>
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body>
        <?php 
            include '../includes/nav.php'; 
        ?>

        <div class="container">
            <h2>Admin Panel</h2>

            <?php if ($poruka): ?>
                <p style="color:green;"><?= $poruka ?></p>
            <?php endif; ?>

            <h3>Dodaj novi film</h3>

            <form method="post" enctype="multipart/form-data" action="dodaj_film.php" style="border:1px solid #ccc; padding:10px; margin-bottom:30px;">
                <label>Naziv:</label>
                <input type="text" name="naziv" required><br><br>

                <label>Godina:</label>
                <input type="number" name="godina"><br><br>

                <label>Cijena (€):</label>
                <input type="number" step="0.01" name="cijena" required><br><br>

                <label>Slika filma:</label>
                <input type="file" name="slika" accept="image/*"><br><br>

                <label>Žanrovi:</label><br>
                <?php foreach ($zanrovi as $zanr): ?>
                    <label>
                        <input type="checkbox" name="zanrovi[]" value="<?= $zanr['IDZanr'] ?>">
                        <?= htmlspecialchars($zanr['naziv']) ?>
                    </label><br>
                <?php endforeach; ?>
                <br>

                <button type="submit" name="dodaj">Dodaj film</button>
            </form>

            <h3>Uredi / Obriši filmove</h3>

            <?php foreach ($filmovi as $film):
                $filmZanrovi = $pdo->prepare("SELECT IDZanr FROM film_zanr WHERE IDFilm = ?");
                $filmZanrovi->execute([$film['IDFilm']]);
                $odabraniZanrovi = $filmZanrovi->fetchAll(PDO::FETCH_COLUMN);
            ?>

            <form method="post" enctype="multipart/form-data" style="border:1px solid #ccc; padding:10px; margin-bottom:20px;">
                <input type="hidden" name="film_id" value="<?= $film['IDFilm'] ?>">
                <input type="hidden" name="stara_slika" value="<?= $film['slika'] ?>">

                <label>Naziv:</label>
                <input type="text" name="naziv" value="<?= htmlspecialchars($film['naziv']) ?>" required><br><br>

                <label>Godina:</label>
                <input type="number" name="godina" value="<?= $film['godina'] ?>"><br><br>

                <label>Cijena (€):</label>
                <input type="number" step="0.01" name="cijena" value="<?= $film['cijena'] ?>" required><br><br>

                <label>Trenutna slika:</label><br>
                <?php if ($film['slika']): ?>
                    <img src="../<?= $film['slika'] ?>" style="max-width:150px;"><br>
                <?php endif; ?>
                <input type="file" name="slika" accept="image/*"><br><br>

                <label>Žanrovi:</label><br>
                <?php foreach ($zanrovi as $zanr): ?>
                    <label>
                        <input type="checkbox" name="zanrovi[]" value="<?= $zanr['IDZanr'] ?>" <?= in_array($zanr['IDZanr'], $odabraniZanrovi) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($zanr['naziv']) ?>
                    </label><br>
                <?php endforeach; ?>
                <br>

                <button type="submit" name="azuriraj">Spremi izmjene</button>
                <a href="admin_panel.php?obrisi=<?= $film['IDFilm'] ?>" onclick="return confirm('Obrisati film?')" style="color:red; margin-left:15px;">Obriši</a>
            </form>

            <?php endforeach; ?>
        </div>
    </body>
</html>