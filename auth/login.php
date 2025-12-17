<?php
    session_start();
    require '../includes/db.php';

    $poruka = '';

    if ($_SERVER["REQUEST_METHOD"] === "POST")
    {
        $korisnik = $_POST['username'];
        $lozinka = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM korisnik WHERE Username = ?");
        $stmt->execute([$korisnik]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        /*
            echo "Upisana lozinka: " . $lozinka . "<br>";
            echo "SHA256 hash upisane: " . hash('sha256', $lozinka) . "<br>";
            echo "Hash iz baze: " . $user['lozinka'] . "<br>";
        */

        if ($user && hash('sha256', $lozinka) === $user['lozinka'])
        {
            $_SESSION['username'] = $korisnik;
            $_SESSION['uloga'] = $user['uloga'];
            header("Location: ../index.php");
            exit;
        } 
        else 
        {
            $poruka = "Neispravno korisničko ime ili lozinka.";
        }
    }
?>

<!DOCTYPE html>
    <html lang="hr">
    <head>
        <meta charset="UTF-8">
        <title>Prijava</title>
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body>
        <?php 
            include '../includes/nav.php';
        ?>

        <div class="login-container">
            <h2>
                Prijavi se
            </h2>

            <form method="post">
                <input type="text" name="username" placeholder="Korisničko ime" required>
                <input type="password" name="password" placeholder="Lozinka" required>
                <button type="submit">
                    Prijava
                </button>

                <?php 
                    if ($poruka):
                ?>

                <p style="color:red;">
                    <?php 
                        echo $poruka; 
                    ?>
                </p>
                
                <?php endif; ?>
            </form>
        </div>
    </body>
</html>