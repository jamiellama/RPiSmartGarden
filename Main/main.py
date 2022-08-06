#J.A.R.V.I.S Raspberry Pi Smart Garden
#Jamie 40176844

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

# Initialise lux sensor, it is using I2C
lux_sens = adafruit_bh1750.BH1750(i2c)

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


        # Sensor inputs and connection check
        print("\nSensor connection check...")
        moisture_input_value = moisture_sens.value
        raw_light_level = lux_sens.lux
        temperature = dht_sens.temperature
        humidity = dht_sens.humidity

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
        water_level_distance = pulse_duration * 17150
        water_level_distance = round(water_level_distance, 2)
        print("Success")

        # Sensor failure check
        print("\nInput data check...")
        if moisture_input_value < 7000:
            raise Exception
        print("Success")

        # Water tank level percentage calculation
        # Max input distance (minimum tank level [furthest from sensor])
        water_max_level = 16.5

        # Min input distance (maximum tank level [closest to sensor])
        water_min_level = 6.8

        if water_level_distance < water_min_level:
            water_level_distance = water_min_level

        if water_level_distance > water_max_level:
            water_level_distance = water_max_level

        water_calculated_percentage = (water_level_distance - water_min_level) / (
                water_max_level - water_min_level)
        water_raw_percentage = (1 - water_calculated_percentage) * 100
        water_level_percentage = round(water_raw_percentage)


        # Capacitive soil moisture percentage calculation
        if moisture_input_value > 19100:
            moisture_input_value = 19100

        if moisture_input_value < 8000:
            moisture_input_value = 8000

        # Max input value through ADC = 19100 (0% wet)
        # Min input value through ADC = 8000 (100% wet)
        moisture_max_value = 19100
        moisture_min_value = 8000

        # Calculation to find % of input value between max and min values
        moisture_calculated_percentage = (moisture_input_value - moisture_min_value) / (
                    moisture_max_value - moisture_min_value)
        moisture_raw_percentage = (1 - moisture_calculated_percentage) * 100
        moisture_percentage = round(moisture_raw_percentage)

        # Water pump
        # Pump will only activate when moisture level is between 0% - 44% and water tank is not empty
        print("\n Water tank level check...")

        if water_level_percentage < 15:
            print("Water tank below 15% which is minimum level, unable to dispense water until filled")

        else:

            # 40% - 44%
            if moisture_percentage in range(40, 45):
                db_water_log = 5.0
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(3)
                GPIO.output(25, GPIO.HIGH)

            # 35% - 39%
            elif moisture_percentage in range(35, 40):
                db_water_log = 7.5
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(4)
                GPIO.output(25, GPIO.HIGH)

            # 30% - 34%
            elif moisture_percentage in range(30, 35):
                db_water_log = 10.0
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(5)
                GPIO.output(25, GPIO.HIGH)

            # 25% - 29%
            elif moisture_percentage in range(25, 30):
                db_water_log = 12.5
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(6)
                GPIO.output(25, GPIO.HIGH)

            # 20% - 24%
            elif moisture_percentage in range(20, 25):
                db_water_log = 15.0
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(7)
                GPIO.output(25, GPIO.HIGH)

            # 10% - 19%
            elif moisture_percentage in range(10, 19):
                db_water_log = 17.5
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(8)
                GPIO.output(25, GPIO.HIGH)

            # 0% - 9%
            elif moisture_percentage in range(0, 10):
                db_water_log = 20.0
                GPIO.setmode(GPIO.BCM)
                GPIO.setup(25, GPIO.OUT)
                GPIO.output(25, GPIO.LOW)
                time.sleep(9)
                GPIO.output(25, GPIO.HIGH)
            else:
                print('Failure determining water amount, no water has been dispensed')
                db_water_log = 0
        print("Success")


        # Lux sensor - round up input value to int
        light_level = round(raw_light_level)


        # Testing neopixels for water tank level
        #water_level_percentage = 24

        # Neopixels set neopixel_water() if water tank level is low
        print("\nSetting Neopixel colour and brightness")
        if water_level_percentage <= 25:
            pixels.deinit()
            pixels = neopixel.NeoPixel(
                pixel_pin, num_pixels, brightness=1, auto_write=True, pixel_order=ORDER
            )
            neopixel_water()
            pixels.show()

        # Neopixels set neopixel_normal() and brightness set depending on lux sensor input
        else:
            if light_level <= 5:
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif light_level in range(6, 17):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.1, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif light_level in range(17, 28):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.2, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif light_level in range(28, 39):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.3, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif light_level in range(39, 50):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.4, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif light_level in range(50, 61):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.5, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif light_level in range(61, 72):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.6, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif light_level in range(72, 83):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.7, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif light_level in range(83, 94):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.8, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif light_level in range(94, 105):
                pixels.deinit()
                pixels = neopixel.NeoPixel(
                    pixel_pin, num_pixels, brightness = 0.9, auto_write = True, pixel_order = ORDER
                )
                neopixel_normal()
                pixels.show()
            elif light_level >= 106:
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
        print("\n Data Inputs:")
        print('Temperature: {}°C'.format(temperature))

        print('\nHumidity: {}%'.format(humidity))

        print('\nSoil Moisture Level: {}%'.format(moisture_percentage))

        # 90% - 100%
        if moisture_percentage in range(90, 101):
            print('The moisture level is currently very high')
        # 80% - 89%
        elif moisture_percentage in range(80, 90):
            print('The moisture level is currently high')
        # 70% - 79%
        elif moisture_percentage in range(70, 80):
            print('The moisture level is currently slightly high')
        # 45% - 69%
        elif moisture_percentage in range(45, 70):
            print('The moisture level is currently optimal')
        # 40% - 44%
        elif moisture_percentage in range(40, 44):
            print('The moisture level is currently slightly low')
            print('5ml of water was dispensed')
        # 35% - 39%
        elif moisture_percentage in range(35, 40):
            print('The moisture level is currently slightly low')
            print('7.5ml of water was dispensed')
        # 30% - 34%
        elif moisture_percentage in range(30, 35):
            print('The moisture level is currently slightly low')
            print('10ml of water was dispensed')
        # 25% - 29%
        elif moisture_percentage in range(25, 30):
            print('The moisture level is currently low')
            print('12.5ml of water was dispensed')
        # 20% - 24%
        elif moisture_percentage in range(20, 25):
            print('The moisture level is currently low')
            print('15ml of water was dispensed')
        # 10% - 19%
        elif moisture_percentage in range(10, 19):
            print('The moisture level is currently low')
            print('17.5ml of water was dispensed')
        # 0% - 9%
        elif moisture_percentage in range(0, 9):
            print('The moisture level is currently very low')
            print('20ml of water was dispensed')
        else:
            print('Error determining moisture level')

        print('\n Water Tank Level:{}%'.format(water_level_percentage))

        print('\nLight Level: {} Lux'.format(light_level))


        # Send to MySQL database
        # sensor_data
        print("\n Sending data to MySQL...")
        mycursor = mydb.cursor()
        sql = "INSERT INTO sensor_data (soil_moisture, temperature, humidity, light_level, water_tank_level, water_dispensed) VALUES (%s, %s, %s, %s, %s, %s)"
        val = (moisture_percentage, temperature, humidity, light_level, water_level_percentage, db_water_log)
        mycursor.execute(sql, val)
        mydb.commit()
        print("Success")

        print("End of script, 60 second delay")
        print('\n')
        print('\n')
        # Sleep delay
        time.sleep(60)

        #GPIO.cleanup()

    except RuntimeError as error:
        print('\n===== RUNTIME ERROR =====')
        print(error.args[0])
        print('\nAn expected error has occurred while reading sensors!')
        print('Retrying in 2 seconds...')
        print('\n')
        print('\n')
        time.sleep(2.0)
        continue

    except Exception as exception:
        print('\n===== EXCEPTION =====')
        dht_sens.exit()

        # Neopixels red
        pixels.deinit()
        pixels = neopixel.NeoPixel(
            pixel_pin, num_pixels, brightness=0.5, auto_write=True, pixel_order=ORDER
        )
        neopixel_exception()
        pixels.show()

        print('\nAn unexpected and fatal error has occurred')
        print('This is likely to have been caused by hardware failure or a database connection error')
        print('The program has been terminated and will need to be restarted manually')
        print(exception.args[0])
        print('\n')
        print('\n')
        raise

