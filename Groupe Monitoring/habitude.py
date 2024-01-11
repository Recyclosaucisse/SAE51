#!/usr/bin/env python3

import paho.mqtt.client as mqtt
import requests
import ssl

import RPi.GPIO as GPIO
import time
import datetime

# MQTT broker and connection details
def mqtt_publish(msg) :
    broker = "192.168.45.155"
    port = 1883
    topic = "SDB/habitude"
    username = "admin"
    password = "passpartout"

    # Set up MQTT client
    clientpy = mqtt.Client(client_id="Codepy")

    clientpy.username_pw_set(username, password)
    clientpy.tls_set(tls_version=ssl.PROTOCOL_TLS, cert_reqs=ssl.CERT_NONE)

    clientpy.connect(broker, port, 60)
    clientpy.subscribe(topic)
    clientpy.loop_start()
    clientpy.publish(topic,msg)
    clientpy.loop_stop()
    clientpy.disconnect()

def send_to_bdd(values):
  url = "http://sae51.rt-blagnac.fr/api/api.php"
  
  data = {
  "token": "zfLMqQseDneEogUe52avGbBdJ8RATdCi9B4eag8cjPy8Qu82qkdffFYXwCcz3n3kV6K9wAeQY2nA6a6UGK38syHwLLfu632FoJ6X",
  "values": ','.join([str(v) for v in values])
  }
  
  response = requests.post(url, data=data)
  
  if response.status_code == 201:
    pass
  else:
    print(f"Erreur {response.status_code}: {response.text}")

SENSOR_PIN = 17
HABITUDES_FILE = "/home/pi/CapMouv/habitudes.txt"
user_piece = 0

# Define the read_habitudes function
def read_habitudes(file_path):
    habitudes = {}

    # Open the file in read mode
    with open(file_path, 'r') as file:
        # Iterate over each line in the file
        for line in file:
            # Split the line into period and duration
            period, duration = line.strip().split(':')

            # Store the period and duration in the habits dictionary
            habitudes[period.lower()] = int(duration)

    # Return the habits dictionary
    return habitudes

# Define the get_current_period function
def get_current_period(hour):
    # Check if the hour is between 6 and 12
    if 6 <= hour < 12:
        # Return "matin"
        return "matin"

    # Check if the hour is between 12 and 18
    elif 12 <= hour < 18:
        # Return "apresmidi"
        return "apresmidi"

    # Check if the hour is between 18 and 23
    elif 18 <= hour < 23:
        # Return "soir"
        return "soir"

    # Otherwise, return "nuit"
    else:
        # Return "nuit"
        return "nuit"

# Set the GPIO mode to BCM
GPIO.setmode(GPIO.BCM)

# Set the GPIO pin for the sensor as input
GPIO.setup(SENSOR_PIN, GPIO.IN)

# Initialize the habitudes dictionary
habitudes = read_habitudes(HABITUDES_FILE)

# Try-except block to handle exceptions
try:
    # Continuously check for motion
    while True:
        # Check if there's motion detected
        if GPIO.input(SENSOR_PIN):
            # Get the current time
            current_time = datetime.datetime.now()

            # Get the current period of the day
            current_period = get_current_period(current_time.hour)

            # Check if the user is currently in the room
            if user_piece == 1:
                # Reset the timer to 0 if someone is in the room
                user_piece = 0

                # Print a message indicating the user has left the room
                print("L'utilisateur sort de la piece")

                # Reset the timer to the duration of the current period
                timer = habitudes[current_period]

            # Otherwise, check if the user is entering the room
            else:
                # Set the timer to the duration of the current period
                user_piece = 1
                print("L'utilisateur entre dans la piece")
                values = [4, 1, "", 1]
                send_to_bdd(values)
                timer = habitudes.get(current_period, 30)  # Default to 30 seconds if period not found

            # Display a message indicating the current period
            print(f"Periode: {current_period.capitalize()}")

        # If there's no motion detected
        else:
            # If the user is currently in the room
            if user_piece == 1:
                # Decrement the timer by 2.5 seconds
                timer -= 2.5

                # If the timer reaches 0, alert the user about their inactivity
                if timer <= 0:
                    mqtt_publish("habitude prolongee")
                    print("Habitude prolongee")

            # Wait for 2.5 seconds
        time.sleep(2.5)

# Handle keyboard interrupts gracefully
except KeyboardInterrupt:
    print("Program stopped by the user.")

# Reset the GPIO pins on exit
finally:
    GPIO.cleanup()


