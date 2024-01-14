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
// Disable error reporting for the script
error_reporting(0);

// Import the necessary Joomla classes
use Joomla\CMS\Factory;

// Load the configuration file for the API
require JPATH_BASE . '/api/config.php';

// Create a new instance of the mysqli class with the server details
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Check if there was an error connecting to the database
if ($conn->connect_error) {
    // If there was an error, display an error message and exit the script
    die("Connection failed: " . $conn->connect_error);
}

// Define a function to retrieve the username of the current user
function get_username()
{
    // Create a new instance of the Joomla user factory
    $user = Factory::getUser();
    $userId = $user->id;
    $username = $user->username;

    // Check if the user is logged in
    if ($userId === 0) {
        // If the user is not logged in, exit the script
        die();
    } else {
        // Return the username of the user
        return $username;
    }
}

// Define a function to retrieve and display the values of the photoresistance sensors
function print_valeur($username, $conn)
{
    // Define the SQL query to retrieve data from the database
    $sql = "SELECT c.*, m.*
    FROM PIECE p
    JOIN CAPTEUR c ON p.ID = c.ID_PIECE
    JOIN MESURE m ON c.ID = m.ID_CAPTEUR
    WHERE p.ID_LOGEMENT IN (SELECT ID FROM LOGEMENT WHERE UTILISATEUR = '$username') AND c.TYPE = 'PHOTORESISTANCE'
    ORDER BY m.ID DESC";

    // Execute the SQL query and store the result set
    $result = $conn->query($sql);

    // Start the table container div
    echo "<div class='table-container'>";

    // Check if there are any rows in the result set
    if ($result->num_rows > 0) {
        // Start the table tag
        echo "<table>";
        echo "<tr><th>Luminosité</th><th>Action</th><th>Criticité</th><th>Date</th></tr>";

        // Loop through each row in the result set
        while ($row = $result->fetch_assoc()) {
            // Initialize variable to store the CSS class for the row
            $criticiteClass = "";

            // Determine the CSS class for the row based on the criticality value
            if ($row['CRITICITE'] == 1) {
                $criticiteClass = "Info";
            } elseif ($row['CRITICITE'] == 2) {
                $criticiteClass = "Action";
            } elseif ($row['CRITICITE'] == 3) {
                $criticiteClass = "Alerte";
            }

            // Determine the action value to display based on the raw action value
            if ($row['ACTION'] === "ALLUMER-LUMIERE") {
                $row['ACTION'] = "Allumer";
            } elseif ($row['ACTION'] === "ETEINDRE-LUMIERE") {
                $row['ACTION'] = "Éteindre";
            } else {
                $row['ACTION'] = "Aucune";
            }

            // Start the row tag and add the CSS class
            echo "<tr class='" . $criticiteClass . "'>";

            // Output the values of the columns
            echo "<td>" .$row['VALEUR'] . "</td>";
            echo "<td>" . $row['ACTION'] . "</td>";
            echo "<td>" . $criticiteClass . "</td>";
            echo "<td>" . date("d/m/Y H:i:s", strtotime($row['TIMESTAMP']) + 3600) . "</td>";

            // Close the row tag
            echo "</tr>";
        }

        // Close the table tag
        echo "</table>";
    } else {
        // If there are no rows in the result set, output a message
        echo "Aucun résultat";
    }

    // Close the table container div
    echo "</div>";
}

// Retrieve the username of the current user
$username = get_username();

// Call the function to retrieve and display the values of the photoresistance sensors
print_valeur($username, $conn);

// Close the database connection
$conn->close();
?>