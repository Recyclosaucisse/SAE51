<?php
// Load the configuration file
require_once 'config.php';

// Create a new instance of the mysqli class with the server details
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Check if there was an error connecting to the database
if ($conn->connect_error) {
    // If there was an error, display an error message and exit the script
    die("Connection failed: " . $conn->connect_error);
}

// Define the SQL query to retrieve data from the database
$sql = "SELECT c.*, m.*
        FROM PIECE p
        JOIN CAPTEUR c ON p.ID = c.ID_PIECE
        JOIN MESURE m ON c.ID = m.ID_CAPTEUR
        WHERE p.ID_LOGEMENT IN (SELECT ID FROM LOGEMENT WHERE UTILISATEUR = 'enseignants')
        AND c.TYPE = 'PIR' AND c.ID IN (1, 2, 3) AND m.ACTION != 'BATTERIE'
        ORDER BY `m`.`ID` DESC LIMIT 3";

// Execute the SQL query and store the result set
$result = $conn->query($sql);

// Initialize variables to store the values of the sensors
$zone = 0;
$capteur1 = null;
$capteur2 = null;
$capteur3 = null;

// Check if there are any rows in the result set
if ($result->num_rows > 0) {
    // Loop through each row in the result set
    while ($row = $result->fetch_assoc()) {
        // Check the ID_CAPTEUR value in the row and assign the corresponding value to the correct sensor
        if ($row["ID_CAPTEUR"] == 1) { 
            $capteur1 = $row["VALEUR"];
        } elseif ($row["ID_CAPTEUR"] == 2) {
            $capteur2 = $row["VALEUR"];
        } elseif ($row["ID_CAPTEUR"] == 3) {
            $capteur3 = $row["VALEUR"];
        }
    }
    // Check if all three sensors have values
    if ($capteur1 !== null && $capteur2 !== null && $capteur3 !== null) {
        // Determine the zone based on the values of the sensors
        if ($capteur1 == 1 && $capteur2 == 1 && $capteur3 == 1) {
            $zone = 7;
        } elseif ($capteur1 == 1 && $capteur2 == 0 && $capteur3 == 0) {
            $zone = 1;
        } elseif ($capteur1 == 0 && $capteur2 == 1 && $capteur3 == 0) {
            $zone = 2;
        } elseif ($capteur1 == 0 && $capteur2 == 0 && $capteur3 == 1) {
            $zone = 3;
        } elseif ($capteur1 == 1 && $capteur2 == 1 && $capteur3 == 0) {
            $zone = 4;
        } elseif ($capteur1 == 1 && $capteur2 == 0 && $capteur3 == 1) {
            $zone = 5;
        } elseif ($capteur1 == 0 && $capteur2 == 1 && $capteur3 == 1) {
            $zone = 6;
        }
    }
}

// Close the database connection
$conn->close();

// Set the content type of the response to JSON
header('Content-Type: application/json');

// Encode the zone value as a JSON string and output it
echo json_encode($zone);
?>