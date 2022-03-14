<?php
require_once 'auth_session.php';
require_once 'config.php';

// define variables and initialize with empty values
$assetTag = $assetName = $qty = $category = "";
// error check variables all initalized with empty vales
$assetTag_err = $assetName_err = $qty_err = $category_err = $elevated_err = "";


// processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // checks to see if elevated_err attibute is empty 
    if (empty($elevated_err)) {

        // validate asset tag
        // Prepare a select statement
        $sql = "SELECT * FROM assets WHERE assetTag = ?";

        if ($stmt = $conn->prepare($sql)) {
            // bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_assetTag);

            // set parameters
            $param_assetTag = trim($_POST["assetTag"]);

            // attempt to execute the prepared statement
            if ($stmt->execute()) {
                // store result
                $stmt->store_result();
                if (empty(trim($_POST['assetTag']))) {
                    $assetTag_err = "Enter a Asset Tag.";
                }
                elseif ($stmt->num_rows == 1) {
                    $assetTag_err = "This assetTag is already taken.";
                } else {
                    $assetTag = trim($_POST["assetTag"]);
                }
            }
        }

        // validate asset name
        if (empty(trim($_POST["assetName"]))) {
            $assetName_err = "enter a name.";
        } elseif (!preg_match('/^[a-zA-Z0-9\s]+$/', trim($_POST["assetName"]))) {
            $assetName_err = "Asset name can only contain characters and numbers";
        } else {
            $assetName = trim($_POST["assetName"]);
        }

        // validate quantity
        if (empty(trim($_POST["qty"]))) {
            $qty_err = "enter a number.";
        } elseif (!preg_match('/^[0-9]+$/', trim($_POST["qty"]))) {
            $qty_err = "only numbers";
        } else {
            $qty = trim($_POST["qty"]);
        }

        $categoryid = $_POST['categoryid'];


        if (empty($assetTag_err) && empty($assetName_err) && empty($qty_err)) {

            // prepare an insert statement
            $sql = "INSERT INTO assets (assetTag, assetName, qty, categoryid, qtyOut, outOfOrder) VALUES (?, ?, ?, ?, 0, 0)";

            if ($stmt = $conn->prepare($sql)) {
                // bind variables to the prepared statement as parameters
                $stmt->bind_param("ssss", $param_assetTag, $param_assetName, $param_qty, $param_categoryid);

                // set parameters
                $param_assetTag = $assetTag;
                $param_assetName = $assetName;
                $param_qty = $qty;
                $param_categoryid = $categoryid;


                // attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // redirect to login page
                    header("location: assets.php");
                } else {
                    ?>
                    <script> console.log("Ooeeeeeeps! Something went wrong. Please try again later.");</script>
                    <?php
                }

                // close statement
                $stmt->close();
            }
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>NNH Inventory</title>
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

    include "config.php";
    // sql stmt for elevated check for current user loggin
    $sql = "SELECT elevated FROM users WHERE username = '" . $_SESSION['username'] . "'";
    $result = $conn->query($sql);

    // checks elevated attribute if false sets elevated_err to permission denied
    while ($row = $result->fetch_array()) {
        if (!$row[0]) {

            $elevated_err = "permission denied to add a user";
        }
    }


    $sql = "SELECT assetTag, assetName, qty, qtyOut, category
        FROM assets 
        LEFT JOIN category on assets.categoryid=category.id ORDER BY assetTag";

    // Checks if query is valid

    if ($result = $conn->query($sql)) {
        ?> <script>console.log('Query Pulled');</script> <?php 
    } else {
        die("Query failed" . $conn->connect_error);
    }

    ?>

    <div class="container">
        <div class="floatright">
            <!-- button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAsset">
                Add Asset
            </button>
        </div>
    </div>


    <!-- modal to add a new Asset -->
    <div class="modal fade" id="addAsset" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">

                        <!-- display error if user is not elevated -->
                        <?php if (!empty($elevated_err)) { ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                </svg>
                                <div>
                                    <?php
                                    echo str_repeat('&nbsp;', 1);
                                    echo 'Requires elevated permissions' ?>
                                </div>
                            </div>
                        <?php
                        } ?>

                        <!-- form action executes php code-->
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <span class="invalid-feedback"><?php echo $elevated_err; ?></span>
                            <div class="row g-2">
                                <div class="col-sm-4">
                                    <label for="validationAssetTag" class="form-label">Asset Tag</label>
                                    <input type="text" id="validationAssetTag" name="assetTag" placeholder="xx58000" class="form-control <?php echo (!empty($assetTag_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $assetTag_err; ?></span>
                                </div>

                                <div class="col-sm-8">
                                    <label for="validationcustom02" class="form-label">Asset Name</label>
                                    <input type="text" id="validationcustom02" name="assetName" class="form-control <?php echo (!empty($assetName_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $assetName_err; ?></span>
                                </div>
                            </div>
                            <div class="row g-2" style="margin-top: 10px;">
                                <div class="col-sm-2">
                                    <label class="form-label">Quantity</label>
                                    <input type="text" name="qty" placeholder="#" class="form-control <?php echo (!empty($qty_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $qty_err; ?></span>
                                </div>

                                <div class="col-sm-10">
                                    <label class="form-label">Category</label>
                                    <select type="text" name="categoryid" class="form-select <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>">
                                        <?php

                                        $sql = $conn->query("SELECT id, category From category");


                                        while ($rows = $sql->fetch_assoc()) {
                                            $category = $rows['category'];
                                            $categoryid = $rows['id'];
                                            echo "<option value='$categoryid'>$category</option>";
                                        }
                                        ?>
                                    </select>
                                    <span class="invalid-feedback"><?php echo $category_err; ?></span>
                                </div>
                            </div>


                            <div class="floatright">
                                <?php if (empty($elevated_err)) { ?>
                                    <input type="submit" class="btn btn-primary" value="Submit" <?php echo (!empty($elevated_err)) ? 'is-invalid' : ''; ?>>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="container">
        <div class="filterinput">
            <div class="form-group">

                <input class="form-control" type="text" id="myInput" onkeyup="filterTable()" placeholder="Search value...">
            </div>
        </div>
        <?php
        //creates table headers

        echo "<table id ='myTable' class='table'>
        <tr><th>Asset Tag</th>
            <th>Asset Name</th>
            <th>Quantity</th>
            <th>Quantity Out</th>
            <th>Category</th></tr>";

        //closes table after qry result = total rows 
        if ($result->num_rows > 0) {

            // creates output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["assetTag"] . "</td> 
                          <td>" . $row["assetName"] . "</td> 
                          <td>" . $row["qty"] . "</td>
                          <td>" . $row["qtyOut"] . "</td>
                          <td>" . $row["category"] . "</td></tr>";
            }
        } else {
            echo "0 records";
        }
        echo "</table>";
        ?>
    </div>

    <div class="container">
        <footer class="d-flex flex-wrap py-3 my-4">
            <ul class="nav col-md-12 justify-content-end list-unstyled d-flex">
                <span class="text-muted">created by - Bayley Sonder</span>
            </ul>
        </footer>
    </div>
    <script>
        var myModal = document.getElementById('myModal')
        var myInput = document.getElementById('myInput')

        myModal.addEventListener('shown.bs.modal', function() {
            myInput.focus()
        })
    </script>
    <script src="filterTable.js"></script>
</body>
</html>