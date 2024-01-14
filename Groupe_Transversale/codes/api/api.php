<?php
require_once 'config.php'; // Include the configuration file for database connection settings

// Function to execute a SQL command
function sql_command($values)
{
    // Create a new connection to the database
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    // Check for a connection error
    if ($conn->connect_error) {
        http_response_code(500);
        exit("Connection failed: " . $conn->connect_error);
    }

    // Prepare the placeholders for the prepared statement based on the number of values
    $placeholders = implode(',', array_fill(0, 4, '?'));

    // Prepare the SQL command with placeholders
    $command = "INSERT INTO `MESURE` (`ID_CAPTEUR`, `VALEUR`, `ACTION`, `CRITICITE`) VALUES ($placeholders);";

    // Prepare the statement to avoid SQL injection
    $stmt = $conn->prepare($command);
    if ($stmt === false) {
        http_response_code(500);
        exit("Erreur lors de la préparation de la requête : " . $conn->error);
    }

    // Bind the parameters to the statement
    $stmt->bind_param("iiss", ...$values);

    // Execute the statement and handle errors
    if (!$stmt->execute()) {
        http_response_code(500);
        echo "Erreur lors de l'exécution de la requête : " . $stmt->error;
    } else {
        http_response_code(201);
        // echo $stmt->affected_rows . " ligne(s) modifiée(s)";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}

// Function to verify the values from the POST request, very restrictive, could be less precise
function verifyValues()
{
    // Check if 'values' are set and not empty
    if (!isset($_POST['values']) || trim($_POST['values']) === '') {
    http_response_code(400);
    echo "Les valeurs sont manquantes ou vides.";
    exit;
    }

    // Explode the values into an array
    $values = explode(',', $_POST['values']);

    // Check if the correct number of values is provided
    if (count($values) !== 4) {
        http_response_code(400);
        echo "Le nombre de valeurs fourni est incorrect.";
        exit;
    }

    // Check if the value of the id_capteur is correct
    $idCapteur = filter_var($values[0], FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 1,
            'max_range' => 6
        ]
    ]);
    if ($idCapteur === false) {
        http_response_code(400);
        echo "La première valeur doit être un entier entre 1 et 6.";
        exit;
    }

    // Check if the value is an int
    $valeur = filter_var($values[1], FILTER_VALIDATE_INT);

    // Remove any leading or trailing whitespace from the third value in the array
    $values[2] = trim($values[2]);

    // Check if the third value is not one of the allowed actions or an empty string
    if ($values[2] !== "ALLUMER-LUMIERE" && $values[2] !== "ETEINDRE-LUMIERE" && $values[2] !== "" && $values[2] !== "BATTERIE") {
        http_response_code(400);
        echo "La troisième valeur doit être 'ALLUMER-LUMIERE' ou 'ETEINDRE-LUMIERE' ou 'BATTERIE' ou ''.";
        exit;
    }

    // Validate the fourth value to ensure it is an integer within the range of 1 to 3
    $criticite = filter_var($values[3], FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 1,
            'max_range' => 3
        ]
    ]);
    if ($criticite === false) {
        // If the validation fails, send a 400 Bad Request HTTP status code and an error message
        http_response_code(400);
        echo "La quatrième valeur doit être un entier entre 1 et 3.";
        exit;
    } //elseif ($values[2] !== '' && $criticite == 1) {
    //     // If the third value is not empty and the fourth value is 1, it's also an error
    //     http_response_code(400);
    //     echo "Si la troisième valeur n'est pas vide, alors la quatrième valeur ne peut pas être 1. S'il s'agit d'une action la criticité doit être à 2, si une alerte à 3.";
    //     exit;
    // }

    return $values;
}

// Function to verify the authentication token
function verifyToken($token)
{
    // Check if the provided token matches the expected token
    if ($token === AUTHTOKEN) {
        $values = verifyValues();
        sql_command($values);
    } else {
        http_response_code(403);
        echo "Accès interdit";
        exit;
    }
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Méthode non acceptée";
    exit;
}

// Check if the token is set in the POST request
if (isset($_POST['token']) && $_POST['token'] !== "") {
    $token = $_POST['token'];
    verifyToken($token);
} else {
    http_response_code(400);
    echo "Token de connexion manquant";
    exit;
}
?>