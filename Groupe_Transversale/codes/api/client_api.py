import requests  # Import the requests library for making HTTP requests
import random  # Import the random library for generating random values
import time  # Import the time library for introducing delays between requests


# Define a function to send data to the database
def send_to_bdd(values):
    url = "http://sae51.rt-blagnac.fr/api/api.php"  # Set the API URL

    data = {
        "token": "token",  # Set the authentication token
        "values": ','.join([str(v) for v in values])  # Convert the values list to a comma-separated string and set it as the request data
    }

    response = requests.post(url, data=data)  # Make a POST request to the API with the data

    # Handle different HTTP status codes
    if response.status_code == 201:
        pass  # Do nothing if the request was successful
    else:
        print(f"Error {response.status_code}: {response.text}")  # Print an error message if the request failed


# Define an optional function to generate test values
def generate_values():
    data = [[1, 1, '', '1'],
            [2, 1, '', '1'],
            [3, 0, '', '1'],
            [5, 200, 'ALLUMER-LUMIERE', '2'],
            [2, 1, '', '3'],
            [1, 1, '', '3'],
            [5, 525, 'ETEINDRE-LUMIERE', '2'],
            [6, -26, '', '1'],
            [6, 5, '', '3'],
            [6, -36, '', '1'],
            [4, 0, '', '1'],
            [1, 0, '', '2'],
            [2, 1, '', '3'],
            [3, 1, '', '2'],
            [4, 1, '', '1'],
            [5, 400, 'ALLUMER-LUMIERE', '2'],
            [5, 475, 'ETEINDRE-LUMIERE', '2'],
            [6, -15, '', '1'],
            [6, 8, '', '3'],
            [6, -45, '', '1'],
            [1, 1, '', '1'],
            [2, 0, '', '1'],
            [3, 0, '', '1'],
            [4, 1, '', '2'],
            [5, 600, 'ALLUMER-LUMIERE', '2'],
            [1, 100, 'BATTERIE', 1]]

    for item in data:
        send_to_bdd(item)  # Call the send_to_bdd function with each item in the data list
        time.sleep(random.uniform(1, 10))  # Introduce a random delay between 1 and 10 seconds before sending the next request

    exit(0)

# The generate_values function is optional and allows generating test values for the send_to_bdd function
# Uncomment the following line to generate test values
# generate_values()


# Set a list of values to send to the database
values = [3, 1, "", 1]

# Call the send_to_bdd function with the values list
send_to_bdd(values)
