#include "B3950Thermistor.h"

// --- Thermistor setup ---
const int thermistorPins[3] = {A0, A1, A2};
B3950Thermistor thermistors[3] = {
  B3950Thermistor(A0, 10000.0, 5.0),
  B3950Thermistor(A1, 10000.0, 5.0),
  B3950Thermistor(A2, 10000.0, 5.0)
};

// --- Soil moisture sensor setup ---
const int soilPin1 = A15;
const int soilPin2 = A14;

void setup() {
  Serial.begin(9600);
  delay(1000);  // Give Serial Monitor time to connect
}

void loop() {
  // --- Thermistor readings ---
  for (int i = 0; i < 3; i++) {
    float tempC = thermistors[i].readTemperatureCelsius();

    Serial.print("T");
    Serial.print(i);
    Serial.print(": ");
    Serial.print(tempC, 1);
    Serial.print(" C  ");
  }

  Serial.println();

  // --- Soil moisture readings ---
  int soil1 = analogRead(soilPin1);
  int soil2 = analogRead(soilPin2);

  soil1 = map(soil1, 485, 1023, 100, 0);
  soil2 = map(soil2, 485, 1023, 100, 0);

  Serial.print("Soil 1: ");
  Serial.print(soil1);
  Serial.println("%");

  Serial.print("Soil 2: ");
  Serial.print(soil2);
  Serial.println("%");

  // Wait 60 seconds before next reading
  delay(60000);
}
