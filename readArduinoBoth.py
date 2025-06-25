import serial
import mysql.connector
from datetime import datetime
import time

# --- Configuration ---
SERIAL_PORT = '/dev/ttyUSB0'  # Change to your Arduino's serial port
BAUD_RATE = 9600

# --- Connect to Arduino ---
ser = serial.Serial(SERIAL_PORT, BAUD_RATE, timeout=2)
time.sleep(2)  # Allow Arduino to reset

# --- Connect to MySQL ---
db = mysql.connector.connect(
    host="localhost",
    user="ben",
    password="password",  # Replace with your actual password
    database="ben"
)
cursor = db.cursor()

# --- Create tables if not exist ---
cursor.execute("""
    CREATE TABLE IF NOT EXISTS tempsoil (
        id INT AUTO_INCREMENT PRIMARY KEY,
        temp1 FLOAT,
        temp2 FLOAT,
        temp3 FLOAT,
        soil1 INT,
        soil2 INT,
        timedate DATETIME
    )
""")

cursor.execute("""
    CREATE TABLE IF NOT EXISTS temp2 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        temp1 FLOAT,
        temp2 FLOAT,
        temp3 FLOAT,
        timedate DATETIME
    )
""")

# --- Helper: Parse and Insert Data ---
def insert_data(temp1, temp2, temp3, soil1, soil2):
    now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    # Insert into tempsoil
    query1 = """
        INSERT INTO tempsoil (temp1, temp2, temp3, soil1, soil2, timedate)
        VALUES (%s, %s, %s, %s, %s, %s)
    """
    values1 = (temp1, temp2, temp3, soil1, soil2, now)
    cursor.execute(query1, values1)

    # Insert into temp2
    query2 = """
        INSERT INTO temp2 (temp1, temp2, temp3, timedate)
        VALUES (%s, %s, %s, %s)
    """
    values2 = (temp1, temp2, temp3, now)
    cursor.execute(query2, values2)

    db.commit()
    print("Data inserted into tempsoil and temp2:", values1, values2)

# --- Main Loop ---
try:
    while True:
        line = ser.readline().decode('utf-8').strip()
        if not line:
            continue

        print("Received:", line)

        if "T0:" in line:
            temps = line.split("  ")
            temp_vals = [float(t.split(":")[1].replace("C", "").strip()) for t in temps]

        elif "Soil 1:" in line:
            soil1 = int(line.split(":")[1].replace("%", "").strip())

        elif "Soil 2:" in line:
            soil2 = int(line.split(":")[1].replace("%", "").strip())

            # All values received ? insert into both tables
            insert_data(temp_vals[0], temp_vals[1], temp_vals[2], soil1, soil2)

except KeyboardInterrupt:
    print("Stopping...")
    ser.close()
    cursor.close()
    db.close()
