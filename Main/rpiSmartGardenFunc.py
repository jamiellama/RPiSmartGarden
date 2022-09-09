#!/usr/bin/env python

"""rpiSmartGardenFunc.py: This script is for testing the functions in rpiSmartGarden where they cannot be
directly tested due to library restrictions."""

__author__ = "40176844"

# Library Imports
import time

# Comm Check
def comm_check(a, b, c, d):
    comm_check_moisture_sens = a
    comm_check_light_sens = b
    comm_check_dht22_temperature = c
    comm_check_dht22_humidity = d

# Read sensor functions
def read_temperature(a, b, c):
    temperature_reading_1 = a
    time.sleep(3)
    temperature_reading_2 = b
    time.sleep(3)
    temperature_reading_3 = c

    temperature_avg_calc = ((temperature_reading_1 + temperature_reading_2 + temperature_reading_3) / 3)

    func_temperature_raw_input = round(temperature_avg_calc, 1)

    return func_temperature_raw_input

def read_humidity(a, b, c):
    humidity_reading_1 = a
    time.sleep(3)
    humidity_reading_2 = b
    time.sleep(3)
    humidity_reading_3 = c

    humidity_avg_calc = ((humidity_reading_1 + humidity_reading_2 + humidity_reading_3) / 3)

    func_humidity_raw_input = round(humidity_avg_calc)

    return func_humidity_raw_input

def read_water_tank_level(a, b, c, d, e, f):

    pulse_start = a
    pulse_end = b

    # Conversion from pulse duration to cm
    pulse_duration = pulse_end - pulse_start
    water_level_tank_distance = pulse_duration * 17150
    water_tank_level_reading_1 = round(water_level_tank_distance, 2)
    time.sleep(0.2)

    pulse_start = c
    pulse_end = d

    # Conversion from pulse duration to cm
    pulse_duration = pulse_end - pulse_start
    water_level_tank_distance = pulse_duration * 17150
    water_tank_level_reading_2 = round(water_level_tank_distance, 2)
    time.sleep(0.2)

    pulse_start = e
    pulse_end = f

    # Conversion from pulse duration to cm
    pulse_duration = pulse_end - pulse_start
    water_level_tank_distance = pulse_duration * 17150
    water_tank_level_reading_3 = round(water_level_tank_distance, 2)

    water_tank_level_avg_calc = (
            (water_tank_level_reading_1 + water_tank_level_reading_2 + water_tank_level_reading_3) / 3)

    func_water_tank_level_raw_input = round(water_tank_level_avg_calc, 2)

    return func_water_tank_level_raw_input

def read_soil_moisture_level(a, b, c, d, e, f, g, h, i, j):
    soil_moisture_reading_1 = a
    time.sleep(0.2)
    soil_moisture_reading_2 = b
    time.sleep(0.2)
    soil_moisture_reading_3 = c
    time.sleep(0.2)
    soil_moisture_reading_4 = d
    time.sleep(0.2)
    soil_moisture_reading_5 = e
    time.sleep(0.2)
    soil_moisture_reading_6 = f
    time.sleep(0.2)
    soil_moisture_reading_7 = g
    time.sleep(0.2)
    soil_moisture_reading_8 = h
    time.sleep(0.2)
    soil_moisture_reading_9 = i
    time.sleep(0.2)
    soil_moisture_reading_10 = j

    soil_moisture_avg_calc = ((soil_moisture_reading_1 + soil_moisture_reading_2 + soil_moisture_reading_3 +
                               soil_moisture_reading_4 + soil_moisture_reading_5 + soil_moisture_reading_6 +
                               soil_moisture_reading_7 + soil_moisture_reading_8 + soil_moisture_reading_9 +
                               soil_moisture_reading_10) / 10)

    func_soil_moisture_raw_input = round(soil_moisture_avg_calc)

    return func_soil_moisture_raw_input

def read_light_level(a, b, c, d, e, f, g, h, i, j):
    light_reading_1 = a
    time.sleep(0.2)
    light_reading_2 = b
    time.sleep(0.2)
    light_reading_3 = c
    time.sleep(0.2)
    light_reading_4 = d
    time.sleep(0.2)
    light_reading_5 = e
    time.sleep(0.2)
    light_reading_6 = f
    time.sleep(0.2)
    light_reading_7 = g
    time.sleep(0.2)
    light_reading_8 = h
    time.sleep(0.2)
    light_reading_9 = i
    time.sleep(0.2)
    light_reading_10 = j

    light_avg_calc = ((light_reading_1 + light_reading_2 + light_reading_3 + light_reading_4 + light_reading_5 +
                       light_reading_6 + light_reading_7 + light_reading_8 + light_reading_9 + light_reading_10) / 10)

    light_raw_input = round(light_avg_calc)

    func_light_rounded = round(light_raw_input)

    return func_light_rounded

def soil_moisture_out_of_range_check(soil_moisture_raw_input):

    if soil_moisture_raw_input < 6000:
        raise ValueError('soil_moisture_raw_input out of range')
    elif soil_moisture_raw_input > 18000:
        raise ValueError('soil_moisture_raw_input out of range')
    else:
        print("Success")

def water_tank_level_percentage_calculation(water_tank_level_raw_input):
    # Max input distance (minimum tank level [furthest from sensor])
    water_tank_max_level = 16

    # Min input distance (maximum tank level [closest to sensor])
    water_tank_min_level = 8.5

    new_water_tank_level_raw_input = water_tank_level_raw_input

    if water_tank_level_raw_input < water_tank_min_level:
        new_water_tank_level_raw_input = water_tank_min_level

    if water_tank_level_raw_input > water_tank_max_level:
        new_water_tank_level_raw_input = water_tank_max_level

    water_tank_level_percentage_calc = (new_water_tank_level_raw_input - water_tank_min_level) / (
            water_tank_max_level - water_tank_min_level)
    water_tank_level_raw_percentage = (1 - water_tank_level_percentage_calc) * 100
    func_water_tank_level_percentage = round(water_tank_level_raw_percentage)

    print("Water level calc complete")

    return func_water_tank_level_percentage

def soil_moisture_level_percentage_calculation(soil_moisture_raw_input):
    # Max input value through ADC = 15000 (0% wet)
    soil_moisture_max_raw_value = 15000

    # Min input value through ADC = 8000 (100% wet)
    soil_moisture_min_raw_value = 8000

    new_soil_moisture_raw_input = soil_moisture_raw_input

    if soil_moisture_raw_input > 15000:
        new_soil_moisture_raw_input = 15000

    if soil_moisture_raw_input < 8000:
        new_soil_moisture_raw_input = 8000

    # Calculation to find % of input value between max and min values
    soil_moisture_percentage_calc = (new_soil_moisture_raw_input - soil_moisture_min_raw_value) / (
            soil_moisture_max_raw_value - soil_moisture_min_raw_value)
    soil_moisture_raw_percentage = (1 - soil_moisture_percentage_calc) * 100
    func_soil_moisture_percentage = round(soil_moisture_raw_percentage)

    print("Soil moisture calc complete")

    return func_soil_moisture_percentage

def water_pump(final_water_tank_level, final_soil_moisture):
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
            #GPIO.setmode(GPIO.BCM)
            #GPIO.setup(25, GPIO.OUT)
            #GPIO.output(25, GPIO.LOW)
            time.sleep(3)
            #GPIO.output(25, GPIO.HIGH)
            print("Success")

        # 35% - 39%
        elif final_soil_moisture in range(35, 40):
            db_water_log = 7.5
            #GPIO.setmode(GPIO.BCM)
            #GPIO.setup(25, GPIO.OUT)
            #GPIO.output(25, GPIO.LOW)
            time.sleep(4)
            #GPIO.output(25, GPIO.HIGH)
            print("Success")

        # 30% - 34%
        elif final_soil_moisture in range(30, 35):
            db_water_log = 10.0
            #GPIO.setmode(GPIO.BCM)
            #GPIO.setup(25, GPIO.OUT)
            #GPIO.output(25, GPIO.LOW)
            time.sleep(5)
            #GPIO.output(25, GPIO.HIGH)
            print("Success")

        # 25% - 29%
        elif final_soil_moisture in range(25, 30):
            db_water_log = 12.5
            #GPIO.setmode(GPIO.BCM)
            #GPIO.setup(25, GPIO.OUT)
            #GPIO.output(25, GPIO.LOW)
            time.sleep(6)
            #GPIO.output(25, GPIO.HIGH)
            print("Success")

        # 20% - 24%
        elif final_soil_moisture in range(20, 25):
            db_water_log = 15.0
            #GPIO.setmode(GPIO.BCM)
            #GPIO.setup(25, GPIO.OUT)
            #GPIO.output(25, GPIO.LOW)
            time.sleep(7)
            #GPIO.output(25, GPIO.HIGH)
            print("Success")

        # 10% - 19%
        elif final_soil_moisture in range(10, 20):
            db_water_log = 17.5
            #GPIO.setmode(GPIO.BCM)
            #GPIO.setup(25, GPIO.OUT)
            #GPIO.output(25, GPIO.LOW)
            time.sleep(8)
            #GPIO.output(25, GPIO.HIGH)
            print("Success")

        # 0% - 9%
        elif final_soil_moisture in range(0, 10):
            db_water_log = 20.0
            #GPIO.setmode(GPIO.BCM)
            #GPIO.setup(25, GPIO.OUT)
            #GPIO.output(25, GPIO.LOW)
            time.sleep(9)
            #GPIO.output(25, GPIO.HIGH)
            print("Success")
        else:
            print('Failure - Unable to determine how much water to dispense')
            db_water_log = 0

    func_final_water_usage = db_water_log

    return func_final_water_usage