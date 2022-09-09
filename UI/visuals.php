<?php include "dbconn.php";?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Visualisation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <link rel="icon" href="https://i.ibb.co/mtFCL4R/RPi-Logo-Round.png">
    <style type="text/css">
        body {
            font-family: "Trebuchet MS", Arial;
        }

        @media (min-width: 576px) {
            .h-sm-100 {
            height: 100%;
            }
        }

        #sidebar {
            color: white;
        }

        #sidebar:hover {
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

        .title {
            text-align: center;
        }

        .main-svg {
            background-color: transparent !important;
        }

        .nav-pills > li > a {
            margin-left:12px;
        }

        .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        color: #fff;
        background-color: #198754;
        }

        .nav-pills a {
        color: #198754;
        }

        .nav-pills > li > a:hover {
        color: #20401f;
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
                            <i id="sidebar" class="fs-5 bi-house-door"></i><span id="sidebar" class="ms-1 d-none d-sm-inline">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/visuals.php" class="nav-link px-sm-0 px-2">
                            <i id="sidebar" class="fs-5 bi-bar-chart-line"></i><span id="sidebar" class="ms-1 d-none d-sm-inline">Visuals</span></a>
                    </li>
                    <li>
                        <a href="/leaf.php" class="nav-link px-sm-0 px-2">
                            <i id="sidebar" class="fs-5 bi-chat-dots"></i><span id="sidebar" class="ms-1 d-none d-sm-inline">Leaf</span></a>
                    </li>
                    <li>
                        <a href="/data.php" class="nav-link px-sm-0 px-2">
                            <i id="sidebar" class="fs-5 bi-file-earmark-spreadsheet"></i><span id="sidebar" class="ms-1 d-none d-sm-inline">Data Log</span></a>
                    </li>
                    <li>
                        <a href="/info.php" class="nav-link px-sm-0 px-2">
                            <i id="sidebar" class="fs-5 bi-info-circle"></i><span id="sidebar" class="ms-1 d-none d-sm-inline">Info and Support</span></a>
                    </li>
                    <li>
                        <a href="/settings.php" class="nav-link px-sm-0 px-2">
                            <i id="sidebar" class="fs-5 bi-gear"></i><span id="sidebar" class="ms-1 d-none d-sm-inline">Settings</span></a>
                    </li>
                </ul>
            </div>
        </div>

        <body>
            <?php
            // get last updated timestamp for page header
                $current_timestamp = "SELECT * FROM sensor_data ORDER BY data_id DESC LIMIT 1";
                $current_timestamp_result = mysqli_query($conn, $current_timestamp);

                while ($row = mysqli_fetch_array($current_timestamp_result)) {
                    $current_timestamp = $row['timestamp'];
                }
            
                $datetimestr = strtotime($current_timestamp);
                $formatdatetime = date("d/m/y g:i:s A", $datetimestr);
                $time_difference = (strtotime("now")-strtotime($current_timestamp));
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

                // Graph MYSQL queries
                // 24 hours
                $graph_day_timestamp = array();
                $graph_day_soil_moisture = array();
                $graph_day_temperature = array();
                $graph_day_humidity = array();
                $graph_day_light_level = array();
                $graph_day_water_tank_level = array();
                $graph_day_water_dispensed = array();
                $graph_day_query = "SELECT * FROM sensor_data WHERE timestamp > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY data_id DESC";
                $result = mysqli_query($conn, $graph_day_query);

                while ($row = mysqli_fetch_array($result)) {
                    $graph_day_timestamp[] = $row['timestamp'];
                    $graph_day_soil_moisture[] = $row['soil_moisture'];
                    $graph_day_temperature[] = $row['temperature'];
                    $graph_day_humidity[] = $row['humidity'];
                    $graph_day_light_level[] = $row['light_level'];
                    $graph_day_water_tank_level[] = $row['water_tank_level'];
                    $graph_day_water_dispensed[] = $row['water_dispensed'];
                }

                // Water dispensed day (excluding 0 values)
                $graph_day_water_dispensed_timestamp = array();
                $graph_day_water_dispensed = array();

                $graph_day_water_dispensed_query = "SELECT * FROM sensor_data WHERE timestamp > DATE_SUB(NOW(), INTERVAL 1 DAY) HAVING water_dispensed != 0 ORDER BY data_id DESC";
                $graph_day_water_dispensed_result = mysqli_query($conn, $graph_day_water_dispensed_query);

                while ($row = mysqli_fetch_array($graph_day_water_dispensed_result)) {
                    $graph_day_water_dispensed_timestamp[] = $row['timestamp'];
                    $graph_day_water_dispensed[] = $row['water_dispensed'];
                }

                // 1 week
                $graph_week_timestamp = array();
                $graph_week_soil_moisture = array();
                $graph_week_temperature = array();
                $graph_week_humidity = array();
                $graph_week_light_level = array();
                $graph_week_water_tank_level = array();

                $graph_week_query = "SELECT * FROM sensor_data WHERE timestamp > DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY data_id DESC";
                $result = mysqli_query($conn, $graph_week_query);

                while ($row = mysqli_fetch_array($result)) {
                    $graph_week_timestamp[] = $row['timestamp'];
                    $graph_week_soil_moisture[] = $row['soil_moisture'];
                    $graph_week_temperature[] = $row['temperature'];
                    $graph_week_humidity[] = $row['humidity'];
                    $graph_week_light_level[] = $row['light_level'];
                    $graph_week_water_tank_level[] = $row['water_tank_level'];
                }

                // Water dispensed week (excluding 0 values)
                $graph_week_water_dispensed_timestamp = array();
                $graph_week_water_dispensed = array();

                $graph_week_water_dispensed_query = "SELECT * FROM sensor_data WHERE timestamp > DATE_SUB(NOW(), INTERVAL 1 WEEK) HAVING water_dispensed != 0 ORDER BY data_id DESC";
                $graph_week_water_dispensed_result = mysqli_query($conn, $graph_week_water_dispensed_query);

                while ($row = mysqli_fetch_array($graph_week_water_dispensed_result)) {
                    $graph_week_water_dispensed_timestamp[] = $row['timestamp'];
                    $graph_week_water_dispensed[] = $row['water_dispensed'];
                }

                // 1 month
                $graph_month_timestamp = array();
                $graph_month_soil_moisture = array();
                $graph_month_temperature = array();
                $graph_month_humidity = array();
                $graph_month_light_level = array();
                $graph_month_water_tank_level = array();

                $graph_month_query = "SELECT * FROM sensor_data WHERE timestamp > DATE_SUB(NOW(), INTERVAL 1 MONTH) ORDER BY data_id DESC";
                $result = mysqli_query($conn, $graph_month_query);

                while ($row = mysqli_fetch_array($result)) {
                    $graph_month_timestamp[] = $row['timestamp'];
                    $graph_month_soil_moisture[] = $row['soil_moisture'];
                    $graph_month_temperature[] = $row['temperature'];
                    $graph_month_humidity[] = $row['humidity'];
                    $graph_month_light_level[] = $row['light_level'];
                    $graph_month_water_tank_level[] = $row['water_tank_level'];
                }

                // Water dispensed month (excluding 0 values)
                $graph_month_water_dispensed_timestamp = array();
                $graph_month_water_dispensed = array();

                $graph_month_water_dispensed_query = "SELECT * FROM sensor_data WHERE timestamp > DATE_SUB(NOW(), INTERVAL 1 MONTH) HAVING water_dispensed != 0 ORDER BY data_id DESC";
                $graph_month_water_dispensed_result = mysqli_query($conn, $graph_month_water_dispensed_query);

                while ($row = mysqli_fetch_array($graph_month_water_dispensed_result)) {
                    $graph_month_water_dispensed_timestamp[] = $row['timestamp'];
                    $graph_month_water_dispensed[] = $row['water_dispensed'];
                }

                // All time
                $graph_all_timestamp = array();
                $graph_all_soil_moisture = array();
                $graph_all_temperature = array();
                $graph_all_humidity = array();
                $graph_all_light_level = array();
                $graph_all_water_tank_level = array();

                $graph_all_query = "SELECT * FROM sensor_data";
                $result = mysqli_query($conn, $graph_all_query);

                while ($row = mysqli_fetch_array($result)) {
                    $graph_all_timestamp[] = $row['timestamp'];
                    $graph_all_soil_moisture[] = $row['soil_moisture'];
                    $graph_all_temperature[] = $row['temperature'];
                    $graph_all_humidity[] = $row['humidity'];
                    $graph_all_light_level[] = $row['light_level'];
                    $graph_all_water_tank_level[] = $row['water_tank_level'];
                }

                // Water dispensed all time (excluding 0 values)
                $graph_all_water_dispensed_timestamp = array();
                $graph_all_water_dispensed = array();

                $graph_all_water_dispensed_query = "SELECT * FROM sensor_data HAVING water_dispensed != 0";
                $graph_all_water_dispensed_result = mysqli_query($conn, $graph_all_water_dispensed_query);

                while ($row = mysqli_fetch_array($graph_all_water_dispensed_result)) {
                    $graph_all_water_dispensed_timestamp[] = $row['timestamp'];
                    $graph_all_water_dispensed[] = $row['water_dispensed'];
                }
            ?>

            <script>
                // Conversion from php to js array
                var js_day_timestamp = <?php echo json_encode($graph_day_timestamp); ?>;
                var js_day_soil_moisture = <?php echo json_encode(array_map('floatval', $graph_day_soil_moisture)); ?>;
                var js_day_temperature = <?php echo json_encode(array_map('floatval', $graph_day_temperature)); ?>;
                var js_day_humidity = <?php echo json_encode(array_map('floatval', $graph_day_humidity)); ?>;
                var js_day_light_level = <?php echo json_encode(array_map('floatval', $graph_day_light_level)); ?>;
                var js_day_water_tank_level = <?php echo json_encode(array_map('floatval', $graph_day_water_tank_level)); ?>;
                var js_day_water_dispensed = <?php echo json_encode(array_map('floatval', $graph_day_water_dispensed)); ?>;
                var js_day_water_dispensed_timestamp = <?php echo json_encode($graph_day_water_dispensed_timestamp); ?>;

                var js_week_timestamp = <?php echo json_encode($graph_week_timestamp); ?>;
                var js_week_soil_moisture = <?php echo json_encode(array_map('floatval', $graph_week_soil_moisture)); ?>;
                var js_week_temperature = <?php echo json_encode(array_map('floatval', $graph_week_temperature)); ?>;
                var js_week_humidity = <?php echo json_encode(array_map('floatval', $graph_week_humidity)); ?>;
                var js_week_light_level = <?php echo json_encode(array_map('floatval', $graph_week_light_level)); ?>;
                var js_week_water_tank_level = <?php echo json_encode(array_map('floatval', $graph_week_water_tank_level)); ?>;
                var js_week_water_dispensed = <?php echo json_encode(array_map('floatval', $graph_week_water_dispensed)); ?>;
                var js_week_water_dispensed_timestamp = <?php echo json_encode($graph_week_water_dispensed_timestamp); ?>;

                var js_month_timestamp = <?php echo json_encode($graph_month_timestamp); ?>;
                var js_month_soil_moisture = <?php echo json_encode(array_map('floatval', $graph_month_soil_moisture)); ?>;
                var js_month_temperature = <?php echo json_encode(array_map('floatval', $graph_month_temperature)); ?>;
                var js_month_humidity = <?php echo json_encode(array_map('floatval', $graph_month_humidity)); ?>;
                var js_month_light_level = <?php echo json_encode(array_map('floatval', $graph_month_light_level)); ?>;
                var js_month_water_tank_level = <?php echo json_encode(array_map('floatval', $graph_month_water_tank_level)); ?>;
                var js_month_water_dispensed = <?php echo json_encode(array_map('floatval', $graph_month_water_dispensed)); ?>;
                var js_month_water_dispensed_timestamp = <?php echo json_encode($graph_month_water_dispensed_timestamp); ?>;

                var js_all_timestamp = <?php echo json_encode($graph_all_timestamp); ?>;
                var js_all_soil_moisture = <?php echo json_encode(array_map('floatval', $graph_all_soil_moisture)); ?>;
                var js_all_temperature = <?php echo json_encode(array_map('floatval', $graph_all_temperature)); ?>;
                var js_all_humidity = <?php echo json_encode(array_map('floatval', $graph_all_humidity)); ?>;
                var js_all_light_level = <?php echo json_encode(array_map('floatval', $graph_all_light_level)); ?>;
                var js_all_water_tank_level = <?php echo json_encode(array_map('floatval', $graph_all_water_tank_level)); ?>;
                var js_all_water_dispensed = <?php echo json_encode(array_map('floatval', $graph_all_water_dispensed)); ?>;
                var js_all_water_dispensed_timestamp = <?php echo json_encode($graph_all_water_dispensed_timestamp); ?>;
            </script>

            <div class="col py-3">
                <div class="row">
                    <h2>J.A.R.V.I.S. Raspberry Pi Smart Garden</h2>
                </div>
                <div class="row">
                    <h4>Data Visualisation</h4>
                </div>
                <div class="row">
                    <?php
                    $last_updated = "Last Updated: $formatdatetime ";

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
                    <h5>Go to...</h5>
                </div>
                <div class="row">
                    <div class="d-grid gap-2 d-md-block">
                    <a href="#soil_moisture_section" class="btn btn-outline-success" role="button">
                        <span><i class="fs-5 bi-droplet" aria-hidden="true"></i></span> Soil Moisture</a>
                    <a href="#temperature_section" class="btn btn-outline-success" role="button">
                        <span><i class="fs-5 bi-thermometer-half" aria-hidden="true"></i></span> Temperature </a>
                    <a href="#humidity_section" class="btn btn-outline-success" role="button">
                        <span><i class="fs-5 bi-water" aria-hidden="true"></i></span> Humidity</a>
                    <a href="#light_level_section" class="btn btn-outline-success" role="button">
                        <span><i class="fs-5 bi-brightness-high" aria-hidden="true"></i></span> Light Level</a>
                    <a href="#water_tank_level_section" class="btn btn-outline-success" role="button">
                        <span><i class="fs-5 bi-moisture" aria-hidden="true"></i></span> Water Tank Level</a>
                    <a href="#water_usage_section" class="btn btn-outline-success" role="button">
                        <span><i class="fs-5 bi-clock-history" aria-hidden="true"></i></span> Water Usage</a>
                    </div>
                </div>
                <div id="soil_moisture_section">
                    <div class="row" data-masonry='{"percentPosition": true }'>
                        <div class="col-sm-12 col-md-12 py-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3 col-md-3 py-3 pt-0 align-self-center">
                                            <i class="fs-3 bi-droplet" style="display:inline;"></i>
                                            <h2 style="display:inline;">   Soil Moisture</h2>
                                        </div>                                       
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-droplet"></i>
                                            <h5>Current</h5>
                                            <?php echo"<h5 style='display:inline;'>$current_soil_moisture</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-arrow-up"></i>
                                            <h5>High</h5>
                                            <?php echo"<h5 style='display:inline;'>$high_soil_moisture</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-arrow-down"></i>
                                            <h5>Low</h5>
                                            <?php echo"<h5 style='display:inline;'>$low_soil_moisture</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-bar-chart-steps"></i>
                                            <h5>Average</h5>
                                            <?php echo"<h5 style='display:inline;'>$average_soil_moisture</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                    </div>
                                    <div class="row py-3">
                                        <ul class="nav nav-pills">
        		                            <li class="nav-item">
        			                            <a href="#soil-day" class="nav-link active" role="tab" data-toggle="tab" data-bs-toggle="tab">Day</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#soil-week" class="nav-link btn-success" role="tab" data-toggle="tab" data-bs-toggle="tab">Week</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#soil-month" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">Month</a>
                                            </li>
                                            <li class="nav-item">
        			                            <a href="#soil-all" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">All</a>
        		                            </li>
        	                            </ul>
                                        <script>
                                            // js function to load Plotly graph when tab is active only and not during initial page load
                                            // The first tab (day) is loaded in tab-content as normal
                                            // This is being used due to a responsive layout bug with the js Plotly library
                                            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                                                var target = $(e.target).attr("href") // active tab
                                                if(target=="#soil-week") {                              
                                                        var trace1 = {
                                                            x: js_week_timestamp,
                                                            y: js_week_soil_moisture,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Soil Moisture (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-soil-week', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-soil-week', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };         
                                                }

                                                if(target=="#soil-month") {                                           
                                                        var trace1 = {
                                                            x: js_month_timestamp,
                                                            y: js_month_soil_moisture,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Soil Moisture (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-soil-month', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-soil-month', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };           
                                                }

                                                if(target=="#soil-all") {
                                                                                          
                                                        var trace1 = {
                                                            x: js_all_timestamp,
                                                            y: js_all_soil_moisture,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Soil Moisture (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-soil-all', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-soil-all', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };          
                                                }
                                                });              
                                        </script>

        	                            <div class="tab-content">
        		                            <div role="tabpanel" class="tab-pane show active" id="soil-day">  
                                                <div id="graph-soil-day">
                                                    <script>                                      
                                                        var trace1 = {
                                                            x: js_day_timestamp,
                                                            y: js_day_soil_moisture,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Soil Moisture (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}

                                                        Plotly.newPlot('graph-soil-day', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-soil-day', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };
                                                    </script>
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="soil-week">
                                                <div id="graph-soil-week">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="soil-month">
                                                <div id="graph-soil-month">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="soil-all">
                                                <div id="graph-soil-all">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        	                            </div>                 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="temperature_section">
                    <div class="row" data-masonry='{"percentPosition": true }'>
                        <div class="col-sm-12 col-md-12 py-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3 col-md-3 py-3 pt-0 align-self-center">
                                            <i class="fs-3 bi-thermometer-half" style="display:inline;"></i>
                                            <h2 style="display:inline;">   Temperature</h2>
                                        </div>                                       
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-thermometer-half"></i>
                                            <h5>Current</h5>
                                            <?php echo"<h5 style='display:inline;'>$current_temperature</h5>" ?>
                                            <h5 style="display:inline;">°C</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-arrow-up"></i>
                                            <h5>High</h5>
                                            <?php echo"<h5 style='display:inline;'>$high_temperature</h5>" ?>
                                            <h5 style="display:inline;">°C</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-arrow-down"></i>
                                            <h5>Low</h5>
                                            <?php echo"<h5 style='display:inline;'>$low_temperature</h5>" ?>
                                            <h5 style="display:inline;">°C</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-bar-chart-steps"></i>
                                            <h5>Average</h5>
                                            <?php echo"<h5 style='display:inline;'>$average_temperature</h5>" ?>
                                            <h5 style="display:inline;">°C</h5>                      
                                        </div>
                                    </div>
                                    <div class="row py-3">
                                        <ul class="nav nav-pills">
        		                            <li class="nav-item">
        			                            <a href="#temp-day" class="nav-link active" role="tab" data-toggle="tab" data-bs-toggle="tab">Day</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#temp-week" class="nav-link btn-success" role="tab" data-toggle="tab" data-bs-toggle="tab">Week</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#temp-month" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">Month</a>
                                            </li>
                                            <li class="nav-item">
        			                            <a href="#temp-all" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">All</a>
        		                            </li>
        	                            </ul>
                                        <script>
                                            // js function to load Plotly graph when tab is active only and not during initial page load
                                            // The first tab (day) is loaded in tab-content as normal
                                            // This is being used due to a responsive layout bug with the js Plotly library
                                            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                                                var target = $(e.target).attr("href") // active tab
                                                if(target=="#temp-week") {                              
                                                        var trace1 = {
                                                            x: js_week_timestamp,
                                                            y: js_week_temperature,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Temperature (°C)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-temp-week', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-temp-week', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };         
                                                }

                                                if(target=="#temp-month") {                                           
                                                        var trace1 = {
                                                            x: js_month_timestamp,
                                                            y: js_month_temperature,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Temperature (°C)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-temp-month', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-temp-month', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };           
                                                }

                                                if(target=="#temp-all") {
                                                                                          
                                                        var trace1 = {
                                                            x: js_all_timestamp,
                                                            y: js_all_temperature,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Temperature (°C)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-temp-all', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-temp-all', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };          
                                                }
                                                });              
                                        </script>

        	                            <div class="tab-content">
        		                            <div role="tabpanel" class="tab-pane show active" id="temp-day">  
                                                <div id="graph-temp-day">
                                                    <script>                                      
                                                        var trace1 = {
                                                            x: js_day_timestamp,
                                                            y: js_day_temperature,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Temperature (°C)'
                                                            }
                                                        };

                                                        var config = {responsive: true}

                                                        Plotly.newPlot('graph-temp-day', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-temp-day', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };
                                                    </script>
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="temp-week">
                                                <div id="graph-temp-week">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="temp-month">
                                                <div id="graph-temp-month">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="temp-all">
                                                <div id="graph-temp-all">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        	                            </div>
                                    </div>                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="humidity_section">
                    <div class="row" data-masonry='{"percentPosition": true }'>
                        <div class="col-sm-12 col-md-12 py-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3 col-md-3 py-3 pt-0 align-self-center">
                                            <i class="fs-3 bi-water" style="display:inline;"></i>
                                            <h2 style="display:inline;">   Humidity</h2>
                                        </div>                                       
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-water"></i>
                                            <h5>Current</h5>
                                            <?php echo"<h5 style='display:inline;'>$current_humidity</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-arrow-up"></i>
                                            <h5>High</h5>
                                            <?php echo"<h5 style='display:inline;'>$high_humidity</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-arrow-down"></i>
                                            <h5>Low</h5>
                                            <?php echo"<h5 style='display:inline;'>$low_humidity</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-bar-chart-steps"></i>
                                            <h5>Average</h5>
                                            <?php echo"<h5 style='display:inline;'>$average_humidity</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                    </div>
                                    <div class="row py-3">
                                        <ul class="nav nav-pills">
        		                            <li class="nav-item">
        			                            <a href="#hum-day" class="nav-link active" role="tab" data-toggle="tab" data-bs-toggle="tab">Day</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#hum-week" class="nav-link btn-success" role="tab" data-toggle="tab" data-bs-toggle="tab">Week</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#hum-month" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">Month</a>
                                            </li>
                                            <li class="nav-item">
        			                            <a href="#hum-all" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">All</a>
        		                            </li>
        	                            </ul>
                                        <script>
                                            // js function to load Plotly graph when tab is active only and not during initial page load
                                            // The first tab (day) is loaded in tab-content as normal
                                            // This is being used due to a responsive layout bug with the js Plotly library
                                            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                                                var target = $(e.target).attr("href") // active tab
                                                if(target=="#hum-week") {                              
                                                        var trace1 = {
                                                            x: js_week_timestamp,
                                                            y: js_week_humidity,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Humidity (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-hum-week', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-hum-week', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };         
                                                }

                                                if(target=="#hum-month") {                                           
                                                        var trace1 = {
                                                            x: js_month_timestamp,
                                                            y: js_month_humidity,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Humidity (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-hum-month', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-hum-month', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };           
                                                }

                                                if(target=="#hum-all") {
                                                                                          
                                                        var trace1 = {
                                                            x: js_all_timestamp,
                                                            y: js_all_humidity,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Humidity (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-hum-all', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-hum-all', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };          
                                                }
                                                });              
                                        </script>

        	                            <div class="tab-content">
        		                            <div role="tabpanel" class="tab-pane show active" id="hum-day">  
                                                <div id="graph-hum-day">
                                                    <script>                                      
                                                        var trace1 = {
                                                            x: js_day_timestamp,
                                                            y: js_day_humidity,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Humidity (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}

                                                        Plotly.newPlot('graph-hum-day', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-hum-day', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };
                                                    </script>
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="hum-week">
                                                <div id="graph-hum-week">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="hum-month">
                                                <div id="graph-hum-month">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="hum-all">
                                                <div id="graph-hum-all">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        	                            </div>                 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="light_level_section">
                    <div class="row" data-masonry='{"percentPosition": true }'>
                        <div class="col-sm-12 col-md-12 py-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3 col-md-3 py-3 pt-0 align-self-center">
                                            <i class="fs-3 bi-brightness-high" style="display:inline;"></i>
                                            <h2 style="display:inline;">   Light Level</h2>
                                        </div>                                       
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-brightness-high"></i>
                                            <h5>Current</h5>
                                            <?php echo"<h5 style='display:inline;'>$current_light_level</h5>" ?>
                                            <h5 style="display:inline;">Lux</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-arrow-up"></i>
                                            <h5>High</h5>
                                            <?php echo"<h5 style='display:inline;'>$high_light_level</h5>" ?>
                                            <h5 style="display:inline;">Lux</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-arrow-down"></i>
                                            <h5>Low</h5>
                                            <?php echo"<h5 style='display:inline;'>$low_light_level</h5>" ?>
                                            <h5 style="display:inline;">Lux</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-bar-chart-steps"></i>
                                            <h5>Average</h5>
                                            <?php echo"<h5 style='display:inline;'>$average_light_level</h5>" ?>
                                            <h5 style="display:inline;">Lux</h5>                      
                                        </div>
                                    </div>
                                    <div class="row py-3">
                                        <ul class="nav nav-pills">
        		                            <li class="nav-item">
        			                            <a href="#light-day" class="nav-link active" role="tab" data-toggle="tab" data-bs-toggle="tab">Day</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#light-week" class="nav-link btn-success" role="tab" data-toggle="tab" data-bs-toggle="tab">Week</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#light-month" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">Month</a>
                                            </li>
                                            <li class="nav-item">
        			                            <a href="#light-all" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">All</a>
        		                            </li>
        	                            </ul>
                                        <script>
                                            // js function to load Plotly graph when tab is active only and not during initial page load
                                            // The first tab (day) is loaded in tab-content as normal
                                            // This is being used due to a responsive layout bug with the js Plotly library
                                            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                                                var target = $(e.target).attr("href") // active tab
                                                if(target=="#light-week") {                              
                                                        var trace1 = {
                                                            x: js_week_timestamp,
                                                            y: js_week_light_level,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Light Level (Lux)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-light-week', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-light-week', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };         
                                                }

                                                if(target=="#light-month") {                                           
                                                        var trace1 = {
                                                            x: js_month_timestamp,
                                                            y: js_month_light_level,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Light Level (Lux)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-light-month', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-light-month', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };           
                                                }

                                                if(target=="#light-all") {
                                                                                          
                                                        var trace1 = {
                                                            x: js_all_timestamp,
                                                            y: js_all_light_level,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Light Level (Lux)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-light-all', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-light-all', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };          
                                                }
                                                });              
                                        </script>

        	                            <div class="tab-content">
        		                            <div role="tabpanel" class="tab-pane show active" id="light-day">  
                                                <div id="graph-light-day">
                                                    <script>                                      
                                                        var trace1 = {
                                                            x: js_day_timestamp,
                                                            y: js_day_light_level,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Light Level (Lux)'
                                                            }
                                                        };

                                                        var config = {responsive: true}

                                                        Plotly.newPlot('graph-light-day', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-light-day', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };
                                                    </script>
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="light-week">
                                                <div id="graph-light-week">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="light-month">
                                                <div id="graph-light-month">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="light-all">
                                                <div id="graph-light-all">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        	                            </div>                 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="water_tank_level_section">
                    <div class="row" data-masonry='{"percentPosition": true }'>
                        <div class="col-sm-12 col-md-12 py-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3 col-md-3 py-3 pt-0 align-self-center">
                                        <i class="fs-3 bi-moisture" style="display:inline;"></i>
                                            <h2 style="display:inline;">   Water Tank Level</h2>
                                        </div>                                       
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-moisture"></i>
                                            <h5>Current</h5>
                                            <?php echo"<h5 style='display:inline;'>$current_water_tank_level</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-arrow-up"></i>
                                            <h5>High</h5>
                                            <?php echo"<h5 style='display:inline;'>$high_water_tank_level</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-arrow-down"></i>
                                            <h5>Low</h5>
                                            <?php echo"<h5 style='display:inline;'>$low_water_tank_level</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                        <div class="col-sm-3 col-md-2 py-3 pt-0 text-center">
                                            <i class="fs-5 bi-bar-chart-steps"></i>
                                            <h5>Average</h5>
                                            <?php echo"<h5 style='display:inline;'>$average_water_tank_level</h5>" ?>
                                            <h5 style="display:inline;">%</h5>                      
                                        </div>
                                    </div>
                                    <div class="row py-3">
                                        <ul class="nav nav-pills">
        		                            <li class="nav-item">
        			                            <a href="#tank-day" class="nav-link active" role="tab" data-toggle="tab" data-bs-toggle="tab">Day</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#tank-week" class="nav-link btn-success" role="tab" data-toggle="tab" data-bs-toggle="tab">Week</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#tank-month" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">Month</a>
                                            </li>
                                            <li class="nav-item">
        			                            <a href="#tank-all" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">All</a>
        		                            </li>
        	                            </ul>
                                        <script>
                                            // js function to load Plotly graph when tab is active only and not during initial page load
                                            // The first tab (day) is loaded in tab-content as normal
                                            // This is being used due to a responsive layout bug with the js Plotly library
                                            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                                                var target = $(e.target).attr("href") // active tab
                                                if(target=="#tank-week") {                              
                                                        var trace1 = {
                                                            x: js_week_timestamp,
                                                            y: js_week_water_tank_level,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Water Tank Level (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-tank-week', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-tank-week', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };         
                                                }

                                                if(target=="#tank-month") {                                           
                                                        var trace1 = {
                                                            x: js_month_timestamp,
                                                            y: js_month_water_tank_level,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Water Tank Level (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-tank-month', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-tank-month', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };           
                                                }

                                                if(target=="#tank-all") {
                                                                                          
                                                        var trace1 = {
                                                            x: js_all_timestamp,
                                                            y: js_all_water_tank_level,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Water Tank Level (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}
                                                        
                                                        Plotly.newPlot('graph-tank-all', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-tank-all', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };          
                                                }
                                                });              
                                        </script>

        	                            <div class="tab-content">
        		                            <div role="tabpanel" class="tab-pane show active" id="tank-day">  
                                                <div id="graph-tank-day">
                                                    <script>                                      
                                                        var trace1 = {
                                                            x: js_day_timestamp,
                                                            y: js_day_water_tank_level,
                                                            mode: 'lines',
                                                            type: 'scatter',
                                                            name: 'Scatter and Lines',
                                                            line: {
                                                                color: 'rgb(25, 135, 84)',
                                                                width: 2
                                                            }
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp'
                                                            },
                                                            yaxis: {
                                                                title: 'Water Tank Level (%)'
                                                            }
                                                        };

                                                        var config = {responsive: true}

                                                        Plotly.newPlot('graph-tank-day', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-tank-day', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };
                                                    </script>
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="tank-week">
                                                <div id="graph-tank-week">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="tank-month">
                                                <div id="graph-tank-month">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="tank-all">
                                                <div id="graph-tank-all">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        	                            </div>                 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="water_usage_section">
                    <div class="row" data-masonry='{"percentPosition": true }'>
                        <div class="col-sm-12 col-md-12 py-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3 col-md-3 py-3 pt-0 align-self-center">
                                        <i class="fs-3 bi-clock-history" style="display:inline;"></i>
                                            <h2 style="display:inline;">   Water Usage</h2>
                                        </div>                                       
                                    </div>
                                    <div class="row py-3">
                                        <ul class="nav nav-pills">
        		                            <li class="nav-item">
        			                            <a href="#water-day" class="nav-link active" role="tab" data-toggle="tab" data-bs-toggle="tab">Day</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#water-week" class="nav-link btn-success" role="tab" data-toggle="tab" data-bs-toggle="tab">Week</a>
        		                            </li>
        		                            <li class="nav-item">
        			                            <a href="#water-month" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">Month</a>
                                            </li>
                                            <li class="nav-item">
        			                            <a href="#water-all" class="nav-link" role="tab" data-toggle="tab" data-bs-toggle="tab">All</a>
        		                            </li>
        	                            </ul>
                                        <script>
                                            // js function to load Plotly graph when tab is active only and not during initial page load
                                            // The first tab (day) is loaded in tab-content as normal
                                            // This is being used due to a responsive layout bug with the js Plotly library
                                            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                                                var target = $(e.target).attr("href") // active tab
                                                if(target=="#water-week") { 
                                                    
                                                    var trace1 = {
                                                        x: js_week_water_dispensed_timestamp,
                                                        y: js_week_water_dispensed,
                                                        type: 'bar'
                                                        };

                                                    var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp (only shows dates/times water was dispensed)'
                                                            },
                                                            yaxis: {
                                                                title: 'Water Dispensed (ml)'
                                                            }
                                                        };

                                                        var config = {responsive: true}

                                                        Plotly.newPlot('graph-water-week', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-water-week', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };
                                                        
                                                }

                                                if(target=="#water-month") {   

                                                    var trace1 = {
                                                        x: js_month_water_dispensed_timestamp,
                                                        y: js_month_water_dispensed,
                                                        type: 'bar'
                                                        };

                                                    var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp (only shows dates/times water was dispensed)'
                                                            },
                                                            yaxis: {
                                                                title: 'Water Dispensed (ml)'
                                                            }
                                                        };

                                                        var config = {responsive: true}

                                                        Plotly.newPlot('graph-water-month', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-water-month', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };
                                                }

                                                if(target=="#water-all") {

                                                    var trace1 = {
                                                        x: js_all_water_dispensed_timestamp,
                                                        y: js_all_water_dispensed,
                                                        type: 'bar'
                                                        };

                                                    var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp (only shows dates/times water was dispensed)'
                                                            },
                                                            yaxis: {
                                                                title: 'Water Dispensed (ml)'
                                                            }
                                                        };

                                                        var config = {responsive: true}

                                                        Plotly.newPlot('graph-water-all', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-water-all', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };
                                                }
                                            });              
                                        </script>

        	                            <div class="tab-content">
        		                            <div role="tabpanel" class="tab-pane show active" id="water-day">  
                                                <div id="graph-water-day">
                                                    <?php
                                                    if (!$graph_day_water_dispensed && !$graph_day_water_dispensed_timestamp) {
                                                        echo "<p>No data recorded within this timeframe</p>";

                                                    } else {
                                                        echo "
                                                        <script>
                                                        var trace1 = {
                                                        x: js_day_water_dispensed_timestamp,
                                                        y: js_day_water_dispensed,
                                                        type: 'bar'
                                                        };

                                                        var data = [trace1];
                                                        
                                                        var layout = {
                                                            autosize: true,
                                                            margin: {
                                                                l: 70,
                                                                r: 50,
                                                                b: 50,
                                                                t: 50,
                                                                pad: 4
                                                            },
                                                            xaxis: {
                                                                title: 'Timestamp (only shows dates/times water was dispensed)'
                                                            },
                                                            yaxis: {
                                                                title: 'Water Dispensed (ml)'
                                                            }
                                                        };

                                                        var config = {responsive: true}

                                                        Plotly.newPlot('graph-water-day', data, layout, config);

                                                        window.onresize = function() {
                                                            Plotly.relayout('graph-water-day', {
                                                            'xaxis.autorange': true,
                                                            'yaxis.autorange': true
                                                            });
                                                        };
                                                        </script>
                                                        ";
                                                    }
                                                    ?>
                                                    
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="water-week">
                                                <div id="graph-water-week">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        		                            <div role="tabpanel" class="tab-pane" id="water-month">
                                                <div id="graph-water-month">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="water-all">
                                                <div id="graph-water-all">
                                                    <!-- Graph loaded in js function -->
                                                </div>
                                            </div>
        	                            </div>                 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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