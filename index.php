<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Accueil</title>
    <link rel="stylesheet" href="index.css" />

</head>

<body>
    <nav class="navbar">

        <ul class="navlinks">
            <?php
            session_start();

            if (isset($_SESSION['login'])) {
                // Utilisateur connecté
            
                echo '<li class="projet"><a href="index.php">Accueil</a></li>';
                echo '<li class="projet"><a href="planning.php">Planning</a></li>';
                echo '<li class="projet"><a href="reservation-form.php">Réservation</a></li>';
                echo '<li class="projet"><a href="profil.php">Profil</a></li>';
                echo '<li><a href="logout.php">Déconnexion</a></li>';

            } else {
                // Aucun utilisateur connecté
                echo '<li class="projet"><a href="index.php">Accueil</a></li>';
                echo '<li class="projet"><a href="connexion.php">Connexion</a></li>';
                echo '<li class="projet"><a href="inscription.php">Inscription</a></li>';
            }
            ?>
        </ul>
        <div class="burger">
            <span></span>
        </div>
    </nav>


</body>

<script>
    const burger = document.querySelector(".burger");
    const navlinks = document.querySelector(".navlinks");
    const body = document.querySelector("body");

    burger.addEventListener("click", () => {
        navlinks.classList.toggle("mobile-menu");
        burger.classList.toggle("cross");
    });

    body.addEventListener("click", (event) => {
        if (!burger.contains(event.target) && !navlinks.contains(event.target)) {
            navlinks.classList.remove("mobile-menu");
            burger.classList.remove("cross");
        }
    });
</script>


</html>