<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Info and Support</title>
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

        #major {
            color: rgb(255,140,0);
        }

        #minor {
            color: rgb(244,202,22);
        }

        .accordion-button {
            background-color: #198754 !important;
        }

        .accordion-button:focus {
            box-shadow: none;
        }

        .accordion-button:not(.collapsed) {
            color: #ffffff;
        }

        .accordion-button {
            color: #ffffff;
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
            <div class="col py-3">
                <div class="row">
                </div>
                <div class="row">
                    <h2>J.A.R.V.I.S. Raspberry Pi Smart Garden</h2>
                </div>
                <div class="row">
                    <h4>Info and Support</h4>
                </div>
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Getting Started
                        </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <p>Hey! Follow these steps to get started using the RPi Smart Garden...<p>
                                <ol>
                                    <li>Fill the planting container with soil and firmly press it down</li>
                                    <li>Sow the plant seeds and space them apart in centimeter intervals</li>
                                    <li>Cover the seeds with a light dusting of soil</li>
                                    <li>Position the RPi Smart Garden in a bright and warm location</li>
                                    <li>Plug in the usb cable into a power adaptor such as a phone charger</li>
                                </ol>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            System Status
                            </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <p>On the Dashboard, the System Status card displays a status indicating the current state of the RPi Smart Garden, these statuses are:
                                    <h5 class="text-success">All good! No known issues.</h5>
                                    <p>The system should be operating as normal</p>
                                    <h5 id="minor">Minor system alert - view below!</h5>
                                    <p>This alert has a low impact, a data parameter may be slightly out of range but will not cause any operational problems.
                                            The system may give the user a recommendation to make a change to RPi Smart Garden or its environment.
                                    </p> 
                                    <h5 id="major">Major system alert - view below!</h5>
                                    <p>This alert has a high impact, a data parameter is very out of range which will cause adverse effects to plant growth and
                                            development. The system will give the user a recommendation to make a change to RPi Smart Garden or its environment.
                                    </p> 
                                    <h5 class="text-danger">Critical system alert - view below!</h5>
                                    <p>This alert has significant impact, a major problem such as no recent data received or empty water tank has been identified.
                                            The system is currently not operating as normal and needs user intervention.
                                    </p> 
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Alert Types
                            </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <h5>Water Tank Level Alert</h5>
                                    <p>Shows when the water tank level is low or empty. When these alerts are displayed, the water tank should be filled as soon as possible
                                        using the steps below:
                                    <ol>
                                        <li>Carefully unclip the water level sensor and move it aside</li>
                                        <li>Insert the funnel into the tank to protect electronics</li>
                                        <li>Fill the tank with cool water until the level reaches the max fill line</li>
                                        <li>Remove the funnel and reclip the water level sensor back onto the top of the tank</li>
                                    </ol>
                                    </p>
                                    <h5>High Temperature Alert</h5>
                                    <p>
                                    Displays when the RPi Smart Garden's ambient temperature is higher than the optimal range.
                                    Action such as opening a window or moving the RPi Smart Garden to a cooler location if possible is recommended to be
                                    taken to prevent adverse affects to the growth and development of the plants. 
                                    </p>
                                    <h5>Low Temperature Alert</h5>
                                    <p>
                                    Displays when the RPi Smart Garden's ambient temperature is lower than the optimal range. 
                                    Action such as turning up the room's heating or moving the RPi Smart Garden to a warmer location is recommended to be
                                    taken to prevent adverse affects to the growth and development of the plants.
                                    </p>
                                    <h5> No Recent Data</h5>
                                    <p>
                                    Displays if no recent data has been received from the sensors of the RPi Smart Garden. User should check the Data Log to see when
                                    the last data entry was made. A solution to fixing this error is rebooting the Raspberry Pi.
                                    </p>

                                </div>
                            </div>
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