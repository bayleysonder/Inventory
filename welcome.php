<?php
require_once 'auth_session.php';
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <!-- icons lib -->
    <script src="https://kit.fontawesome.com/5ca3b63cae.js" crossorigin="anonymous"></script>
    <!-- bootstrap 5 css lib -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- bootstrap 5 js lib -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <!-- personal style sheet -->
    <link rel="stylesheet" href="style.css">
</head>
<header>
    <div class="container">
        <div class="navBar" style="margin-bottom: 10px">
            <div class="logo"><img src="/imgs/nnh_logo.png" alt="Company Logo"></div>
            <!-- account actions -->
            <div class="topNav">
                <!-- displays current username who has an active session -->
                <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong> <span>|</span>
                <!-- logout icon and code to end session -->
                <a href="logout.php"><em class="fas fa-sign-out-alt"></em></a>
            </div>
            <!-- website navigation -->
            <div class="botNav">
                <ul class="nav justify-content-end">
                    <li class="nav-item">
                        <a class="underline" href="welcome.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="underline" href="employees.php">Employees</a>
                    </li>
                    <li class="nav-item">
                        <a class="underline" href="assets.php">Inventory</a>
                    </li>
                    <div class="dropdown">
                        <div class="dropunderline">
                            <button id="dLabel" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Tracking
                            </button>

                            <ul class="dropdown-menu" aria-labelledby="dLabel">
                                <li><a class="dropdown-item" href="computers.php">Computers</a></li>
                                <li><a class="dropdown-item" href="medicalsupplies.php">Medical Supplies</a></li>
                            </ul>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </div>
</header>

<body>
    <div class="clearfix">
        <div class="container">
            <!-- current laptop chart -->
            <div class="row">
                <div class="col-6">
                    <canvas id="laptopChart"></canvas>
                </div>
                <!-- current assets chart -->
                <div class="col-6">
                    <canvas id="assetsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="container">

    </div>

    <!-- Footer -->
    <div class="clearfix">
        <div class="container">
            <footer class="d-flex flex-wrap py-3 my-4">
                <ul class="nav col-md-12 justify-content-end list-unstyled d-flex">
                    <span class="text-muted">created by - Bayley Sonder</span>
                </ul>
            </footer>
        </div>
    </div>

    <!-- Chart.js lib -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js">
    </script>
    <script src="chartjs-plugin-labels.js"></script>
    <!-- dashboard SQL queries -->
    <script>
        <?php

        /* --LAPTOP QUERIES-- */
        // sql stmt to get all laptops that are in the checkout table with a NULL return date
        $sql = "SELECT count(DISTINCT checkout.id) as total FROM checkout 
            LEFT JOIN assets on checkout.assetid = assets.id 
            WHERE assets.categoryid = 1 AND returnDate IS NULL";
        $result = $conn->query($sql);
        $ltOut = $result->fetch_assoc();

        // sql stmt to get all laptops in assets table with a qtyOut IS 0
        $sql = "SELECT count(DISTINCT id) as total FROM assets
            WHERE categoryid = 1 AND qtyOut = 0";
        $result = $conn->query($sql);
        $ltIn = $result->fetch_assoc();
        ?>

        /* --CURRENT LAPTOP INVENTORY CHART-- */
        // Setup Block
        var data = {
            labels: ["Laptops Assigned", "Laptops Unassigned"],
            datasets: [{
                data: [<?= $ltOut['total'] ?>, <?= $ltIn['total'] ?>],
                backgroundColor: ["#F7464A", "#46BFBD"],
                hoverBackgroundColor: ["#FF5A5E", "#5AD3D1"]
            }],

        };

        // Config Block
        const ltConfig = {
            type: 'pie',
            data,
            options: {
                responsive: true,
                plugin: {
                    labels: {
                        fontColor: '#fff',
                        render: 'value'
                    }

                }
            }

        };

        // Render Chart
        const laptopChart = new Chart(
            document.getElementById('laptopChart'),
            ltConfig
        );

        <?php
        /* --TOTAL ASSET QUERY-- */
        // laptop count
        $sql = "SELECT count(DISTINCT id) AS total FROM assets
        WHERE categoryid = 1";
        $result = $conn->query($sql);
        $ltTotal = $result->fetch_assoc();

        $sql = "SELECT count(DISTINCT id) AS total FROM assets
        WHERE categoryid = 2";
        $result = $conn->query($sql);
        $wsTotal = $result->fetch_assoc();

        $sql = "SELECT count(DISTINCT id) AS total FROM assets
        WHERE categoryid = 3";
        $result = $conn->query($sql);
        $aioTotal = $result->fetch_assoc();
        ?>

        /* --CURRENT ASSET CHART-- */
        // Setup Block
        var data = {
            labels: ["Laptops", "Desktops", "All-in-One"],
            datasets: [{
                data: [<?= $ltTotal['total'] ?>, <?= $wsTotal['total'] ?>, <?= $aioTotal['total'] ?>],
                backgroundColor: ["#9733c7", "#ee603a", "#F1AB4B"],
                hoverBackgroundColor: ["#a957d1", "#f5714d", "#f1ba5f"]
            }]
        };

        // Config Block
        const assetConfig = {
            type: 'pie',
            data,
            options: {
                responsive: true
            }
        };

        // Render Chart
        const assetsChart = new Chart(
            document.getElementById('assetsChart'),
            assetConfig
        );
    </script>



</body>

</html>