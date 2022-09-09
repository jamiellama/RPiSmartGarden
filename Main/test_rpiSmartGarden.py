import unittest
import rpiSmartGardenFunc

class TestFunc(unittest.TestCase):

    def test_read_temperature(self):
        result = rpiSmartGardenFunc.read_temperature(21.0, 22.3, 23.5)
        self.assertEqual(result, 22.3)

    def test_read_humidity(self):
        result = rpiSmartGardenFunc.read_humidity(40, 50, 60)
        self.assertEqual(result, 50)

    def test_read_water_Tank_level(self):
        result = rpiSmartGardenFunc.read_water_tank_level(0.0012, 0.0020, 0.0009, 0.0015, 0.0010, 0.0017)
        self.assertEqual(result, 12.0)

    def test_read_soil_moisture_level(self):
        result = rpiSmartGardenFunc.read_soil_moisture_level(13042, 12042, 10234, 13521, 12321, 11231, 10212,
                                                             9967, 12345, 12312,)
        self.assertEqual(result, 11723)

    def test_read_light_level(self):
        result = rpiSmartGardenFunc.read_light_level(4056, 5603, 3405, 2405, 6044, 2405, 5603, 5064, 3054, 4532)
        self.assertEqual(result, 4217)

    def test_soil_moisture_out_of_range_check(self):
        try:
            rpiSmartGardenFunc.soil_moisture_out_of_range_check(10000)
        except ValueError:
            self.fail("Out of Range check raised ValueError")

    def test_water_tank_level_percentage_calculation(self):
        result = rpiSmartGardenFunc.water_tank_level_percentage_calculation(12.0)
        self.assertEqual(result, 53)

    def test_soil_moisture_level_percentage_calculation(self):
        result = rpiSmartGardenFunc.soil_moisture_level_percentage_calculation(11400)
        self.assertEqual(result, 51)

    def test_water_pump(self):
        result = rpiSmartGardenFunc.water_pump(50, 30)
        self.assertEqual(result, 10)



if __name__ == '__main__':
    unittest.main()