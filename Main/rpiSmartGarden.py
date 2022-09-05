#!/usr/bin/env python

"""rpiSmartGarden.py: This script queries RPi Smart Garden sensors, processes data and inserts it into SQL database.
RPi Smart Garden peripherals are controlled using logical reasoning based on the processed data."""

__author__      = "40176844"

# Library Imports
import time
import board
import busio
import RPi.GPIO as GPIO
import neopixel
import adafruit_dht
import adafruit_bh1750
import mysql.connector
import adafruit_ads1x15.ads1015 as ADS
from adafruit_ads1x15.analog_in import AnalogIn

# Create I2C bus
i2c = busio.I2C(board.SCL, board.SDA)

# Initialise ADC, it is using I2C
ads = ADS.ADS1015(i2c)

# Initialise light sensor, it is using I2C
light_sens = adafruit_bh1750.BH1750(i2c)

# Initialise capacitive moisture sensor, it is on channel A0 through the ADC
moisture_sens = AnalogIn(ads, ADS.P0)

# Initialise DHT22 sensor, it is connected to GPIO 17
dht_sens = adafruit_dht.DHT22(board.D26, use_pulseio=False)

# Neopixel Setup, connected to GPIO 21
pixel_pin = board.D21
num_pixels = 8
ORDER = neopixel.GRB

pixels = neopixel.NeoPixel(
    pixel_pin, num_pixels, brightness=0.2, auto_write=True, pixel_order=ORDER
)

# MySQL Database Connection
mydb = mysql.connector.connect(
  host="localhost",
  user="40176844",
  password="banana12",
  database="RPiSmartGardenDB"
)

# Neopixel colour variables
# Normal operating colour
def neopixel_normal():
    pixels.fill((0, 255, 0))
    pixels.show()

# Exception colour
def neopixel_exception():
    pixels.fill((255, 0, 0))
    pixels.show()

def neopixel_water():
    pixels.fill((0, 255, 255))
    pixels.show()

# GPIO Assignment
# GPIO I2C SDA - ADC, BH1750 (Light Sensor)
# GPIO I2C SCL - ADC, BH1750 (Light Sensor)
# GPIO 26 - DHT22 (Temperature and Humidity Sensor)
# GPIO 21 - Neopixels
# GPIO 23 - HC-SR04 (Ultrasonic Sensor) TRIG
# GPIO 24 - HC-SR04 (Ultrasonic Sensor) ECHO
# GPIO 25 - 5V Relay

while True:
    try:
        print('=====')
        print('J.A.R.V.I.S Raspberry Pi Smart Garden')
        print('Console Output Log')
        print('=====')

        # Set pump relay to default off - relay on GPIO 21
        GPIO.setmode(GPIO.BCM)
        GPIO.setup(25, GPIO.OUT)
        GPIO.output(25, GPIO.HIGH)


        # Sensor comm check and sensor outputs
        print("== Sensor comm check... ==")
        comm_check_moisture_sens = moisture_sens.value
        comm_check_light_sens = light_sens.lux
        comm_check_dht22_temperature = dht_sens.temperature
        comm_check_dht22_humidity = dht_sens.humidity
        print("Success")


        print("\n== Reading sensors and calculating averages... ==")
        # DHT22 temperature sensor
        temperature_reading_1 = dht_sens.temperature
        time.sleep(3)
        temperature_reading_2 = dht_sens.temperature
        time.sleep(3)
        temperature_reading_3 = dht_sens.temperature

        temperature_avg_calc = ((temperature_reading_1+temperature_reading_2+temperature_reading_3)/3)

        temperature_raw_input = round(temperature_avg_calc, 1)


        # DHT22 humidity sensor
        humidity_reading_1 = dht_sens.humidity
        time.sleep(3)
        humidity_reading_2 = dht_sens.humidity
        time.sleep(3)
        humidity_reading_3 = dht_sens.humidity

        humidity_avg_calc = ((humidity_reading_1+humidity_reading_2+humidity_reading_3)/3)

        humidity_raw_input = round(humidity_avg_calc)


        # Ultrasonic water tank sensor
        GPIO.setmode(GPIO.BCM)
        # TRIG on GPIO 23 - ECHO on GPIO 24
        GPIO.setup(23, GPIO.OUT)
        GPIO.setup(24, GPIO.IN)

        # TRIG
        GPIO.output(23, GPIO.LOW)
        time.sleep(2)
        GPIO.output(23, GPIO.HIGH)
        time.sleep(0.00001)
        GPIO.output(23, GPIO.LOW)

        # ECHO
        while GPIO.input(24) == 0:
            pulse_start = time.time()

        while GPIO.input(24) == 1:
            pulse_end = time.time()

        # Conversion from pulse duration to cm
        pulse_duration = pulse_end - pulse_start
        water_level_tank_distance = pulse_duration * 17150
        water_tank_level_reading_1 = round(water_level_tank_distance, 2)
        time.sleep(0.2)

        # TRIG
        GPIO.output(23, GPIO.LOW)
        time.sleep(2)
        GPIO.output(23, GPIO.HIGH)
        time.sleep(0.00001)
        GPIO.output(23, GPIO.LOW)

        # ECHO
        while GPIO.input(24) == 0:
            pulse_start = time.time()

        while GPIO.input(24) == 1:
            pulse_end = time.time()

        # Conversion from pulse duration to cm
        pulse_duration = pulse_end - pulse_start
        water_level_tank_distance = pulse_duration * 17150
        water_tank_level_reading_2 = round(water_level_tank_distance, 2)
        time.sleep(0.2)

        # TRIG
        GPIO.output(23, GPIO.LOW)
        time.sleep(2)
        GPIO.output(23, GPIO.HIGH)
        time.sleep(0.00001)
        GPIO.output(23, GPIO.LOW)

        # ECHO
        while GPIO.input(24) == 0:
            pulse_start = time.time()

        while GPIO.input(24) == 1:
            pulse_end = time.time()

        # Conversion from pulse duration to cm
        pulse_duration = pulse_end - pulse_start
        water_level_tank_distance = pulse_duration * 17150
        water_tank_level_reading_3 = round(water_level_tank_distance, 2)

        water_tank_level_avg_calc = ((water_tank_level_reading_1+water_tank_level_reading_2+water_tank_level_reading_3)/3)

        water_tank_level_raw_input = round(water_tank_level_avg_calc, 2)


        # Capacitive soil moisture sensor
        soil_moisture_reading_1 = moisture_sens.value
        time.sleep(0.2)
        soil_moisture_reading_2 = moisture_sens.value
        time.sleep(0.2)
        soil_moisture_reading_3 = moisture_sens.value
        time.sleep(0.2)
        soil_moisture_reading_4 = moisture_sens.value
        time.sleep(0.2)
        soil_moisture_reading_5 = moisture_sens.value
        time.sleep(0.2)
        soil_moisture_reading_6 = moisture_sens.value
        time.sleep(0.2)
        soil_moisture_reading_7 = moisture_sens.value
        time.sleep(0.2)
        soil_moisture_reading_8 = moisture_sens.value
        time.sleep(0.2)
        soil_moisture_reading_9 = moisture_sens.value
        time.sleep(0.2)
        soil_moisture_reading_10 = moisture_sens.value

        soil_moisture_avg_calc = ((soil_moisture_reading_1+soil_moisture_reading_2+soil_moisture_reading_3+
                                 soil_moisture_reading_4+soil_moisture_reading_5+soil_moisture_reading_6+
                                 soil_moisture_reading_7+soil_moisture_reading_8+soil_moisture_reading_9+
                                 soil_moisture_reading_10)/10)

        soil_moisture_raw_input = round(soil_moisture_avg_calc)


        # Light sensor
        light_reading_1 = light_sens.lux
        time.sleep(0.2)
        light_reading_2 = light_sens.lux
        time.sleep(0.2)
        light_reading_3 = light_sens.lux
        time.sleep(0.2)
        light_reading_4 = light_sens.lux
        time.sleep(0.2)
        light_reading_5 = light_sens.lux
        time.sleep(0.2)
        light_reading_6 = light_sens.lux
        time.sleep(0.2)
        light_reading_7 = light_sens.lux
        time.sleep(0.2)
        light_reading_8 = light_sens.lux
        time.sleep(0.2)
        light_reading_9 = light_sens.lux
        time.sleep(0.2)
        light_reading_10 = light_sens.lux

        light_avg_calc = ((light_reading_1+light_reading_2+light_reading_3+light_reading_4+light_reading_5+
                                  light_reading_6+light_reading_7+light_reading_8+light_reading_9+light_reading_10)/10)

        light_raw_input = round(light_avg_calc)


        print("Success")


        # Sensor out of range failure check
        print("\n== Raw input data out of range check... ==")

        if soil_moisture_raw_input < 6000:
            raise ValueError('soil_moisture_raw_input out of range')
        elif soil_moisture_raw_input > 18000:
            raise ValueError('soil_moisture_raw_input out of range')
        else:
            print("Success")


        print("\n== Processing sensor data into a user readable format... ==")
        # Water tank level percentage calculation
        # Max input distance (minimum tank level [furthest from sensor])
        water_tank_max_level = 16

        # Min input distance (maximum tank level [closest to sensor])
        water_tank_min_level = 8.5

        if water_tank_level_raw_input < water_tank_min_level:
            water_tank_level_raw_input = water_tank_min_level

        if water_tank_level_raw_input > water_tank_max_level:
            water_tank_level_raw_input = water_tank_max_level

        water_tank_level_percentage_calc = (water_tank_level_raw_input - water_tank_min_level) / (
                water_tank_max_level - water_tank_min_level)
        water_tank_level_raw_percentage = (1 - water_tank_level_percentage_calc) * 100
        water_tank_level_percentage = round(water_tank_level_raw_percentage)


        # Soil moisture percentage calculation
        if soil_moisture_raw_input > 15000:
            soil_moisture_raw_input = 15000

        if soil_moisture_raw_input < 8000:
            soil_moisture_raw_input = 8000

        # Max input value through ADC = 12000 (0% wet)
        # Min input value through ADC = 7000 (100% wet)
        soil_moisture_max_raw_value = 15000
        soil_moisture_min_raw_value = 8000

        # Calculation to find % of input value between max and min values
        soil_moisture_percentage_calc = (soil_moisture_raw_input - soil_moisture_min_raw_value) / (
                soil_moisture_max_raw_value - soil_moisture_min_raw_value)
        soil_moisture_raw_percentage = (1 - soil_moisture_percentage_calc) * 100
        soil_moisture_percentage = round(soil_moisture_raw_percentage)

        # Light sensor
        light_rounded = round(light_raw_input)

        print("Success")


        # Assigning variables
        final_soil_moisture = soil_moisture_percentage
        final_temperature = temperature_raw_input
        final_humidity = humidity_raw_input
        final_light_level = light_rounded
        final_water_tank_level = water_tank_level_percentage


        # Water pump
        # Pump will only activate when moisture level is between 0% - 44% and water tank is not empty
        print("\n== Water tank level check... ==")

        if final_water_tank_level < 15:
            db_water_log = 0
            print("Water tank below 15% which is minimum level, unable to dispense water until filled")

        else:
            print("Success")
            print("\n== Dispensing water via pump... ==")
            # 45% - 100%
            if final_soil_moisture in range(45, 101):
                db_water_log = 0
                print("Success - no water needed")

            # 40% - 44%
            elif final_soil_moisture in range(40, 45):
                db_water_log = 5.0
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(3)
                GPIO.output(25, GPIO.HIGH)
                print("Success")

            # 35% - 39%
            elif final_soil_moisture in range(35, 40):
                db_water_log = 7.5
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(4)
                GPIO.output(25, GPIO.HIGH)
                print("Success")

            # 30% - 34%
            elif final_soil_moisture in range(30, 35):
                db_water_log = 10.0
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(5)
                GPIO.output(25, GPIO.HIGH)
                print("Success")

            # 25% - 29%
            elif final_soil_moisture in range(25, 30):
                db_water_log = 12.5
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(6)
                GPIO.output(25, GPIO.HIGH)
                print("Success")

            # 20% - 24%
            elif final_soil_moisture in range(20, 25):
                db_water_log = 15.0
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(7)
                GPIO.output(25, GPIO.HIGH)
                print("Success")

            # 10% - 19%
            elif final_soil_moisture in range(10, 20):
                db_water_log = 17.5
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(8)
                GPIO.output(25, GPIO.HIGH)
                print("Success")

            # 0% - 9%
            elif final_soil_moisture in range(0, 10):
                db_water_log = 20.0
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(9)
                GPIO.output(25, GPIO.HIGH)
                print("Success")
            else:
                print('Failure - Unable to determine how much water to dispense')
                db_water_log = 0

        final_water_usage = db_water_log


        # Testing neopixels for water tank level
        #water_level_percentage = 24

        # Neopixels set neopixel_water() if water tank level is low
        print("\n== Setting Neopixel colour and brightness... ==")
        if final_water_tank_level <= 25:
            pixels.deinit()
            pixels = neopixel.NeoPixel(
                pixel_pin, num_pixels, brightness=1, auto_write=True, pixel_order=ORDER
            )
            neopixel_water()
            pixels.show()

        # Neopixels set neopixel_normal() and brightness set depending on lux sensor input
        else:
            if final_light_level <= 5:
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif final_light_level in range(6, 17):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.1, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif final_light_level in range(17, 28):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.2, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif final_light_level in range(28, 39):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.3, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif final_light_level in range(39, 50):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.4, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif final_light_level in range(50, 61):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.5, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif final_light_level in range(61, 72):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.6, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif final_light_level in range(72, 83):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.7, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif final_light_level in range(83, 94):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.8, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif final_light_level in range(94, 105):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.9, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif final_light_level >= 106:
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 1, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            else:
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 1, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()

        print("Success")


        # Print sensor inputs to console
        print("\n== Sensor data: ==")
        print('Temperature: {}°C'.format(final_temperature))
        print('Humidity: {}%'.format(final_humidity))
        print('Soil Moisture Level: {}%'.format(final_soil_moisture))
        print('Raw Soil Moisture Level (for testing/debugging): {}'.format(soil_moisture_raw_input))
        print('Water Tank Level: {}%'.format(final_water_tank_level))
        print('Raw Water Tank Distance (for testing/debugging): {}cm'.format(water_tank_level_raw_input))
        print('Light Level: {} Lux'.format(final_light_level))

        # 90% - 100%
        if final_soil_moisture in range(90, 101):
            print('\nThe moisture level is currently very high')
        # 80% - 89%
        elif final_soil_moisture in range(80, 90):
            print('\nThe moisture level is currently high')
        # 70% - 79%
        elif final_soil_moisture in range(70, 80):
            print('\nThe moisture level is currently slightly high')
        # 45% - 69%
        elif final_soil_moisture in range(45, 70):
            print('\nThe moisture level is currently optimal')
        # 40% - 44%
        elif final_soil_moisture in range(40, 44):
            print('\nThe moisture level is currently slightly low')
            print('5ml of water was dispensed')
        # 35% - 39%
        elif final_soil_moisture in range(35, 40):
            print('\nThe moisture level is currently slightly low')
            print('7.5ml of water was dispensed')
        # 30% - 34%
        elif final_soil_moisture in range(30, 35):
            print('\nThe moisture level is currently slightly low')
            print('10ml of water was dispensed')
        # 25% - 29%
        elif final_soil_moisture in range(25, 30):
            print('\nThe moisture level is currently low')
            print('12.5ml of water was dispensed')
        # 20% - 24%
        elif final_soil_moisture in range(20, 25):
            print('\nThe moisture level is currently low')
            print('15ml of water was dispensed')
        # 10% - 19%
        elif final_soil_moisture in range(10, 19):
            print('\nThe moisture level is currently low')
            print('17.5ml of water was dispensed')
        # 0% - 9%
        elif final_soil_moisture in range(0, 9):
            print('\nThe moisture level is currently very low')
            print('20ml of water was dispensed')
        else:
            print('\nError determining moisture level')


        # Send to MySQL database
        # sensor_data
        print("\n== Sending data to MySQL... ==")
        mycursor = mydb.cursor()
        sql = "INSERT INTO sensor_data (soil_moisture, temperature, humidity, light_level, water_tank_level, water_dispensed) VALUES (%s, %s, %s, %s, %s, %s)"
        val = (final_soil_moisture, final_temperature, final_humidity, final_light_level, final_water_tank_level, final_water_usage)
        mycursor.execute(sql, val)
        mydb.commit()
        print("Success")

        print('\n=====')
        print('End of script, 30 second delay')
        print('=====')
        print('\n')
        print('\n')


        # Sleep delay
        time.sleep(30)

        #GPIO.cleanup()

    except RuntimeError as error:
        print('\n===== RUNTIME ERROR =====')
        print(error.args[0])
        print('\nAn expected error has occurred while reading sensors!')
        print('Retrying in 3 seconds...')
        print('\n')
        print('\n')
        time.sleep(3)
        continue

    except ValueError as error:
        print('\n===== VALUE ERROR =====')
        # Neopixels red
        pixels.deinit()
        pixels = neopixel.NeoPixel(
            pixel_pin, num_pixels, brightness=0.5, auto_write=True, pixel_order=ORDER
        )
        neopixel_exception()
        pixels.show()
        print(error.args[0])
        print('\nAn unexpected value has been read from the sensors!')
        print('The program has been terminated and will need to be restarted manually.')
        print('\n')
        raise

    except Exception as exception:
        print('\n===== EXCEPTION =====')
        # Neopixels red
        pixels.deinit()
        pixels = neopixel.NeoPixel(
            pixel_pin, num_pixels, brightness=0.5, auto_write=True, pixel_order=ORDER
        )
        neopixel_exception()
        pixels.show()

        print(exception.args[0])
        dht_sens.exit()

        print('\nAn unexpected and fatal error has occurred')
        print('The program has been terminated and will need to be restarted manually')
        print('\n')
        raise
