<?php
// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs du formulaire
    $login = isset($_POST['login']) ? $_POST['login'] : '';
    $form_password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_check = isset($_POST['password_check']) ? $_POST['password_check'] : '';

    // Vérifier si les mots de passe correspondent
    if ($form_password !== $password_check) {
        echo "Les mots de passe ne correspondent pas.";
        // Vous pouvez également rediriger l'utilisateur vers une page d'erreur ou afficher un message d'erreur approprié.
        exit;
    }

    // Connexion à la base de données
    $servername = "localhost"; // Remplacez par l'adresse de votre serveur de base de données
    $username = "root"; // Remplacez par votre nom d'utilisateur de la base de données
    $password = "root"; // Remplacez par votre mot de passe de la base de données
    $dbname = "reservationsalles"; // Remplacez par le nom de votre base de données

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion à la base de données
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    // Vérifier si l'utilisateur existe déjà avec le même login
    $existing_user_query = "SELECT * FROM utilisateurs WHERE login='$login'";
    $existing_user_result = $conn->query($existing_user_query);
    if ($existing_user_result->num_rows > 0) {
        echo "Un utilisateur avec le même login existe déjà.";
        exit;
    }

    // Préparer et exécuter la requête d'insertion
    $sql = "INSERT INTO utilisateurs (login, password) VALUES ('$login', '$form_password')";

    if ($conn->query($sql) === TRUE) {
        echo "Les données ont été ajoutées avec succès à la base de données.";

        // Redirection vers la page de connexion
        header("Location: connexion.php");
        exit;
    } else {
        echo "Une erreur s'est produite lors de l'ajout des données à la base de données : " . $conn->error;
    }

    // Fermer la connexion à la base de données
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription</title>
    <link rel="stylesheet" href="inscription.css" />
    <link rel="shortcut icon" href="./images/shortcut_icon.png" />
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
                echo '<li class="projet"><a href="reservation.php">Réservation</a></li>';
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
        <form class="login" action="inscription.php" method="post">
            <h1>Formulaire d'inscription</h1>
            <input type="text" id="login" name="login" placeholder="Entrer votre identifiant" required />
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="Entrer votre mot de passe" required />
            <label for="password_check">Confirmer MDP</label>
            <input type="password" id="password_check" name="password_check" placeholder="Confirmer MDP" required />
            <button class="eye" type="button" onclick="togglePassword()" id="toggle-password"><img
                    src="./images/eye-open.svg" alt="eye" height="30" /></button>
            <li class="options">
                <input type="submit" name="valider" value="Valider &#10004;" />
                <input type="reset" name="reset" value="Effacer &#10005;" />
            </li>
        </form>
    </section>
</body>
<script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const passwordCheckInput = document.getElementById("password_check");
        const togglePasswordButton = document.getElementById("toggle-password");
        const img = togglePasswordButton.querySelector("img");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordCheckInput.type = "text";
            img.src = "./images/eye-closed.svg";
        } else {
            passwordInput.type = "password";
            passwordCheckInput.type = "password";
            img.src = "./images/eye-open.svg";
        }
    }
</script>
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