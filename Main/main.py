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

# Creating variable for normal operating colour of neopixel
def neopixel_normal():
    pixels.fill((0, 255, 0))
    pixels.show()

# Creating variable for colour of neopixel when there is an exception
def neopixel_exception():
    pixels.fill((255, 0, 0))
    pixels.show()

# Creating variable for colour of neopixel when water tank is empty
def neopixel_water_tank():
    pixels.fill((0, 255, 255))
    pixels.show()

while True:
    try:
        # Set pump relay to off - relay on GPIO 21
        GPIO.setmode(GPIO.BCM)
        GPIO.setup(21, GPIO.OUT)
        GPIO.output(21, GPIO.HIGH)


        # Sensor inputs and connection check
        moisture_input_value = moisture_sens.value
        raw_light_level = lux_sens.lux
        temperature = dht_sens.temperature
        humidity = dht_sens.humidity


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
        print('Reading sensors...')
        print('=====')

        print('\nTemperature: {}°C'.format(temperature))

        print('\nHumidity: {}%'.format(humidity))

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

        print('\nLight Level: {} Lux'.format(light_level))
        print('\n')
        print('\n')

        # Sleep delay
        time.sleep(10)

    except RuntimeError as error:
        # print(error.args[0])
        #pixels.fill((255, 215, 0))
        #pixels.show()
        print('\nProblem occurred reading sensor data!')
        print('Retrying in 2 seconds...')
        print('\n')
        print('\n')
        time.sleep(2.0)
        continue

    except Exception as error:
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

    finally:
        GPIO.cleanup()

