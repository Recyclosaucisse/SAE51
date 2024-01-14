// Import the axios library to make HTTP requests
const axios = require('axios');

// Define a function to send data to the database
function send_to_bdd(values) {
    // Set the API URL and request data
    const url = "http://sae51.rt-blagnac.fr/api/api.php";
    const data = {
        // The token is used for authentication purposes
        token: "token",
        // The values parameter is an array that is joined into a comma-separated string
        values: values.join(',')
    };

    // Set the request headers to indicate the type of data being sent
    const headers = {
        'Content-Type': 'application/x-www-form-urlencoded'
    };

    // Make a POST request to the API with the data and headers
    axios.post(url, data, { headers })
    // Handle a successful response
    .then((response) => {
    // Check if the response status is 201 (Created)
        if (response.status === 201) {
            // Log a success message to the console
            console.log("Données envoyées.");
        }
    })
    // Handle an error response
    .catch((error) => {
        // Log an error message to the console, including the HTTP status code and response data
        console.error(`Erreur ${error.response.status}: ${error.response.data}`);
    });
}

// Define an array of values to send to the database
const values = [1, 1, "", 1];
// Call the send_to_bdd function with the values array
send_to_bdd(values);