const int thermistorPins[3] = {A0, A1, A2};

// Constants for the thermistor and calculation
const float SERIES_RESISTOR = 10000.0;   // 10k? fixed resistor
const float NOMINAL_RESISTANCE = 10000.0; // 10k? at 25C
const float NOMINAL_TEMPERATURE = 25.0;   // 25C
const float BETA_COEFFICIENT = 3950.0;    // Beta coefficient, more research needed
const int ADC_MAX = 1023;                 // 10-bit ADC

void setup() {
  Serial.begin(9600);
  delay(1000);  // Give Serial Monitor time to connect
}

void loop() {
  for (int i = 0; i < 3; i++) {
    int adcValue = analogRead(thermistorPins[i]);

    // Convert the ADC value to resistance
    float resistance = SERIES_RESISTOR / ((ADC_MAX / (float)adcValue) - 1);

    // Apply the Beta formula to get temperature in Kelvin, note: off by a few degrees
    float tempK = 1.0 / (1.0 / (NOMINAL_TEMPERATURE + 273.15) + 
                         (1.0 / BETA_COEFFICIENT) * log(resistance / NOMINAL_RESISTANCE));

    // Convert to Celsius
    float tempC = tempK - 273.15;

    Serial.print("T");
    Serial.print(i);
    Serial.print(": ");
    Serial.print(tempC, 1);  // 1 decimal place
    Serial.print(" C  ");
  }

  Serial.println();
  delay(60000);  // 1 reading 60 seconds, 60,000
}
