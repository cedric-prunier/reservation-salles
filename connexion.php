<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Connexion</title>
    <link rel="stylesheet" href="connexion.css" />
</head>

<body>

    <form class="login" action="connexion.php" method="post">
        <h1>Connexion</h1>
        <br>
        <?php
        session_start();

        // Vérifier si l'utilisateur est déjà connecté
        if (isset($_SESSION["connected"]) && $_SESSION["connected"] === true) {
            // Rediriger vers la page d'accueil si l'utilisateur est déjà connecté
            header("Location: index.php");
            exit();
        }

        // Traitement du formulaire de connexion
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Récupération des valeurs du formulaire
            $login = $_POST["login"];
            $password = $_POST["password"];

            // Connexion à la base de données (à adapter avec vos propres informations)
            $servername = "localhost";
            $username = "root";
            $dbpassword = "root";
            $dbname = "reservationsalles";

            $conn = new mysqli($servername, $username, $dbpassword, $dbname);

            // Vérification de la connexion
            if ($conn->connect_error) {
                die("Erreur de connexion à la base de données : " . $conn->connect_error);
            }

            // Préparation de la requête d'authentification de l'utilisateur
            $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE login = ? AND password = ?");
            $stmt->bind_param("ss", $login, $password);
            $stmt->execute();
            $stmt->store_result();

            // Vérification si un utilisateur correspondant a été trouvé
            if ($stmt->num_rows == 1) {
                // Utilisateur trouvé, création des variables de session
                $_SESSION["login"] = $login;
                $_SESSION["connected"] = true;

                // Récupération de l'ID de l'utilisateur
                $stmt->bind_result($id_utilisateur);
                $stmt->fetch();
                $_SESSION["id_utilisateur"] = $id_utilisateur;

                // Redirection vers une page sécurisée (par exemple, index.php)
                header("Location: index.php");
                exit();
            } else {
                // Aucun utilisateur correspondant trouvé, affichage d'un message d'erreur
                $error_message = "Identifiants invalides.";
            }

            // Fermeture de la connexion à la base de données
            $stmt->close();
            $conn->close();
        }
        ?>
        <h2>
            <?php if (isset($error_message))
                echo $error_message; ?>
        </h2>
        <div class="identifiant">
            <div class="input">
                <input type="text" name="login" placeholder="Login" required />
                <input type="password" name="password" placeholder="Mot de passe" id="password" required />
            </div>
            <button class="eye" type="button" onclick="togglePassword()" id="toggle-password"><img
                    src="./images/eye-open.svg" alt="eye" height="30" /></button>
        </div>
        <br>
        <a href="new_mdp.php">Mot de passe oublié ?</a>
        <br>
        <a href="./inscription.php"> Créer un compte </a>
        <br>
        <div class="options">
            <input type="submit" name="valider" value="Valider" />
            <input type="reset" name="reset" value="Effacer" />
        </div>
    </form>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const togglePasswordButton = document.getElementById("toggle-password");
            const img = togglePasswordButton.querySelector("img");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                img.src = "./images/eye-closed.svg";
            } else {
                passwordInput.type = "password";
                img.src = "./images/eye-open.svg";
            }
        }
    </script>
</body>

</html>