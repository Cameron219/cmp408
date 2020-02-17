#!/usr/bin/python3.7

import paho.mqtt.client as mqtt
import sys
import os

# Broker Information
broker_ip = "xxx.xxx.xxx.xxx"
broker_port = 1234
broker_username = "xxxxx"
broker_password = "xxxxxxxxxxx"
cert_path = "ca.crt"

# Topic information
topic = "lcd"
topic_message = topic + "/message"
topic_custom = topic + "/custom"

# When a message is published to broker
def on_publish(client, userdata, mid):
    print("Message Published!")
    pass

# On connection to broker
def connect(client):
    client.on_publish = on_publish
    client.username_pw_set(username = broker_username,password = broker_password)
    client.tls_set(cert_path, tls_version = 2)
    client.tls_insecure_set(True) # Due to self-signed cert.
    client.connect(broker_ip, broker_port)


# Print Menu for user
def print_menu():
    os.system("clear")
    print("LCD Display Control\n")
    print("1: Custom Message")
    print("2: Date & Time")
    print("3: IP Address")
    print("9: Exit\n")


# Get user input
def main():
    client = mqtt.Client()
    connect(client)
    print_menu()
    choice = input("Choice: ")
    if choice == "1": # Publish Custom Message
        msg = input("\nMessage: ")
        client.publish(topic_message, msg)
    elif choice == "2": # Publish Date & Time
        client.publish(topic_custom, "datetime")
    elif choice == "3": # Publish IP of Pi
        client.publish(topic_custom, "ip")
    elif choice == "9": # Exit
        exit()


if __name__ == "__main__":
    main()
