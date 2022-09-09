<?php include "dbconn.php";
header("Refresh:60");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
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

        .title {
            text-align: center;
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
            // get last updated timestamp
                $sqli = "SELECT * FROM sensor_data ORDER BY data_id DESC LIMIT 1";
                $result = mysqli_query($conn, $sqli);

                while ($row = mysqli_fetch_array($result)) {
                    $timestamp = $row['timestamp'];
                }

                $datetimestr = strtotime($timestamp);
                $formatdatetime = date("d/m/y g:i:s A", $datetimestr);
                $time_difference = (strtotime("now")-strtotime($timestamp));
                $time_difference_minutes_raw = ($time_difference / 60);
                $time_difference_minutes = round($time_difference_minutes_raw);
                ?>
            <div class="col py-3">
                <div class="row">
                    <h2>J.A.R.V.I.S. Raspberry Pi Smart Garden</h2>
                </div>
                <div class="row">
                    <h4>Sensor Data Log</h4>
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
                <div class='row px-2'>
                    <table class='table table-sm table-hover'>
                        <thead>
                          <tr>
                            <th scope='col'>Timestamp</th>
                            <th scope='col'>Soil Moisture</th>
                            <th scope='col'>Temperature</th>
                            <th scope='col'>Humidity</th>
                            <th scope='col'>Light</th>
                            <th scope='col'>Water Tank Level</th>
                          </tr>
                        </thead>
                        <tbody>
                <?php
                // pagination of table rows
                
                // rows per page (this number can be changed)
                $rows_per_page = 30;

                // find out total number of rows in mysql sensor_data table
                $sql = "SELECT * FROM sensor_data";
                $result = mysqli_query($conn, $sql);
                $number_of_rows = mysqli_num_rows($result);

                // divide number of rows by rows per page - result will be number of pages required
                // using ceil() function to round number up to int
                $number_of_pages = ceil($number_of_rows/$rows_per_page);

                // find out which page the user is currently on
                if (!isset($_GET['page'])) {
                    $page = 1;
                } else {
                    $page = $_GET['page'];
                }

                // find out sql limit number to display correct results per page
                $this_page_first_result = ($page-1)*$rows_per_page;
             
                // retrieve selected results from mysql based on limit number
                // these are echoed out to user

                $sql = "SELECT * FROM sensor_data ORDER BY data_id DESC LIMIT " . $this_page_first_result . ',' . $rows_per_page;
                $result = mysqli_query($conn, $sql);

                while ($row = mysqli_fetch_array($result)) {
                    $raw_timestamp = $row['timestamp'];
                    $soil_moisture = $row['soil_moisture'];
                    $temperature = $row['temperature'];
                    $humidity = $row['humidity'];
                    $light_level = $row['light_level'];
                    $water_tank_level = $row['water_tank_level'];

                    $table_datetimestr = strtotime($raw_timestamp);
                    $table_formatdatetime = date("d/m/y g:i:s A", $table_datetimestr);
                    
                    echo " 
                          <tr>
                            <th scope='row'>$table_formatdatetime</th>
                            <td>$soil_moisture %</td>
                            <td>$temperature °C</td>
                            <td>$humidity %</td>
                            <td>$light_level Lux</td>
                            <td>$water_tank_level %</td>
                          </tr>";
                }                
                ?>
                            </tbody>
                      </table>
                </div>
                <div class="row">
                    <nav aria-label="Data log pagination">
                        <ul class="pagination pagination-sm">
                        <?php
                        
                        // Display dynamic pagination
                        $visible_page_links = "";
                        $current_page = $_GET['page'];
                        $total_pages = $number_of_pages;
                        $visible_link_limit = 10;

                        if ($total_pages >=1 && $current_page <= $total_pages) {
                            $counter = 1;
                            $visible_page_links = "";

                            //if ($current_page > ($visible_link_limit/2)) {
                               // $visible_page_links .= "<a href=\"?page=1\">1 </a> ... ";
                            //}
                            if ($current_page != 1) {
                                //$visible_page_links .= "<a href=\"?page=1\">1 </a> ... ";
                                $visible_page_links .= "<li class='page-item'><a class='page-link text-success' href='data.php?page=1'>1</a></li>";
                                $visible_page_links .= "<li class='page-item'><a> ... </a></li>";
                            }

                            for ($x = $current_page; $x <= $total_pages; $x++) {
                                if ($counter < $visible_link_limit) {
                                    //$visible_page_links .=  "<a href=\"?page=" .$x."\">".$x." </a>";
                                    $visible_page_links .= "<li class='page-item'><a class='page-link text-success' href='data.php?page=$x'>$x</a></li>";
                                    $counter++;
                                }
                            }
                                
                                if ($current_page < $total_pages - ($visible_link_limit/2)) {
                                    //$visible_page_links .= " ... " . "<a href=\"?page=" .$total_pages."\">".$total_pages." </a>";
                                    $visible_page_links .= "<li class='page-item'><a> ... </a></li>";
                                    $visible_page_links .= "<li class='page-item'><a class='page-link text-success' href='data.php?page=$total_pages'>$total_pages</a></li>";
                                }
                        }
                        echo $visible_page_links;

                        // Display all pagination links (for testing only)
                        //for ($page = 1; $page <= $number_of_pages; $page++) {
                            //echo '<a href = "data.php?page=' . $page  . '">' . $page . '</a>';
                            //echo"<li class='page-item'><a class='page-link' href='data.php?page=$page'>$page</a></li>";
                        //}
                        ?>
                        </ul>
                    </nav>
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