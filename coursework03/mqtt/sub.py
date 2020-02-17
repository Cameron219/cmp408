import paho.mqtt.client as mqtt
import Adafruit_CharLCD as LCD
import os
import time
from datetime import datetime

# Broker Information
broker_ip = "xxx.xxx.xxx.xxx"
broker_port = 1234
broker_username = "xxxxx"
broker_password = "xxxxxxxxxx"
cert_path = "ca.crt"

# Topic Information
topic = "lcd"
topic_message = topic + "/message"
topic_custom = topic + "/custom"

# LCD Information
lcd_rs  = 25
lcd_en  = 24
lcd_d4  = 23
lcd_d5  = 17
lcd_d6  = 18
lcd_d7  = 22
lcd_col = 16
lcd_row = 2
lcd_backlight = 4

lcd = LCD.Adafruit_CharLCD(lcd_rs, lcd_en, lcd_d4, lcd_d5, lcd_d6, lcd_d7, lcd_col, lcd_row, lcd_backlight)

# Display msg on the LCD screen
def lcd_display_message(msg):
    lcd.clear()
    lcd.message(msg)


# On connection to broker
def on_connect(client, userdata, flags, rc):
    if rc == 0:
        # Subscribe to relevant topics
        client.subscribe(topic_message)
        client.subscribe(topic_custom)
        lcd_display_message("Connected!\n"+broker_ip)
        print "Connection to broker successful!"
    else: # Error connecting to broker
        error_message = "Connection refused - "
        if(rc == 1):
            error_message += "incorrect protocol version"
        elif (rc == 2):
            error_message += "invalid client identifier"
        elif (rc == 3):
            error_message += "server unavailable"
        elif (rc == 4):
            error_message += "bad username or password"
        elif (rc == 5):
            error_message += "not authorised"
        else:
            error_message += "Unknown"
        print "(" + str(rc) + ") " + error_message
        lcd_display_message("Connection\nFailed.")


# When a subscribed topic recieves a message
def on_message(client, userdata, msg):
    print "Topic: " + msg.topic + "\nMessage:\n" + str(msg.payload)
    if msg.topic == topic_message:
        # If the message goes offscreen, take a new line (Assming no newline is already present in the message
        if len(msg.payload) > 16 and not "\n" in msg.payload:
            lcd_display_message(msg.payload[0:16] + "\n" + msg.payload[16:])
        else:
            lcd_display_message(msg.payload)
    elif msg.topic == topic_custom:
        if msg.payload == "datetime": # Display the date & time of the Pi
            now = datetime.now()
            date_time = now.strftime("   %d/%m/%Y\n    %H:%M:%S")
            lcd_display_message(date_time)
        if msg.payload == "ip": # Display the IP address of the Pi
            stream = os.popen("hostname -I")
            host_ip = stream.read()
            lcd_display_message("IP:\n" + host_ip)


# Connect to the broker
def connect(client):
    client.on_connect = on_connect
    client.on_message = on_message
    client.username_pw_set(username = broker_username, password = broker_password)
    client.tls_set(cert_path, tls_version = 2)
    client.connect(broker_ip, broker_port)
    client.loop_forever()


def main():
    client = mqtt.Client()
    connect(client)


if __name__ == "__main__":
    main()
