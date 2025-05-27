import serial
import mysql.connector
from datetime import datetime
import time
import re

# --- Configuration ---
SERIAL_PORT = '/dev/ttyUSB0'        # Location of Arduino (eg, /dev/ttyUSB0 on Linux)
BAUD_RATE = 9600
DB_CONFIG = {
    'host': 'localhost',
    'user': 'ben',
    'password': 'password',
    'database': 'ben'
}
TABLE_NAME = 'temp2'

# --- Connect to Serial ---
ser = serial.Serial(SERIAL_PORT, BAUD_RATE, timeout=1)
time.sleep(2)  # Wait for Arduino to reset

# --- Connect to MySQL ---
db = mysql.connector.connect(**DB_CONFIG)
cursor = db.cursor()

# --- Main Loop ---
try:
    while True:
        line = ser.readline().decode('utf-8').strip()

        # Expected format: "T0: 23.4 C  T1: 24.1 C  T2: 23.8 C"
        match = re.findall(r'T\d: ([\d.]+)', line)

        if len(match) == 3:
            temp1, temp2, temp3 = map(float, match)
            now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

            # Insert into MySQL
            sql = f"INSERT INTO {TABLE_NAME} (temp1, temp2, temp3, timedate) VALUES (%s, %s, %s, %s)"
            values = (temp1, temp2, temp3, now)

            cursor.execute(sql, values)
            db.commit()

            print(f"Inserted: {temp1}C, {temp2}C, {temp3}C at {now}")

except KeyboardInterrupt:
    print("Stopped by user.")

finally:
    ser.close()
    db.close()
