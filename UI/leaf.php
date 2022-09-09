<?php include "dbconn.php";?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leaf</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="icon" href="https://i.ibb.co/mtFCL4R/RPi-Logo-Round.png">
    <style type="text/css">
        body {
            font-family: "Trebuchet MS", Arial;
            background-color: #d6e6dd;
        }

        @media (min-width: 576px) {
            .h-sm-100 {
            height: 100%;
            }
        }

        #mapped_img {
            max-width: 100%;
            max-height: 100%;
        }

        .nav-link {
            color: white;
        }

        .nav-link:hover {
            color: #E8E8E8;
        }

        .content {
            max-width: 1000px;
            margin: auto;
        }

        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 15%;
        }

        .speech {
            margin: 0 0 10px 0;
        }

        .bubble {
            position: relative;
            background: #198754;
            padding: 20px;
            color:#fff;
            border-radius: 3px;
            margin-left: 20px;
        }

        .bubble:after {
            content: "";
            display: block;
            position: absolute;
            left: -15px;
            top: 15px;
            width: 0;
            height: 0;
            border-top: 8px solid transparent;
            border-bottom: 8px solid transparent;
            border-right: 15px solid #198754;

        }

    </style>
</head>


<div class="container-fluid overflow-hidden">
    <div class="row vh-100 overflow-auto">
        <div class="col-12 col-sm-3 col-xl-2 px-sm-2 px-0 bg-success d-flex sticky-top">
            <div class="d-flex flex-sm-column flex-row flex-grow-1 align-items-center align-items-sm-start px-3 pt-2 text-white">
                <a href="/index.php" class="d-flex align-items-center pb-sm-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <img src="https://i.ibb.co/mtFCL4R/RPi-Logo-Round.png" class="d-none d-md-block" alt="RPi-Smart-Garden-Logo" width="110px" height="110px">
                    <img src="https://i.ibb.co/mtFCL4R/RPi-Logo-Round.png" class="d-md-none" alt="RPi-Smart-Garden-Logo" width="30px" height="30px">
                </a>
                <ul class="nav nav-pills flex-sm-column flex-row flex-nowrap flex-shrink-1 flex-sm-grow-0 flex-grow-1 mb-sm-auto mb-0 justify-content-center align-items-center align-items-sm-start" id="menu">
                    <li class="nav-item">
                        <a href="/index.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-house-door"></i><span class="ms-1 d-none d-sm-inline">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/visuals.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-bar-chart-line"></i><span class="ms-1 d-none d-sm-inline">Visuals</span></a>
                    </li>
                    <li>
                        <a href="/leaf.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-chat-dots"></i><span class="ms-1 d-none d-sm-inline">Leaf</span></a>
                    </li>
                    <li>
                        <a href="/data.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-file-earmark-spreadsheet"></i><span class="ms-1 d-none d-sm-inline">Data Log</span></a>
                    </li>
                    <li>
                        <a href="/info.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-info-circle"></i><span class="ms-1 d-none d-sm-inline">Info and Support</span></a>
                    </li>
                    <li>
                        <a href="/settings.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-gear"></i><span class="ms-1 d-none d-sm-inline">Settings</span></a>
                    </li>
                </ul>
            </div>
        </div>

        <body>
            <?php
                // get last updated timestamp for page header
                $header_current_timestamp_request = "SELECT * FROM sensor_data ORDER BY data_id DESC LIMIT 1";
                $header_current_timestamp_result = mysqli_query($conn, $header_current_timestamp_request);

                while ($row = mysqli_fetch_array($header_current_timestamp_result)) {
                    $header_current_timestamp_raw = $row['timestamp'];
                }
                $datetimestr = strtotime($header_current_timestamp_raw);
                $header_current_timestamp = date("d/m/y g:i:s A", $datetimestr);
                $time_difference = (strtotime("now")-strtotime($header_current_timestamp_raw));
                $time_difference_minutes_raw = ($time_difference / 60);
                $time_difference_minutes = round($time_difference_minutes_raw);

                                // Current, high, low and average sql queries
                // Current value variable query
                $current_variables = "SELECT * FROM sensor_data ORDER BY data_id DESC LIMIT 1";
                $current_varibles_result = mysqli_query($conn, $current_variables);

                while ($row = mysqli_fetch_array($current_varibles_result)) {
                    $current_soil_moisture = $row['soil_moisture'];
                    $current_temperature = $row['temperature'];
                    $current_humidity = $row['humidity'];
                    $current_light_level = $row['light_level'];
                    $current_water_tank_level = $row['water_tank_level'];
                }

                // High value variable query
                $high_variables = " SELECT 
                                    MAX( `soil_moisture` ) AS `high_soil_moisture`,
                                    MAX( `temperature` ) AS `high_temperature`,
                                    MAX( `humidity` ) AS `high_humidity`,
                                    MAX( `light_level` ) AS `high_light_level`,
                                    MAX( `water_tank_level` ) AS `high_water_tank_level`
                                    FROM sensor_data WHERE timestamp > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY data_id DESC";
                $high_variables_result = mysqli_query($conn, $high_variables);
                
                while ($row = mysqli_fetch_array($high_variables_result)) {
                    $high_soil_moisture = $row['high_soil_moisture'];
                    $high_temperature = $row['high_temperature'];
                    $high_humidity = $row['high_humidity'];
                    $high_light_level = $row['high_light_level'];
                    $high_water_tank_level = $row['high_water_tank_level'];
                }

                // Low value variable query
                $low_variables = "  SELECT 
                                    MIN( `soil_moisture` ) AS `low_soil_moisture`,
                                    MIN( `temperature` ) AS `low_temperature`,
                                    MIN( `humidity` ) AS `low_humidity`,
                                    MIN( `light_level` ) AS `low_light_level`,
                                    MIN( `water_tank_level` ) AS `low_water_tank_level`
                                    FROM sensor_data WHERE timestamp > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY data_id DESC";
                $low_variables_result = mysqli_query($conn, $low_variables);

                while ($row = mysqli_fetch_array($low_variables_result)) {
                    $low_soil_moisture = $row['low_soil_moisture'];
                    $low_temperature = $row['low_temperature'];
                    $low_humidity = $row['low_humidity'];
                    $low_light_level = $row['low_light_level'];
                    $low_water_tank_level = $row['low_water_tank_level'];
                }

                // Average value variable query
                $average_variables = "  SELECT 
                                        AVG( `soil_moisture` ) AS `avg_soil_moisture`,
                                        AVG( `temperature` ) AS `avg_temperature`,
                                        AVG( `humidity` ) AS `avg_humidity`,
                                        AVG( `light_level` ) AS `avg_light_level`,
                                        AVG( `water_tank_level` ) AS `avg_water_tank_level`
                                        FROM sensor_data WHERE timestamp > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY data_id DESC";
                $average_variables_result = mysqli_query($conn, $average_variables);

                while ($row = mysqli_fetch_array($average_variables_result)) {
                    $raw_avg_soil_moisture = $row['avg_soil_moisture'];
                    $raw_avg_temperature = $row['avg_temperature'];
                    $raw_avg_humidity = $row['avg_humidity'];
                    $raw_avg_light_level = $row['avg_light_level'];
                    $raw_avg_water_tank_level = $row['avg_water_tank_level'];
                }

                $average_soil_moisture = round($raw_avg_soil_moisture,1);
                $average_temperature = round($raw_avg_temperature,1);
                $average_humidity = round($raw_avg_humidity,1);
                $average_light_level = round($raw_avg_light_level,1);
                $average_water_tank_level = round($raw_avg_water_tank_level,1);

                // Water usage query
                $water_usage_query = "SELECT SUM(water_dispensed) AS daily_total FROM sensor_data WHERE DATE(`timestamp`) = CURDATE()";
                $water_usage_query_result = mysqli_query($conn, $water_usage_query);

                while ($row = mysqli_fetch_array($water_usage_query_result)) {
                    $daily_water_usage = $row['daily_total'];
                }

                if ($daily_water_usage == 0) {
                    $daily_water_usage = 0;
                }

                //////////////////////////////////////////////////
                // Cat painting response
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, 'https://catfact.ninja/fact');
                $result = curl_exec($ch);
                curl_close($ch);
            
                $obj = json_decode($result);
                $cat_fact = $obj->fact;

                if (is_null($cat_fact)) {
                   $cat_fact = "I can't find my book of facts right now, maybe try again later.";
                }

                $cat_painting_output = "The cat painting has nothing to do with the RPi Smart Garden but cats are my favourite animal. Here's a fact about them... $cat_fact";
                
                //////////////////////////////////////////////////
                // Thermometer (temperature) response
                // testing/debugging
                // $current_temperature = 28.8;

                // empty var check
                if (is_null($current_temperature)) {
                    $thermometer_output = "I seem to have lost communication with my plant friends in the RPi Smart Garden, maybe try again later or go to info and support page for help.";
                
                // current value
                } else {
                    $thermometer_output = "The ambient temperature of the RPi Smart Garden is currently $current_temperature °C, ";
                }
                    // very high
                    if ($current_temperature >= 30) {
                        $thermometer_output .= "this is a very high temperature and will cause some problems with the growth and development of your plants. 
                        Try to reduce the temperature by opening a window or moving the garden to a cooler location.";
                    }
                    // high
                    if ((26 <= $current_temperature) && ($current_temperature <=29)) {
                        $thermometer_output .= "this is a high temperature and may cause some problems with the growth and development of your plants. 
                        Try to reduce the temperature by opening a window.";
                    }
                    // optimal
                    if ((18 <= $current_temperature) && ($current_temperature <=26)) {
                        $thermometer_output .= "this is an ideal and optimal temperature for your plants.";
                    }
                    // low
                    if ((15 <= $current_temperature) && ($current_temperature <=18)) {
                        $thermometer_output .= "this is a low temperature and may cause some problems with the growth and development of your plants. 
                        Try to gradually increase the temperature by turning up your heating or move the RPi Smart Garden to a warmer location.";
                    }
                    // very low
                    if ($current_temperature <= 15) {
                        $thermometer_output .= "this is a very low temperature and will cause some problems with the growth and development of your plants. 
                        Try to gradually increase the temperature by turning up your heating or move the RPi Smart Garden to a warmer location.";
                    }

                //////////////////////////////////////////////////
                // Hydrometer (humidity) response
                // testing/debugging
                // $current_humidity = 0;

                // Empty var check
                if (is_null($current_humidity)) {
                    $hydrometer_output = "I seem to have lost communication with my plant friends in the RPi Smart Garden, maybe try again later or go to info and support page for help.";
                
                // Current value
                } else {
                    $hydrometer_output = "The ambient humidity of the RPi Smart Garden is currently $current_humidity %, ";
                }

                # 60% and over - high
                if ($current_humidity > 70) {
                    $hydrometer_output = "this level is very high.";
                }

                # 30% - 59% - optimal
                if ((30 <= $current_humidity) && ($current_humidity <= 70)) {
                    $hydrometer_output .= "this level is ideal and optimal for your plants.";
                }

                # 29% and under - low
                if ($current_humidity < 60) {
                    $hydrometer_output = "this level is very low.";
                }

                //////////////////////////////////////////////////
                // Sun (light level) response
                // testing/debugging
                // $current_light_level = 0;

                // Empty var check
                if (is_null($current_light_level)) {
                    $sun_output = "I seem to have lost communication with my plant friends in the RPi Smart Garden, maybe try again later or go to info and support page for help.";
                
                // current value
                } else {
                    $sun_output = "The ambient level of light on the RPi Smart Garden is currently $current_light_level Lux, ";
                }

                # sunlight
                if ($current_light_level > 1000) {
                    $sun_output .= "this is great for your plants growth and is likely that it is sunny.";
                }

                # bright
                if ((101 <= $current_light_level) && ($current_light_level <= 1000)) {
                    $sun_output .= "this is great for your plant's growth.";
                }

                # dim
                if ((50 <= $current_light_level) && ($current_light_level <= 101)) {
                    $sun_output .= "if it's during the daytime, you may need to move your RPi Smart Garden to a brighter location.";
                }

                # dark
                if ($current_light_level < 50) {
                    $sun_output .= "if it's during the daytime, you will need to move your RPi Smart Garden to a brighter location.";
                }

                //////////////////////////////////////////////////
                // Water tank (water tank level) response
                // testing/debugging
                // $current_water_tank_level = 0;
                // $daily_water_usage = 0;

                // Empty var check
                if (is_null($current_water_tank_level)) {
                    $water_tank_output = "I seem to have lost communication with my plant friends in the RPi Smart Garden, maybe try again later or go to info and support page for help.";
                
                } elseif ($current_water_tank_level <= 15) {
                    // Water tank empty
                    $water_tank_output = "The water tank is empty, please fill it up to the max fill line as soon as you can.";
                
                // current value
                } else {
                    $water_tank_output = "The water tank level is currently $current_water_tank_level % full, ";
                }
                    
                // Water tank low
                if ((16 <= $current_water_tank_level) && ($current_water_tank_level <= 30)) {
                    $water_tank_output .= "the tank is low and will need filled soon.";
                } else {
                    $water_tank_output .= "I'll tell you when it needs a top up. "; 
                }

                // haven't been watered
                if ($daily_water_usage == 0) {
                    $water_tank_output .= "Your plants haven't required any water today, check back later to see if they have been watered.";
                
                    // have been watered
                } else {
                    $water_tank_output .= "Your plants have been watered today and $daily_water_usage ml of water has been dispensed by the watering system.";
                }
                
                //////////////////////////////////////////////////
                // Smart garden (soil moisture) response
                // testing/debugging
                // $current_soil_moisture = 0;

                // empty var check
                if (is_null($current_soil_moisture)) {
                    $smart_garden_output = "I seem to have lost communication with my plant friends in the RPi Smart Garden, maybe try again later or go to info and support page for help.";
                
                // current value
                } else {
                    $smart_garden_output = "The soil moisture level is currently $current_soil_moisture %, ";
                }
                
                # 90% - 100% very high
                if ((90 <= $current_soil_moisture) && ($current_soil_moisture <= 101)) {
                    $smart_garden_output .= "this level is very high and the plants won't be watered until it decreases natually over time.";
                }

                # 80% - 89% high
                if ((80 <= $current_soil_moisture) && ($current_soil_moisture <= 90)) {
                    $smart_garden_output .= "this level is high and the plants won't be watered until it decreases natually over time.";
                }

                # 70% - 79% slightly high
                if ((70 <= $current_soil_moisture) && ($current_soil_moisture <= 80)) {
                    $smart_garden_output .= "this level is slightly high and the plants won't be watered until it decreases natually over time.";
                }

                # 45% - 69% optimal
                if ((45 <= $current_soil_moisture) && ($current_soil_moisture <= 70)) {
                    $smart_garden_output .= "this level is ideal for your plants to grow. The RPi Smart Garden will monitor this level and add water when necessary to make sure it stays in it's optimal range.";
                }

                # 30% - 44% slightly low
                if ((30 <= $current_soil_moisture) && ($current_soil_moisture <= 45)) {
                    $smart_garden_output .= "this level is slightly low, the RPi Smart Garden will be watering your plants soon.";
                }

                # 10% - 29% low
                if ((10 <= $current_soil_moisture) && ($current_soil_moisture <= 30)) {
                    $smart_garden_output .= "this level is low, the RPi Smart Garden will be watering your plants soon.";
                }

                 # 0% - 9% very low
                if ((0 <= $current_soil_moisture) && ($current_soil_moisture <= 10)) {
                    $smart_garden_output .= "this level is very low, the RPi Smart Garden will be watering your plants soon.";
                }

                //////////////////////////////////////////////////
                // Default response
                $default_output = "Hey! I'm leaf, your virtual plant assistant. Click on something in the room below and I'll give you an update...";
            ?>
            <div class="col py-3">
                <div class="row">
                    <h2>J.A.R.V.I.S. Raspberry Pi Smart Garden</h2>
                </div>
                <div class="row">
                    <h4>Leaf</h4>
                </div>
                <div class="row">
                    <?php
                    $last_updated = "Last Updated: $header_current_timestamp ";

                    if ($time_difference_minutes == 0) {
                        $last_updated .= "(less than a minute ago)";
                    } elseif ($time_difference_minutes == 1) {
                        $last_updated .= "($time_difference_minutes minute ago)";
                    } else {
                        $last_updated .= "($time_difference_minutes minutes ago)";
                    }

                    echo"<p class='text-secondary'>$last_updated</p>";
                    ?>
                </div>
                <?php
                if ($time_difference >= 3600) {
                    echo"   <div class='alert alert-danger' role='alert'>
                            No recent data has been received from the RPi Smart Garden! Go to <a href='/info.php' class='alert-link'>Info and Support</a> for troubleshooting.
                            </div> ";
                }
                ?>
              
                    <div class="row">
                        <div class="col-md-2 col-sm-1">
                            <img id="leaf" src="https://i.ibb.co/m0Mz1LR/leaf-virtual-assistant-v3.png" alt="Leaf Virtual Plant Assistant" class="img-fluid d-block mx-auto" width="60%" height="60%">
                        </div>
                        <div class="col-md-10">
                            <div class="speech bubble">
                                <p id="speechbubble"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 pt-3">
                                <img src="https://i.ibb.co/gwk75h1/RPI-daytime-window-v4.png" usemap="#image-map" alt="rpi-windowsill" class="img-fluid d-block mx-auto">
                                <map name="image-map">
                                    <area alt="Cat Painting" class="cat_painting" href="" title="Cat Painting" coords="4,33,118,126" shape="rect">
                                    <area alt="Thermometer" class="thermometer" href="" title="Thermometer" coords="63,286,102,343" shape="rect">
                                    <area alt="Hydrometer" class="hydrometer" href="" title="Hydrometer" coords="605,133,33" shape="circle">
                                    <area alt="Sun" class="sun" href="" title="Sun" coords="450,93,79" shape="circle">
                                    <area alt="Water Tank" class="water_tank" href="" title="Water Tank" coords="257,374,257,407,290,409,303,397,303,368,271,367" shape="poly">
                                    <area alt="RPi Smart Garden" class="smart_garden" href="" title="RPi Smart Garden" coords="351,393,352,407,466,407,478,393,474,366,369,365" shape="poly">
                                </map>
                            </div>
                        </div>
                    </div>            
            </div>
            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
            <script>
                var cat_painting_output = <?php echo json_encode($cat_painting_output); ?>;
                var thermometer_output = <?php echo json_encode($thermometer_output); ?>;
                var hydrometer_output = <?php echo json_encode($hydrometer_output); ?>;
                var sun_output = <?php echo json_encode($sun_output); ?>;
                var water_tank_output = <?php echo json_encode($water_tank_output); ?>;
                var smart_garden_output = <?php echo json_encode($smart_garden_output); ?>;
                var default_output = <?php echo json_encode($default_output); ?>;

                $(function(e){
                    document.getElementById("speechbubble").textContent = default_output;
                });

                $(".cat_painting").on("click", function(e){
                    e.preventDefault();
                    document.getElementById("speechbubble").textContent = cat_painting_output;
                });

                $(".thermometer").on("click", function(e){
                    e.preventDefault();
                    document.getElementById("speechbubble").textContent = thermometer_output;
                }); 

                $(".hydrometer").on("click", function(e){
                    e.preventDefault();
                    document.getElementById("speechbubble").textContent = hydrometer_output;
                }); 

                $(".sun").on("click", function(e){
                    e.preventDefault();
                    document.getElementById("speechbubble").textContent = sun_output;
                }); 

                $(".water_tank").on("click", function(e){
                    e.preventDefault();
                    document.getElementById("speechbubble").textContent = water_tank_output;
                }); 

                $(".smart_garden").on("click", function(e){
                    e.preventDefault();
                    document.getElementById("speechbubble").textContent = smart_garden_output;
                }); 
                
            </script>
            <script src="jquery.rwdImageMaps.min.js"></script>
            <script>
                $(document).ready(function(e) {
	                $('img[usemap]').rwdImageMaps();
                });
            </script>     
            
        </body>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"
    integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous"
    async></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"
    integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js"
    integrity="sha384-ODmDIVzN+pFdexxHEHFBQH3/9/vQ9uori45z4JjnFsRydbmQbmL5t1tQ0culUzyK"
    crossorigin="anonymous"></script>
</body>

</html>