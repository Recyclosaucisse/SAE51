<?php
    function sql_command($colonnes, $values)
    {
        $servername = "";
        $username = "";
        $password = "";
        $dbname = "";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            http_response_code(500);
            die("Connection failed: " . $conn->connect_error);
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $columns_string = implode(',', $colonnes);

        $command = "INSERT INTO `MESURE` ($columns_string) VALUES ($placeholders);";

        $stmt = $conn->prepare($command);
        if ($stmt === false) {
            http_response_code(500);
            die("Erreur lors de la préparation de la requête : " . $conn->error);
        }

        $stmt->bind_param("iiss", ...$values);

        if (!$stmt->execute()) {
            http_response_code(500);
            echo "Erreur lors de l'exécution de la requête : " . $stmt->error;
        } else {
            http_response_code(201);
            echo $stmt->affected_rows . " ligne(s) modifiée(s)";
        }

        $stmt->close();
        $conn->close();
    }

    function verifyToken($token)
    {
        // Remplacer par votre token
        $secret_token = "";
        if ($token === $secret_token) {
            $colonnes = explode(',', $_POST['colonnes']);
            $values = explode(',', $_POST['values']);
            sql_command($colonnes, $values);
        } else {
            http_response_code(403);
            echo "Accès interdit";
            exit;
        }
    }

    if (isset($_POST['token'])) {
        $token = $_POST['token'];
        verifyToken($token);
    } else {
        http_response_code(400);
        echo "Token de connexion manquant";
        exit;
    }
?>