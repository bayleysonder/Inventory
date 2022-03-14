<?php
require_once 'auth_session.php';
require_once 'config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <title>NNH Tracking</title>
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
    <div class="clearfix">
        <div class="container">
            <div class="navBar" style="margin-bottom: 10px">
                <div class="logo"><img src="/imgs/nnh_logo.png" alt="Company Logo"></div>

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
    </div>
</header>

<body>
    <?php
    // sql stmt for elevated check for current user loggin
    $sql = "SELECT elevated FROM users WHERE username = '" . $_SESSION['username'] . "'";
    $result = $conn->query($sql);

    // checks elevated attribute if false sets elevated_err to permission denied
    while ($row = $result->fetch_array()) {
        if (!$row[0]) {
            $elevated_err = "permission denied to add a user";
        }
    }
    ?>

    <div class="container">
        <div class="floatright">
            <!-- button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployee">
                Check-out item
            </button>
        </div>
    </div>

    <!-- modal content -->
    <div class="modal fade" id="addEmployee" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Computer Check-out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <!-- error code if user is not elevated -->
                        <?php if (!empty($elevated_err)) { ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                </svg>
                                <div>
                                    <?php
                                    // prints out a backspace
                                    echo str_repeat('&nbsp;', 1);
                                    echo 'Requires elevated permissions' ?>
                                </div>
                            </div>
                        <?php
                        } ?>

                        <form action="insertComputer.php" method="post">
                            <input type="hidden" name="elevated_err" value="$elevated_err">
                            <div class="form-group">
                                <label for="assetid">Asset Tag</label>
                                <select name="assetid" class="form-select form-select-sm">
                                    <?php

                                    //stores Asset Tags where qty is =! 0 in a qry
                                    $tag = $conn->query("SELECT id, assetTag 
                                                         From assets 
                                                         WHERE qtyOut = 0 AND outOfOrder = 0 ORDER BY assetTag");

                                    //display asset tags where qty != 0
                                    while ($rows = $tag->fetch_assoc()) {
                                        $assetTag = $rows['assetTag'];
                                        $assetid = $rows['id'];
                                        echo "<option value='$assetid'>$assetTag</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="empid">Employee</label>
                                <select name="empid" class="form-select form-select-sm">
                                    <?php

                                    //stores distinct employee emails in a qry
                                    $resultSet = $conn->query("SELECT DISTINCT id, Email 
                                                               FROM `employees` 
                                                               ORDER BY Email ");

                                    //displays emails						    
                                    while ($rows = $resultSet->fetch_assoc()) {
                                        $empEmail = $rows['Email'];
                                        $empid = $rows['id'];
                                        echo "<option value='$empid'>$empEmail</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="clearfix">
                                <div class="row g-2">
                                    <div class="col-sm-4">
                                        <label for="qtyOut">Quantity</label>
                                        <input type="text" class="form-control form-control-sm" name="qtyOut" placeholder="#">
                                    </div>

                                    <div class="col-sm-8">
                                        <label for="dateOut">Date</label>
                                        <input type="date" class="form-control form-control-sm" name="dateOut">
                                    </div>
                                </div>
                            </div>
                            <div class="floatright">
                                <?php if (empty($elevated_err)) { ?>
                                    <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    include 'config.php';
    // sql statement selects all rows in checkout with a null returnDate
    $sql = "SELECT employees.email, assets.assetTag, assets.qtyOut, dateOut, checkout.id, returnDate
                        FROM checkout 
                        LEFT JOIN employees on employees.id = checkout.empid 
                        LEFT JOIN assets on assets.id = checkout.assetid
                        WHERE returnDate IS NULL ORDER BY assets.assetTag";

    // checks if query is valid
    if ($result = $conn->query($sql)) {
    ?> <script>
            console.log('Query Pulled');
        </script>
    <?php } else {
        die("Query failed" . $conn->connect_error);
    }

    ?>
    <div class="container">
        <div class="filterinput">
            <input class="form-control" type="text" id="myInput" onkeyup="filterTable()" placeholder="Search value...">
        </div>
    </div>
    <div class="container">
        <?php
        // creates table headers
        if (empty($elevated_err)) {
            echo "<table id ='myTable' class='table'>
                    <tr>
                        <th>Employee</th>
                        <th>Asset Tag</th>
                        <th>Quantity Out</th>
                        <th>Date Out</th>
                        <th>Edit</th>
                    </tr>";
        } else {
            echo "<table id ='myTable' class='table'>
                    <tr>
                        <th>Employee</th>
                        <th>Asset Tag</th>
                        <th>Quantity Out</th>
                        <th>Date Out</th>
                    </tr>";
        }

        //closes table after qry result = total rows
        if ($result->num_rows > 0) {
            // creates output data of each row

            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row["email"]; ?></td>
                    <td><?php echo $row["assetTag"]; ?></td>
                    <td><?php echo $row["qtyOut"]; ?></td>
                    <td><?php echo $row["dateOut"]; ?></td>
                    <?php if (empty($elevated_err)) { ?>
                        <!-- passes both assetTag and row id with anchor tag -->
                        <td><a href="return.php?id=<?php echo $row["id"] ?>&&assetTag=<?php echo $row["assetTag"]; ?>&&elevated_err=<?php $elevated_err; ?>">Return</a></td>
                    <?php } ?>
                </tr>

        <?php
            }
        } ?>
        </table>
    </div>
    <div class="container">
        <footer class="d-flex flex-wrap py-3 my-4">
            <ul class="nav col-md-12 justify-content-end list-unstyled d-flex">
                <span class="text-muted">created by - Bayley Sonder</span>
            </ul>
        </footer>
    </div>
    <script src="filterTable.js"></script>
</body>
</html>