<?php
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }
?>

<nav>
    <label class="logo">
        Videoteka
    </label>

    <ul class="nav-links">
        <li>
            <a href="/PHP/Projekt_2_navigacija/index.php">
                Početna
            </a>
        </li>

        <li>
            <a href="#">
                Kontakt
            </a>
        </li>

        <li>
            <a href="#">
                O nama
            </a>
        </li>

        <?php
            if (isset($_SESSION['uloga']) && $_SESSION['uloga'] === 'admin'):
        ?>

        <li>
            <a href="/PHP/Projekt_2_navigacija/admin/admin_panel.php">
                Admin Panel
            </a>
        </li>

        <?php 
            endif; 
        ?>

        <?php 
            if (isset($_SESSION['username'])): 
        ?>

        <li>
            <a href="/PHP/Projekt_2_navigacija/auth/logout.php" class="logout-link">
                Odjava
            </a>
        </li>

        <?php else: ?>

            <li>
                <a href="/PHP/Projekt_2_navigacija/auth/login.php" class="btn btn-success">
                    Prijava
                </a>
            </li>

        <?php endif; ?>
    </ul>
</nav>