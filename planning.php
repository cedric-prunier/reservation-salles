<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning</title>
    <link rel="stylesheet" href="planning.css">
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

    <section>
        <?php
        // Connexion à la base de données (à adapter avec vos propres informations)
        $servername = "localhost";
        $username = "root";
        $password = "root";
        $dbname = "reservationsalles";

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Vérification de la connexion
        if ($conn->connect_error) {
            die("Erreur de connexion à la base de données : " . $conn->connect_error);
        }

        // Obtenir la date de début de la semaine en cours
        $today = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('last Monday', strtotime($today)));

        // Obtenir la date de fin de la semaine en cours
        $endDate = date('Y-m-d', strtotime('next Sunday', strtotime($today)));

        // Requête pour récupérer les réservations pour la semaine en cours
        $sql = "SELECT * FROM reservations WHERE debut BETWEEN '$startDate' AND '$endDate'";
        $result = $conn->query($sql);

        // Création du tableau du planning
        $daysOfWeek = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $timesOfDay = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];

        // Création des en-têtes du tableau
        echo '<table class="planning-table">';
        echo '<tr><th></th>';
        foreach ($daysOfWeek as $day) {
            echo '<th>' . $day . '</th>';
        }
        echo '</tr>';

        // Création des lignes du tableau
        foreach ($timesOfDay as $time) {
            echo '<tr>';
            echo '<td>' . $time . '</td>';

            foreach ($daysOfWeek as $index => $day) {
                echo '<td';

                // Appliquer une classe CSS conditionnellement pour les samedis et dimanches
                if ($index >= 5) {
                    echo ' class="weekend"';
                }

                echo '>';

                // Parcourir les réservations pour afficher les informations
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $reservationDate = date('Y-m-d', strtotime($row['debut']));
                        $reservationTime = date('H:i', strtotime($row['debut']));
                        $reservationEndTime = date('H:i', strtotime($row['fin']));
                        $reservationTitle = $row['titre'];

                        if ($reservationDate == $day && $reservationTime == $time) {
                            echo 'Titre : ' . $reservationTitle . '<br>';
                            echo '<br>';
                        }
                    }
                    // Réinitialiser le pointeur de résultats à la première ligne
                    $result->data_seek(0);
                }

                echo '</td>';
            }

            echo '</tr>';
        }

        echo '</table>';

        // Fermeture de la connexion à la base de données
        $conn->close();
        ?>

    </section>

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
</body>

</html>