<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings</title>
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
                    <h2>J.A.R.V.I.S. Raspberry Pi Smart Garden</h2>
                </div>
                <div class="row">
                    <h4>Settings</h4>
                </div>
                <div class="row">
                <div class="container">
                    <h4>Reboot Raspberry Pi</h4>
                    <p>Clicking this button will remotely reboot the Raspberry Pi</p>
                    <form action="settings.php" method="post">
                    <input type="button" id="reboot" name="reboot" value="Reboot" onClick="reboot()"/>
                    </form>
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['reboot'])) {
                            reboot();
                        }
                    
                    function reboot() {
                        exec("python /var/www/reboot.py");
                        echo "<p>Calling for reboot...</p>";
                    }
                ?>
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