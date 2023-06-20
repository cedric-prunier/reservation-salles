<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de salle</title>
    <link rel="stylesheet" href="reservation-form.css">
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
        <form class="login" action="reservation-form.php" method="POST">

            <label for="titre">Titre :</label>
            <input type="text" id="titre" name="titre" required>
            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="4" cols="20" required></textarea><br>
            <label for="debut">Heure de début :</label>
            <select id="debut" name="debut" required>
                <option value="" disabled selected></option>
                <option selected value="08:00">08:00</option>
                <option value="09:00">09:00</option>
                <option value="10:00">10:00</option>
                <option value="11:00">11:00</option>
                <option value="12:00">12:00</option>
                <option value="13:00">13:00</option>
                <option value="14:00">14:00</option>
                <option value="15:00">15:00</option>
                <option value="16:00">16:00</option>
                <option value="17:00">17:00</option>
                <option value="18:00">18:00</option>
                <option value="19:00">19:00</option>
            </select><br>
            <label for="fin">Heure de fin :</label>

            <select id="fin" name="fin" required>
                <option value="" disabled selected></option>
                <option selected value="09:00">09:00</option>
                <option value="10:00">10:00</option>
                <option value="11:00">11:00</option>
                <option value="12:00">12:00</option>
                <option value="13:00">13:00</option>
                <option value="14:00">14:00</option>
                <option value="15:00">15:00</option>
                <option value="16:00">16:00</option>
                <option value="17:00">17:00</option>
                <option value="18:00">18:00</option>
                <option value="19:00">19:00</option>
            </select><br>
            <label for="date">Date de réservation :</label>
            <input type="date" id="date" name="date" required><br>
            <?php


            // Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['id_utilisateur'])) {
                // Rediriger vers la page de connexion ou afficher un message d'erreur
                header("Location: login.php");
                exit();
            }

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

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Vérifier si la date sélectionnée est un samedi ou un dimanche
                $selectedDate = $_POST["date"];
                $dayOfWeek = date('N', strtotime($selectedDate)); // Renvoie 6 pour le samedi et 7 pour le dimanche
            
                if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                    echo "Les réservations ne sont pas autorisées le week-end.";
                } else {
                    // Le jour sélectionné est valide, continuer avec le traitement de la réservation
            
                    $titre = $_POST["titre"];
                    $description = $_POST["description"];
                    $dateDebut = $_POST["date"] . " " . $_POST["debut"];
                    $dateFin = $_POST["date"] . " " . $_POST["fin"];
                    $idUtilisateur = $_SESSION['id_utilisateur'];

                    // Vérifier si une réservation existe déjà pour ce créneau horaire
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE (debut <= ? AND fin >= ?) OR (debut >= ? AND fin <= ?) OR (debut <= ? AND fin >= ?)");
                    $stmt->bind_param("ssssss", $dateDebut, $dateDebut, $dateDebut, $dateFin, $dateFin, $dateFin);
                    $stmt->execute();
                    $stmt->bind_result($count);
                    $stmt->fetch();
                    $stmt->close();

                    if ($count > 0) {
                        echo "Impossible de réserver sur ce créneau horaire, veuillez choisir une autre période.";
                    } else {
                        // Insérer la réservation dans la base de données
                        $stmt = $conn->prepare("INSERT INTO reservations (titre, description, debut, fin, id_utilisateur) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssssi", $titre, $description, $dateDebut, $dateFin, $idUtilisateur);
                        $stmt->execute();

                        if ($stmt->affected_rows > 0) {
                            echo "Réservation enregistrée avec succès.";
                        } else {
                            echo "Erreur lors de l'enregistrement de la réservation : " . $stmt->error;
                        }

                        $stmt->close();
                    }
                }
            }

            // Fermeture de la connexion à la base de données
            $conn->close();
            ?>

            <div>
                <input type="submit" value="Réserver">
                <input type="reset" value="Réinitialiser">
            </div>
        </form>
    </section>

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