<?php
require_once 'auth_session.php';
require_once 'config.php';

// define variables and initialize with empty values
$firstname = $lastname = $email = $userid = "";
// error check variables all initalized with empty vales
$firstname_err = $lastname_err = $email_err = $elevated_err = "";

// processing form data when form is submitted and user is elevated
if (isset($_POST['insertEmp']) && empty($elevated_err)) {

    // validate first name
    if (empty(trim($_POST["firstname"]))) {
        $firstname_err = "Please enter a name.";
    } elseif (!preg_match('/^[a-zA-Z]+$/', trim($_POST["firstname"]))) {
        $firstname_err = "First name can only contain letters";
    } else {
        $firstname = trim($_POST["firstname"]);
    }

    // validate last name
    if (empty(trim($_POST["lastname"]))) {
        $lastname_err = "Please enter a last name.";
    } elseif (!preg_match('/^[a-zA-Z]+$/', trim($_POST["firstname"]))) {
        $lastname_err = "Last name can only contain letters";
    } else {
        $lastname = trim($_POST["lastname"]);
    }

    // validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter a email.";
    }

    // Prepare a select statement
    $sql = "SELECT * FROM employees WHERE email = ?";

    if ($stmt = $conn->prepare($sql)) {
        // bind variables to the prepared statement as parameters
        $stmt->bind_param("s", $param_email);

        // set parameters
        $param_email = trim($_POST["email"]);

        // attempt to execute the prepared statement
        if ($stmt->execute()) {
            // store result
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $email_err = "This email is already taken.";
            } else {
                $email = trim($_POST["email"]);
            }
        }
    }
    // checks input errors before inserting in database
    if (empty($firstname_err) && empty($lastname_err) && empty($email_err) && empty($elevated_err)) {

        // prepare an insert statement to insert employee into database
        $sql = "INSERT INTO employees (firstname, lastname, email) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_firstname, $param_lastname, $param_email);

            // set parameters
            $param_firstname = $firstname;
            $param_lastname = $lastname;
            $param_email = $email;


            // attempt to execute the prepared statement
            if ($stmt->execute()) {
                // redirect to login page
                header("location: employees.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // close statement
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employees</title>
    
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
        <!-- Website Navigation Container -->
        <div class="container">
            <div class="navBar" style="margin-bottom: 10px">
                <div class="logo"><img src="/imgs/logo.png" alt="Company Logo"></div>

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
                                <button id="dropDownLabel" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Tracking
                                </button>

                                <ul class="dropdown-menu" aria-labelledby="dropDownLabel">
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
    // check to see if user has paired account yet
    $sql = "SELECT paired FROM users WHERE username = '" . $_SESSION['username'] . "'";
    $result = $conn->query($sql);

    // if paired is 0 redirect to linkemployee.php
    while ($row = $result->fetch_array()) {
        if (!$row[0]) {
            header("location: linkemployee.php");
            $conn->close();
        }
    }

    // check to see if user is elevated
    $sql = "SELECT elevated FROM users WHERE username = '" . $_SESSION['username'] . "'";
    $result = $conn->query($sql);

    // if elevated is 0 $elevated_err stores error message
    while ($row = $result->fetch_array()) {
        if (!$row[0]) {
            $elevated_err = "permission denied to add a user";
        }
    }

    ?>
    <!-- Add Employee button container -->
    <div class="container">
        <div class="floatright">
            <!-- button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployee">
                Add Employee
            </button>
        </div>
    </div>

    <!-- modal to add a new employee -->
    <div class="modal fade" id="addEmployee" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addEmployeeModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModal">New Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <!-- displays a warning message to user if not elevated -->
                        <?php if (!empty($elevated_err)) { ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                </svg>
                                <div>
                                    <?php
                                    // prints a backspace after alert icon
                                    echo str_repeat('&nbsp;', 1);
                                    echo 'Requires elevated permissions' ?>
                                </div>
                            </div>
                        <?php
                        } ?>
                        <!-- form action executes php code start of document -->
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <!-- form input -->
                            <div class="row">
                                <div class="col">
                                    <label class="form-label">First name</label>
                                    <input type="text" name="firstname" placeholder="First Name" class="form-control <?php echo (!empty($firstname_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $firstname_err; ?></span>
                                </div>


                                <!-- form input -->
                                <div class="col">
                                    <label class="form-label">Last name</label>
                                    <input type="text" name="lastname" placeholder="Last Name" class="form-control <?php echo (!empty($lastname_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $lastname_err; ?></span>
                                </div>
                            </div>
                            <!-- form input -->
                            <div class="form-group" style="margin-top: 10px;">
                                <label class="form-label">Email</label>
                                <input type="text" name="email" placeholder="---@---.org" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>">
                                <span class="invalid-feedback"><?php echo $email_err; ?></span>
                            </div>
                            <!-- Hides submit button if user is not elevated -->
                            <div class="floatright">
                                <?php if (empty($elevated_err)) { ?>
                                    <input type="submit" class="btn btn-primary" value="Submit" name="insertEmp" <?php echo (!empty($elevated_err)) ? 'is-invalid' : ''; ?>>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search value filter container -->
    <div class="container">
        <!-- search value input box -->
        <div class="filterinput">
            <input class="form-control" type="text" id="myInput" onkeyup="filterTable()" placeholder="Search value...">
        </div>

        <?php

        // SQL statement for displaying all employees 
        $sql = "SELECT firstName, lastName, email
                FROM employees order by email";

        // Checks if query is valid

        if ($result = $conn->query($sql)) { ?>
            <script>
                console.log('Query Pulled');
            </script>
        <?php } else {
            die("Query failed" . $conn->connect_error);
        }
        //creates table headers

        echo "<table id ='myTable' class='table'>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                </tr>";

        //closes table after qry result = total rows 
        if ($result->num_rows > 0) {

            // creates output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                          <td>" . $row["firstName"] . "</td>
                          <td>" . $row["lastName"] . "</td>
                          <td>" . $row["email"] . "</td>
                      </tr>";
            }
        } else {
            echo "0 records";
        }
        echo "</table>";
        $conn->close();
        ?>

    </div>
    <!-- footer container-->
    <div class="container">
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4">
            <ul class="nav col-md-12 justify-content-end list-unstyled d-flex">
                <span class="text-muted">created by - Bayley Sonder</span>
            </ul>
        </footer>
    </div>

    <!-- script from bootstrap handling modals -->
    <script>
        var myModal = document.getElementById('myModal')
        var myInput = document.getElementById('myInput')

        myModal.addEventListener('shown.bs.modal', function() {
            myInput.focus()
        })
    </script>
    <!-- js that filters myTable based on user input -->
    <script src="filterTable.js"></script>

</body>
</html>