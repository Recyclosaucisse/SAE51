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

// Define a function to retrieve and display the general information of the user's house
function print_info_maison($username, $conn)
{
        // Define the SQL query to retrieve data from the database
        $sql = "SELECT * FROM `LOGEMENT` WHERE `UTILISATEUR` = '$username'";
        $result = $conn->query($sql);

        // Check if there are any rows in the result set
        if ($result->num_rows > 0) {
                // Start the unordered list tag
                echo "Voici les informations générales concernant votre maison:<ul>";

                // Loop through each row in the result set
                while ($row = $result->fetch_assoc()) {
                        // Output the values of the columns
                        echo "<li>Nom du batiment: " . $row["NOM"] . "</li>";
                        echo "<li>Adresse: " . $row["ADRESSE"] . "</li>";
                }

                // Close the unordered list tag
                echo "</ul>";
        } else {
                // If there are no rows in the result set, output a message
                echo "Vous n'avez pas encore associé de logement à votre compte, veuillez-nous contacter pour démarrer la configuration de votre logement si ca n'est pas déjà fait.";
        }
}

// Define a function to retrieve and display the list of available rooms
function print_table_piece($username, $conn)
{
        // Start the h2 tag for the title
        echo "<h2>Pièces disponibles</h2>";

        // Define the SQL query to retrieve data from the database
        $sql = "SELECT * FROM PIECE WHERE ID_LOGEMENT IN (SELECT ID FROM LOGEMENT WHERE UTILISATEUR = '$username')";
        $result = $conn->query($sql);

        // Check if there are any rows in the result set
        if ($result->num_rows > 0) {
                // Start the div tag
                echo "<div><ul>";

                // Loop through each row in the result set
                while ($row = $result->fetch_assoc()) {
                        // Output the value of the NOM column
                        echo "<li>" . $row['NOM'] . "</li>";
                }

                // Close the unordered list and div tags
                echo "</ul></div>";
        } else {
                // If there are no rows in the result set, output a message
                echo "Il n'y a pas encore de pièce associée à votre compte !";
        }
}

// Define a function to retrieve and display the list of available sensors
function print_table_capteurs($username, $conn)
{
        // Start the h2 tag for the title
        echo "<h2>Capteurs disponibles</h2>";

        // Define the SQL query to retrieve data from the database
        $sql = "SELECT c.* FROM PIECE p JOIN CAPTEUR c ON p.ID = c.ID_PIECE WHERE p.ID_LOGEMENT IN (SELECT ID FROM LOGEMENT WHERE UTILISATEUR = '$username')";
        $result = $conn->query($sql);

        // Check if there are any rows in the result set
        if ($result->num_rows > 0) {
                // Start the div tag for the table container
                echo "<div class='table-container'>";

                // Start the table tag
                echo "<table>";

                // Output the table header row
                echo "<tr><th>Nom</th><th>Type de capteur</th></tr>";

                // Loop through each row in the result set
                while ($row = $result->fetch_assoc()) {
                        // Start the table row tag
                        echo "<tr>";

                        // Output the values of the columns
                        echo "<td>" . $row['NOM'] . "</td>";
                        echo "<td>" . $row['TYPE'] . "</td>";

                        // Close the table row tag
                        echo "</tr>";
                }

                // Close the table and div tags
                echo "</table></div>";
        } else {
                // If there are no rows in the result set, output a message
                echo "Pas de capteurs disponibles !";
        }
}

// Define a function to retrieve and display the list of available measures
function print_table_mesures($username, $conn)
{
        // Start the h2 tag for the title
        echo "<h2>Mesures disponibles</h2>";

        // Define the SQL query to retrieve data from the database
        $sql = "SELECT c.*, m.* FROM PIECE p JOIN CAPTEUR c ON p.ID = c.ID_PIECE JOIN MESURE m ON c.ID = m.ID_CAPTEUR WHERE p.ID_LOGEMENT IN (SELECT ID FROM LOGEMENT WHERE UTILISATEUR = '$username') ORDER BY `m`.`ID` ASC";
        $result = $conn->query($sql);

        // Check if there are any rows in the result set
        if ($result->num_rows > 0) {
                // Start the div tag for the table container
                echo "<div class='table-container'>";

                // Start the table tag
                echo "<table>";

                // Output the table header row
                echo "<tr><th>Capteur</th><th>Valeur du capteur</th><th>Action réalisé</th><th>Criticité</th><th>Timestamp</th></tr>";

                // Loop through each row in the result set
                while ($row = $result->fetch_assoc()) {
                        // Start the table row tag
                        echo "<tr>";

                        // Output the values of the columns
                        echo "<td>" . $row['NOM'] . "</td>";
                        echo "<td>" . $row['VALEUR'] . "</td>";
                        echo "<td>" . $row['ACTION'] . "</td>";
                        echo "<td>" . $row['CRITICITE'] . "</td>";
                        echo "<td>" . $row['TIMESTAMP'] . "</td>";

                        // Close the table row tag
                        echo "</tr>";
                }

                // Close the table and div tags
                echo "</table></div>";
        } else {
                // If there are no rows in the result set, output a message
                echo "Pas de mesure disponibles !";
        }
}

// Retrieve the username of the current user
$username = get_username();

// Call the functions to retrieve and display the information
print_info_maison($username, $conn);
print_table_piece($username, $conn);
print_table_capteurs($username, $conn);
print_table_mesures($username, $conn);

// Close the database connection
$conn->close();
?>