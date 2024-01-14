<style>
    /* Set table width to 100%, border-collapse to collapse and text-align to center */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    /* Set border, padding and text-align for th and td elements */
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    /* Set sticky positioning for th element and white background to prevent text overlap during scrolling */
    th {
        position: sticky;
        top: 0;
        background: white; /* prevent text overlap during scrolling */
        z-index: 1; /* make sure header is on top of other rows */
    }

    /* Set background color for Info, Action and Alerte classes */
    .Info {
        background-color: #d9edf7;
    }

    .Action {
        background-color: #fcf8e3;
    }

    .Alerte {
        background-color: #f2dede;
    }

    /* Set max-height and overflow property for table container */
    .table-container {
        max-height: 500px; /* or the height you want */
        overflow: auto;
    }
</style>

<?php
// Disable error reporting
error_reporting(0);

// Import Joomla CMS Factory class
use Joomla\CMS\Factory;

// Include configuration file for API
require JPATH_BASE . '/api/config.php';

// Create a new MySQLi connection object with server credentials from config file
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Check for connection errors and exit if found
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to retrieve the username of the current user
function get_username()
{
    // Get the current user object from Joomla CMS Factory
    $user = Factory::getUser();
    // Get the user ID and username from the user object
    $userId = $user->id;
    $username = $user->username;
    // Check if the user ID is 0 (indicating guest user) and exit if true
    if ($userId === 0) {
        die();
    } else {
        // Return the username if the user ID is non-zero
        return $username;
    }
}

// Function to retrieve and display the sensor measurements for the current user
function print_mesures($username, $conn)
{
    // Define the SQL query to retrieve the measurements for PIR sensors in the user's accommodation
    $sql = "SELECT c.*, m.*
    FROM PIECE p
    JOIN CAPTEUR c ON p.ID = c.ID_PIECE
    JOIN MESURE m ON c.ID = m.ID_CAPTEUR
    WHERE p.ID_LOGEMENT IN (SELECT ID FROM LOGEMENT WHERE UTILISATEUR = '$username') AND c.TYPE = 'PIR' AND NOT m.VALEUR = 0
    ORDER BY `m`.`ID` DESC";
    // Execute the query and store the result set
    $result = $conn->query($sql);

    // Display the results in an HTML table
    echo "<div class='table-container'>";
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Événement</th><th>Lieu</th><th>Criticité</th><th>Date</th></tr>";
        while ($row = $result->fetch_assoc()) {
            // Set the CSS class for the table row based on the criticality of the measurement
            $criticiteClass = "";
            if ($row['CRITICITE'] == 1) {
                $criticiteClass = "Info";
            } elseif ($row['CRITICITE'] == 2) {
                $criticiteClass = "Action";
            } elseif ($row['CRITICITE'] == 3) {
                $criticiteClass = "Alerte";
            }

            // Set the default action value to "Aucune" if it is empty
            if (!$row['ACTION']) {
                $row['ACTION'] = "Aucune";
            }

            // Display the measurement data in an HTML table row with the appropriate CSS class
            echo "<tr class='" . $criticiteClass . "'>";
            if (strtolower($row['ACTION']) === 'batterie') {
                echo "<td>Information batterie</td>";
            } else {
                echo "<td>Détection d'une personne</td>";
            }
            echo "<td>" . $row['NOM'] . "</td>";
            if (strtolower($row['ACTION']) === 'batterie') {
                $criticiteClass = $row["VALEUR"] . "%";
            }
            echo "<td>" . $criticiteClass . "</td>";
            echo "<td>" . date("d/m/Y H:i:s", strtotime($row['TIMESTAMP']) + 3600) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        // Display a message if no measurements are found
        echo "Pas de résultat";
    }
    echo "</div>";
}

// Call the get_username function to retrieve the current user's username
$username = get_username();

// Call the print_mesures function to retrieve and display the sensor measurements for the current user
print_mesures($username, $conn);

// Close the MySQLi connection
$conn->close();
?>