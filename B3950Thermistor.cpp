#include "B3950Thermistor.h"

const float B3950Thermistor::temperatureValues[B3950Thermistor::numDataPoints] = 
  {   
    -40, -39, -38, -37, -36, -35, -34, -33, -32, -31, 
    -30, -29, -28, -27, -26, -25, -24, -23, -22, -21, 
    -20, -19, -18, -17, -16, -15, -14, -13, -12, -11, 
    -10, -9,  -8,  -7,  -6,  -5,  -4,  -3,  -2,  -1,  
    0,    1,   2,   3,   4,   5,   6,   7,   8,   9, 
    10,  11,  12,  13,  14,  15,  16,  17,  18,  19, 
    20,  21,  22,  23,  24,  25,  26,  27,  28,  29, 
    30,  31,  32,  33,  34,  35,  36,  37,  38,  39, 
    40,  41,  42,  43,  44,  45,  46,  47,  48,  49, 
    50,  51,  52,  53,  54,  55,  56,  57,  58,  59, 
    60,  61,  62,  63,  64,  65,  66,  67,  68,  69, 
    70,  71,  72,  73,  74,  75,  76,  77,  78,  79, 
    80,  81,  82,  83,  84,  85,  86,  87,  88,  89, 
    90,  91,  92,  93,  94,  95,  96,  97,  98,  99, 
    100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 
    110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 
    120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 
    130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 
    140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 
    150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 
    160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 
    170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 
    180, 181, 182, 183, 184, 185, 186, 187, 188, 189, 
    190, 191, 192, 193, 194, 195, 196, 197, 198, 199, 200
    
    };
    
const float B3950Thermistor::resistanceValues[B3950Thermistor::numDataPoints] = 
{ 
    277.2, 263.6, 250.1, 236.8, 224.0, 211.5, 199.6, 188.1, 177.3, 167.0, 
    157.2, 148.1, 139.4, 131.3, 123.7, 116.6, 110.0, 103.7, 97.9,  92.50, 
    87.43, 82.79, 78.44, 74.36, 70.53, 66.92, 63.54, 60.34, 57.33, 54.50, 
    51.82, 49.28, 46.89, 44.62, 42.48, 40.45, 38.53, 36.70, 34.97, 33.33, 
    31.77, 30.25, 28.82, 27.45, 26.16, 24.94, 23.77, 22.67, 21.62, 20.63, 
    19.68, 18.78, 17.93, 17.12, 16.35, 15.62, 14.93, 14.26, 13.63, 13.04, 
    12.47, 11.92, 11.41, 10.91, 10.45, 10.00, 9.575, 9.170, 8.784, 8.416, 
    8.064, 7.730, 7.410, 7.106, 6.815, 6.538, 6.273, 6.020, 5.778, 5.548, 
    5.327, 5.117, 4.915, 4.723, 4.539, 4.363, 4.195, 4.034, 3.880, 3.733, 
    3.592, 3.457, 3.328, 3.204, 3.086, 2.972, 2.863, 2.759, 2.659, 2.564, 
    2.472, 2.384, 2.299, 2.218, 2.141, 2.066, 1.994, 1.926, 1.860, 1.796, 
    1.735, 1.677, 1.621, 1.567, 1.515, 1.465, 1.417, 1.371, 1.326, 1.284, 
    1.243, 1.203, 1.165, 1.128, 1.093, 1.059, 1.027, 0.9955, 0.9654, 0.9363, 
    0.9083, 0.8812, 0.8550, 0.8297, 0.8052, 0.7816, 0.7587, 0.7366, 0.7152, 0.6945, 
    0.6744, 0.6558, 0.6376, 0.6199, 0.6026, 0.5858, 0.5694, 0.5535, 0.5380, 0.5229, 
    0.5083, 0.4941, 0.4803, 0.4669, 0.4539, 0.4412, 0.4290, 0.4171, 0.4055, 0.3944, 
    0.3835, 0.3730, 0.3628, 0.3530, 0.3434, 0.3341, 0.3253, 0.3167, 0.3083, 0.3002, 
    0.2924, 0.2848, 0.2774, 0.2702, 0.2633, 0.2565, 0.2500, 0.2437, 0.2375, 0.2316, 
    0.2258, 0.2202, 0.2148, 0.2095, 0.2044, 0.1994, 0.1946, 0.1900, 0.1855, 0.1811, 
    0.1769, 0.1728, 0.1688, 0.1650, 0.1612, 0.1576, 0.1541, 0.1507, 0.1474, 0.1441, 
    0.1410, 0.1379, 0.1350, 0.1321, 0.1293, 0.1265, 0.1239, 0.1213, 0.1187, 0.1163, 
    0.1139, 0.1115, 0.1092, 0.1070, 0.1048, 0.1027, 0.1006, 0.0986, 0.0966, 0.0947, 
    0.0928, 0.0909, 0.0891, 0.0873, 0.0856, 0.0839, 0.0822, 0.0806, 0.0790, 0.0774, 
    0.0759, 0.0743, 0.0729, 0.0714, 0.0700, 0.0686, 0.0672, 0.0658, 0.0645, 0.0631, 0.0619

};

B3950Thermistor::B3950Thermistor(int pin, float seriesResistor, float vcc) : _pin(pin), _seriesResistor(seriesResistor), _vcc(vcc) {}

float B3950Thermistor::readResistanceKOhms() {
    int adcValue = analogRead(_pin);
    return readThermistorResistance(adcValue);
}

float B3950Thermistor::readTemperatureCelsius() {
    float resistance = readResistanceKOhms();
    return interpolateTemperature(resistance);
}

float B3950Thermistor::readTemperatureFahrenheit() {
    float temperatureCelsius = readTemperatureCelsius();
    return temperatureCelsius * 9.0/5.0 + 32.0;  // Convert Celsius to Fahrenheit
}

float B3950Thermistor::readThermistorResistance(int adcValue) {
    float voltage = adcValue * (_vcc / 1023.0);
//    float thermistorResistance = _seriesResistor * (_vcc / voltage - 1.0);
    float thermistorResistance = (_seriesResistor * voltage) / (_vcc - voltage);
    return thermistorResistance / 1000;   // Convert the resistor into kOhms
}

float B3950Thermistor::interpolateTemperature(float resistance) {
    // If resistance is outside the range, return either minimum or maximum temperature
    if (resistance >= resistanceValues[0]) return temperatureValues[0];
    if (resistance <= resistanceValues[numDataPoints - 1]) return temperatureValues[numDataPoints - 1];

    // Find the two data points for interpolation
    for (int i = 0; i < numDataPoints - 1; i++) {
        if (resistance <= resistanceValues[i] && resistance >= resistanceValues[i + 1]) {
            // Linear interpolation formula: 
            // y = y0 + (x - x0) * (y1 - y0) / (x1 - x0)
            return temperatureValues[i] + (resistance - resistanceValues[i]) * (temperatureValues[i + 1] - temperatureValues[i]) / (resistanceValues[i + 1] - resistanceValues[i]);
        }
    }
    return 0.0; // Should not reach here
}

