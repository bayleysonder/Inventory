<?php
require_once 'auth_session.php';
require_once 'config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tracking</title>
    <script src="https://kit.fontawesome.com/5ca3b63cae.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">




</head>

<header>
    <div class="container">
        <div class="navBar" style="margin-bottom: 10px">
            <div class="logo"><img src="/imgs/logo.png" alt="Company Logo"></div>

            <div class="topNav">
                <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong> <span>|</span>
                <a href="logout.php"><em class="fas fa-sign-out-alt"></em></a>
            </div>

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
    <div class="container">
        <div class="column left">
            <!-- inserts into sql database -->
            <form action="insertMedical.php" method="post">
                <div class="form-group">
                    <h4>Medical Supply Entry</h4>
                    <label for="assetTag">Asset Tag</label>
                    <input type="text" class="form-control form-control-sm" name="assetTag">
                </div>

                <div class="form-group">
                    <label for="serialNumber">Serial Number</label>
                    <input type="text" class="form-control form-control-sm" name="serialNumber">
                </div>

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control form-control-sm" name="name">
                </div>

                <div class="form-group">
                    <label for="itemDesc">Item Description</label>
                    <textarea type="text" class="form-control form-control-sm" name="itemDesc"></textarea>
                </div>

                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control form-control-sm" name="date">
                </div>
                <div class="floatright">
                    <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                </div>
            </form>
        </div>

        <!-- displays output of query -->
        <div class="column right">

            <!-- sql statment -->
            <?php
            $sql = "SELECT assetTag, serialNum, name, itemDesc, followUp
                        FROM medicalsupplies";
            // checks qry statement if it is valid
            if ($result = $conn->query($sql)) { ?>
                <script>console.log('Query Pulled');</script>
            <?php } else {
                die("Query failed" . $conn->connect_error);
            }

            ?>

            <div class="container">
                <div class="form-group">
                    <input class="form-control" type="text" id="myInput" onkeyup="filterTable()" placeholder="Search value...">
                </div>
                <?php
                // table headers
                echo "<table id ='myTable' class='table'><tr><th>Asset Tag</th>
                                <th>Serial Number</th><th>Name</th><th>Item Description</th><th>Date</th></tr>"; //creates table headers 
                //closes table after qry result = total rows
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["assetTag"] . "</td> 
                                  <td>" . $row["serialNum"] . "</td> 
                                  <td>" . $row["name"] . "</td>
                                  <td>" . $row["itemDesc"] . "</td>
                                  <td>" . $row["followUp"] . "</td></tr>";
                    }
                } else {
                    echo "0 records";
                }
                echo "</table>";
                ?>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="container">
            <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4">
                <ul class="nav col-md-12 justify-content-end list-unstyled d-flex">
                    <span class="text-muted">created by - Bayley Sonder</span>
                </ul>
            </footer>
        </div>
    </div>

</body>

</html>