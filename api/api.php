<?php
require_once 'config.php';

function sql_command($values)
{
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    if ($conn->connect_error) {
        http_response_code(500);
        die("Connection failed: " . $conn->connect_error);
    }

    $placeholders = implode(',', array_fill(0, count($values), '?'));

    $command = "INSERT INTO `MESURE` (`ID_CAPTEUR`, `VALEUR`, `ACTION`, `CRITICITE`) VALUES ($placeholders);";

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

function verifyValues()
{
    if (!isset($_POST['values']) || trim($_POST['values']) === '') {
    http_response_code(400);
    echo "Les valeurs sont manquantes ou vides.";
    exit;
    }

    $values = explode(',', $_POST['values']);

    if (count($values) !== 4) {
        http_response_code(400);
        echo "Le nombre de valeurs fourni est incorrect.";
        exit;
    }

    $idCapteur = filter_var($values[0], FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 1,
            'max_range' => 4
        ]
    ]);
    if ($idCapteur === false) {
        http_response_code(400);
        echo "La première valeur doit être un entier entre 1 et 4.";
        exit;
    }

    $valeur = filter_var($values[1], FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 1,
            'max_range' => 775
        ]
    ]);
    if ($valeur === false) {
        http_response_code(400);
        echo "La deuxième valeur doit être un entier entre 1 et 775.";
        exit;
    }

    $values[2] = trim($values[2]);

    if ($values[2] !== "ALLUMER-LUMIERE" && $values[2] !== "ETEINDRE-LUMIERE" && $values[2] !== "") {
        http_response_code(400);
        echo "La troisième valeur doit être 'ALLUMER-LUMIERE' ou 'ETEINDRE-LUMIERE' ou rien.";
        exit;
    }

    $criticite = filter_var($values[3], FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 1,
            'max_range' => 3
        ]
    ]);
    if ($criticite === false) {
        http_response_code(400);
        echo "La quatrième valeur doit être un entier entre 1 et 3.";
        exit;
    } elseif ($values[2] !== '' && $criticite == 1) {
        http_response_code(400);
        echo "Si la troisième valeur n'est pas vide, alors la quatrième valeur ne peut pas être 1.";
        exit;
    }

    return $values;
}

function verifyToken($token)
{
    if ($token === AUTHTOKEN) {
        $values = verifyValues();
        sql_command($values);
    } else {
        http_response_code(403);
        echo "Accès interdit";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

if (isset($_POST['token'])) {
    $token = $_POST['token'];
    verifyToken($token);
} else {
    http_response_code(400);
    echo "Requete mal formulée";
    exit;
}
?>