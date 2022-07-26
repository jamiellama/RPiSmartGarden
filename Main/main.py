# Smart garden test code

# Library Imports
import time
import board
import busio
import RPi.GPIO as GPIO
import adafruit_dht
import adafruit_bh1750
import adafruit_ads1x15.ads1015 as ADS
from adafruit_ads1x15.analog_in import AnalogIn

# Create I2C bus
i2c = busio.I2C(board.SCL, board.SDA)

# Initialise ADC, it is using I2C
ads = ADS.ADS1015(i2c)

# Initialise lux sensor, it is using I2C
lux_sens = adafruit_bh1750.BH1750(i2c)

# Initialise capacitive moisture sensor, it is on channel A0 through the ADC
moisture_sens = AnalogIn(ads, ADS.P0)

# Initialise DHT22 sensor, it is connected to GPIO 17
dht_sens = adafruit_dht.DHT22(board.D26, use_pulseio=False)

while True:
    try:
        print('=====')
        print('J.A.R.V.I.S Raspberry Pi Smart Garden')
        print('Reading sensors...')
        print('=====')

        # Set pump relay to off - relay on GPIO 21
        GPIO.setmode(GPIO.BCM)
        GPIO.setup(21, GPIO.OUT)
        GPIO.output(21, GPIO.HIGH)

        # Sensor inputs and check
        moisture_input_value = moisture_sens.value
        raw_light_level = lux_sens.lux
        temperature = dht_sens.temperature
        humidity = dht_sens.humidity

        # Sensor failure check
        if moisture_input_value < 6000:
            raise Exception

        # DHT22
        print('\nTemperature: {}°C'.format(temperature))
        print('\nHumidity: {}%'.format(humidity))

        # Capacitive soil moisture
        # Max input value through ADC = 19100 (0% wet)
        # Min input value through ADC = 8000 (100% wet)
        moisture_max_value = 19100
        moisture_min_value = 8000

        # Percentage calculation
        if moisture_input_value > 19100:
            moisture_input_value = 19100

        if moisture_input_value < 8000:
            moisture_input_value = 8000

        moisture_calculated_percentage = (moisture_input_value - moisture_min_value) / (
                    moisture_max_value - moisture_min_value)
        moisture_raw_percentage = (1 - moisture_calculated_percentage) * 100
        moisture_percentage = round(moisture_raw_percentage)

        print('\nSoil Moisture Level: {}%'.format(moisture_percentage))

        if moisture_percentage == 100:
            print('The moisture level is currently very high')
        elif moisture_percentage in range(95, 100):
            print('The moisture level is currently very high')
        elif moisture_percentage in range(90, 95):
            print('The moisture level is currently very high')
        elif moisture_percentage in range(85, 90):
            print('The moisture level is currently high')
        elif moisture_percentage in range(80, 85):
            print('The moisture level is currently high')
        elif moisture_percentage in range(75, 80):
            print('The moisture level is currently slightly high')
        elif moisture_percentage in range(70, 75):
            print('The moisture level is currently slightly high')
        elif moisture_percentage in range(65, 70):
            print('The moisture level is currently optimal')
        elif moisture_percentage in range(60, 65):
            print('The moisture level is currently optimal')
        elif moisture_percentage in range(55, 60):
            print('The moisture level is currently optimal')
        elif moisture_percentage in range(50, 55):
            print('The moisture level is currently optimal')
        elif moisture_percentage in range(45, 50):
            print('The moisture level is currently optimal')
        elif moisture_percentage in range(40, 45):
            print('The moisture level is currently optimal')
        elif moisture_percentage in range(35, 40):
            print('The moisture level is currently slightly low')
        elif moisture_percentage in range(30, 35):
            print('The moisture level is currently slightly low')
        elif moisture_percentage in range(25, 30):
            print('The moisture level is currently low')
        elif moisture_percentage in range(20, 25):
            print('The moisture level is currently low')
        elif moisture_percentage in range(15, 20):
            print('The moisture level is currently low')
        elif moisture_percentage in range(10, 15):
            print('The moisture level is currently very low')
        elif moisture_percentage in range(5, 10):
            print('The moisture level is currently very low')
        elif moisture_percentage in range(0, 5):
            print('The moisture level is currently very low')
        else:
            print('Error determining moisture level actions')

        # Lux sensor
        light_level = round(raw_light_level)

        print('\nLight Level: {} Lux'.format(light_level))
        print('\n')
        print('\n')

        time.sleep(10)

    except RuntimeError as error:
        # print(error.args[0])
        print('\nProblem occurred reading sensor data!')
        print('Retrying in 2 seconds...')
        print('\n')
        print('\n')
        time.sleep(2.0)
        continue

    except Exception as error:
        dht_sens.exit()
        print('\nReceived unexpected input data!')
        print('This could be due to a possible sensor failure.')
        print('Retrying in 2 seconds...')
        print('\n')
        print('\n')
        time.sleep(2.0)
        continue

    finally:
        GPIO.cleanup()

