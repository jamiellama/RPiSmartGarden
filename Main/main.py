#Raspberry Pi Smart Garden
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

while True:
    try:
        # Set pump relay to off - relay on GPIO 21
        #GPIO.setmode(GPIO.BCM)
        #GPIO.setup(25, GPIO.OUT)
        #GPIO.output(25, GPIO.HIGH)


        # Sensor inputs and connection check
        moisture_input_value = moisture_sens.value
        raw_light_level = lux_sens.lux
        temperature = dht_sens.temperature
        humidity = dht_sens.humidity
        water_tank_level = 0


        # Sensor failure check
        if moisture_input_value < 7000:
            raise Exception


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
        # Pump will only activate when moisture level is between 0% - 44%

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
            print('Error determining moisture level')
            db_water_log = 0


        # Lux sensor - round up input value to int
        light_level = round(raw_light_level)


        # Neopixel set neopixel_normal() and brightness depending on lux sensor input
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

        # Print to console
        print('=====')
        print('J.A.R.V.I.S Raspberry Pi Smart Garden')
        print('Console Output')
        print('Reading sensors...')
        print('=====')

        print('\nTemperature: {}°C'.format(temperature))

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


        print('\nLight Level: {} Lux'.format(light_level))
        print('\n')
        print('\n')

        # Send to MySQL
        mycursor = mydb.cursor()

        sql = "INSERT INTO sensor_data (soil_moisture, temperature, humidity, light_level, water_tank_level) VALUES (%s, %s, %s, %s, %s)"
        val = (moisture_percentage, temperature, humidity, light_level, water_tank_level)
        mycursor.execute(sql, val)

        mydb.commit()

        print('Data sent to database')

        # Sleep delay
        time.sleep(60)

        #GPIO.cleanup()

    except RuntimeError as error:
        print('===== RUNTIME ERROR =====')
        print(error.args[0])
        #pixels.fill((255, 215, 0))
        #pixels.show()
        print('\nProblem occurred reading sensor data!')
        print('Retrying in 2 seconds...')
        print('\n')
        print('\n')
        time.sleep(2.0)
        continue

    except Exception as error:
        print('===== EXCEPTION =====')
        dht_sens.exit()

        pixels.deinit()
        pixels = neopixel.NeoPixel(
            pixel_pin, num_pixels, brightness=0.5, auto_write=True, pixel_order=ORDER
        )
        neopixel_exception()
        pixels.show()

        print('\nReceived unexpected input data!')
        print('This could be due to a possible sensor failure.')
        print('Retrying in 2 seconds...')
        print('\n')
        print('\n')
        time.sleep(2.0)
        continue

