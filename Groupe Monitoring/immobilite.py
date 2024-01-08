#!/usr/bin/env python3

# Import necessary libraries
import paho.mqtt.client as mqtt
import requests
import RPi.GPIO as GPIO
import time
import datetime
import ssl

# Initialize a list to store received messages
messages_recus = []

# Define the function to be called when a new message is received
def on_message(client, userdata, message):
    global messages_recus
    messages_recus.append(message.payload.decode())

# Define a function to check if a given zone is present
def verification_presence(resu):
    zones = ["zone 1", "zone 2", "zone 3", "zone 4", "zone 5", "zone 6", "zone 7"]
    return resu in zones

# Define the main function for motion detection and inactivity alert
def immobilite():
    global messages_recus  # Indicate that messages_recus is a global variable

    # MQTT broker and connection details
    broker = "192.168.45.155"
    port = 1883
    topic = "SDB/localisation"
    username = "admin"
    password = "passpartout"

    # Set up MQTT client
    clientpy = mqtt.Client(client_id="Codepy")
    clientpy.on_message = on_message

    clientpy.username_pw_set(username, password)
    clientpy.tls_set(tls_version=ssl.PROTOCOL_TLS, cert_reqs=ssl.CERT_NONE)

    clientpy.connect(broker, port, 60)
    clientpy.subscribe(topic)
    clientpy.loop_start()

    timer = 30  # Initialize the timer

    # Continuously check for motion
    while True:
        # Decrement the timer only if motion is not detected
        if timer > 0:
            timer -= 10
            print(f"Timer: {timer}")

        # Wait until messages are received
        while not messages_recus:
            pass

        # Use the last received message
        result = verification_presence(messages_recus[-1])

        # Empty the list for the next iteration
        messages_recus = []

        # Check if the user is currently in the room
        if result:
            # Reset the timer to 30 if motion is detected
            timer = 30
            print("Detection")

        # If the timer reaches 0, alert the user about their inactivity
        if timer <= 0 and not result:
            print("Immobilite detectee")
            clientpy.publish(topic,"immobilite")


        # Wait for 10 seconds before the next iteration
        time.sleep(10)

    # Stop the MQTT loop and disconnect from the broker
    clientpy.loop_stop()
    clientpy.disconnect()

try:
    immobilite()

except KeyboardInterrupt:
    print("Program stopped by the user.")


