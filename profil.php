<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["connected"]) || $_SESSION["connected"] !== true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: connexion.php");
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

// Récupération des informations de l'utilisateur depuis la base de données
$login = $_SESSION["login"];
$sql = "SELECT * FROM utilisateurs WHERE login = '$login'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $login = $row["login"];
} else {
    echo "Erreur : impossible de récupérer les informations de l'utilisateur.";
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des nouvelles valeurs du formulaire
    $nouveaulogin = $_POST["login"];
    $nouveaupassword = $_POST["password"];
    $nouveaunewpassword = $_POST["new_password"];
    $nouveauconfirmpassword = $_POST["confirm_password"];

    // Vérification du mot de passe actuel
    if ($nouveaupassword !== $row["password"]) {
        $message = "Mot de passe actuel incorrect.";
    } else {
        // Vérification que le nouveau mot de passe et la confirmation correspondent
        if ($nouveaunewpassword !== $nouveauconfirmpassword) {
            $message = "Le nouveau mot de passe et la confirmation ne correspondent pas.";
        } else {
            // Mise à jour des informations dans la base de données
            $sql = "UPDATE utilisateurs SET login = '$nouveaulogin', password = '$nouveaunewpassword' WHERE login = '$login'";

            if ($conn->query($sql) === TRUE) {
                $message = "Informations mises à jour avec succès.";
                // Mettre à jour les valeurs dans la session
                $_SESSION["login"] = $nouveaulogin;
                $_SESSION["password"] = $nouveaunewpassword;
            } else {
                $message = "Erreur lors de la mise à jour des informations : " . $conn->error;
            }
        }
    }
}

// Fermeture de la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Accueil</title>
    <link rel="stylesheet" href="profil.css" />
    <link rel="shortcut icon" href="./images/shortcut_icon.png" />
</head>

<body>
    <nav class="navbar">
        <ul class="navlinks">
            <?php
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
        <form class="login" action="profil.php" method="post">

            <?php if (isset($message)) { ?>
                <p>
                    <?php echo $message; ?>
                </p>
            <?php } ?>
            <label for="login">Login</label>
            <input type="text" id="login" name="login" value="<?php echo $login; ?>" required>
            <label for="password">Mot de passe actuel</label>
            <input type="password" id="password" name="password" value="" required>
            <label for="new-password">Nouveau mot de passe</label>
            <input type="password" id="new-password" name="new_password" required>
            <label for="confirm-password">Confirmer le mot de passe</label>
            <input type="password" id="confirm-password" name="confirm_password" required>
            <button class="eye" type="button" onclick="togglePassword()" id="toggle-password"><img id="eye"
                    src="./images/eye-open.svg" alt="" /></button>
            <div>
                <input type="submit" name="valider" value="Valider" />
                <input type="reset" name"effacer" value="Effacer">
            </div>
        </form>
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

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const newPasswordInput = document.getElementById("new-password");
            const confirmPasswordInput = document.getElementById("confirm-password");
            const togglePasswordButton = document.getElementById("toggle-password");
            const img = togglePasswordButton.querySelector("img");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                newPasswordInput.type = "text";
                confirmPasswordInput.type = "text";
                img.src = "./images/eye-closed.svg";
            } else {
                passwordInput.type = "password";
                newPasswordInput.type = "password";
                confirmPasswordInput.type = "password";
                img.src = "./images/eye-open.svg";
            }
        }

    </script>
</body>

</html>