<?php
// Check if the 'id_bat' parameter is set in the URL
if (isset($_GET['id_bat'])) {

    // Disable error reporting for the script
    error_reporting(0);

    // Load the configuration file for the API
    require JPATH_BASE . '/api/config.php';

    // Create a new instance of the mysqli class with the server details
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

    // Check if there was an error connecting to the database
    if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
    }

    // Initialize an array to store the results
    $result = array();

    // Check if the 'type' parameter is set to 'LOGEMENT'
    if ($_GET['type'] == 'LOGEMENT') {
        // Select all from the LOGEMENT table where ID matches the 'id_bat' parameter
        $query = "SELECT * FROM LOGEMENT WHERE ID = " . $_GET['id_bat'];
        $result = $conn->query($query);
    }

    // Check if the 'type' parameter is set to 'PIECE'
    if ($_GET['type'] == 'PIECE') {
        // Select all from the PIECE table where ID_LOGEMENT matches the 'id_bat' parameter
        $query = "SELECT * FROM PIECE WHERE ID_LOGEMENT = " . $_GET['id_bat'];
        $result = $conn->query($query);
    }

    // Check if the 'type' parameter is set to 'CAPTEUR'
    if ($_GET['type'] == 'CAPTEUR') {
        // Select the ID from the PIECE table where ID_LOGEMENT matches the 'id_bat' parameter
        $query = "SELECT ID FROM PIECE WHERE ID_LOGEMENT = " . $_GET['id_bat'];
        $liste_lien_bat = $conn->query($query);

        while ($row = $liste_lien_bat->fetch_assoc()) {
            $id = $row["ID"];
            // Make a query on another table with the ID retrieved
            $sql_CAPTEUR = "SELECT * FROM CAPTEUR WHERE ID_PIECE = $id";
            $result_CAPTEUR = $conn->query($sql_CAPTEUR);

            if ($result_CAPTEUR->num_rows > 0) {
                // Loop through the sensors
                while ($row_CAPTEUR = $result_CAPTEUR->fetch_assoc()) {
                    $result['CAPTEUR'][] = $row_CAPTEUR;
                }
            }
        }
    }

    // Check if the 'type' parameter is set to 'MESURE'
    if ($_GET['type'] == 'MESURE') {
        // Select the ID from the PIECE table where ID_LOGEMENT matches the 'id_bat' parameter
        $query = "SELECT ID FROM PIECE WHERE ID_LOGEMENT = " . $_GET['id_bat'];
        $liste_lien_bat = $conn->query($query);

        while ($row = $liste_lien_bat->fetch_assoc()) {
            $id_piece = $row["ID"];
            // Make a query on another table with the ID retrieved
            $sql_CAPTEUR = "SELECT * FROM CAPTEUR WHERE ID_PIECE = $id_piece";
            $result_CAPTEUR = $conn->query($sql_CAPTEUR);

            if ($result_CAPTEUR->num_rows > 0) {
                // Loop through the sensors
                while ($row_CAPTEUR = $result_CAPTEUR->fetch_assoc()) {
                    $id_capteur = $row_CAPTEUR["ID"];
                    // Make a query on another table with the sensor ID
                    $sql_MESURE = "SELECT * FROM MESURE WHERE ID_CAPTEUR = $id_capteur";
                    $result_MESURE = $conn->query($sql_MESURE);

                    if ($result_MESURE->num_rows > 0) {
                        // Store the results in the array
                        while ($row_MESURE = $result_MESURE->fetch_assoc()) {
                            $result['MESURE'][] = $row_MESURE;
                        }
                    }
                }
            }
        }
    }

// Close the database connection
    $conn->close();

// Create a temporary CSV file
    $csvFile = tempnam(sys_get_temp_dir(), 'export_data');
    $csvHandle = fopen($csvFile, 'w');


    if ($_GET['type'] == 'LOGEMENT' OR $_GET['type'] == 'PIECE' ) {

// Write the data from the result array to the CSV file
        foreach ($result as $row) {
            fputcsv($csvHandle, $row);
        }
    }


    if ($_GET['type'] == 'CAPTEUR' OR $_GET['type'] == 'MESURE' ) {

// Write the data from the result array to the CSV file
        foreach ($result as $key => $subArray) {
            if (is_array($subArray)) {
                foreach ($subArray as $row) {
                    fputcsv($csvHandle, $row);
                }
            } else {
                fputcsv($csvHandle, $subArray);
            }
        }
    }


// Close the CSV file
    fclose($csvHandle);

// Send the CSV file as a download
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="export_data.csv"');
    header('Pragma: no-cache');
    readfile($csvFile);

// Delete the temporary CSV file
    unlink($csvFile);
    exit();

}
?>