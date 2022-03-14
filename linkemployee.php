<?php
// check if the user is logged in, if not then redirect him to login page
require_once 'auth_session.php';
// include config file
require_once "config.php";

// define variables and initialize with empty values
$firstname = $lastname = $email = $userid = "";
// define error varaibles with empty values
$firstname_err = $lastname_err = $email_err = "";

// processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

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
        $firstname_err = "Last name can only contain letters";
    } else {
        $lastname = trim($_POST["lastname"]);
    }

    // validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter a email.";
    }

    // prepare a select statement
    $sql = "SELECT * FROM employees WHERE email = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("s", $param_email);

        // Set parameters
        $param_email = trim($_POST["email"]);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // store result
            $stmt->store_result();

            // link existing employee to employee user account
            if ($stmt->num_rows == 1) {

                $sql = "SELECT id FROM users where username = '" . $_SESSION['username'] . "'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_array()) {
                    $userid = $row[0];
                }
                
                // update employees with userid to link employee account to user account
                $sql = "UPDATE employees SET userid = $userid WHERE email = '$param_email'";
                $conn->query($sql);

                // update users to show that they paired to an employee account
                $sql = "UPDATE users SET paired = 1 WHERE id = $userid";
                $conn->query($sql);

                // Redirect to employee page
                header("location: employees.php");

                $email_err = "This email is already taken.";
            } else {
                $email = trim($_POST["email"]);
            }

            // Check input errors before inserting in database
            if (empty($firstname_err) && empty($lastname_err) && empty($email_err)) {

                $sql = "SELECT id FROM users where username = '" . $_SESSION['username'] . "'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_array()) {
                    $userid = $row[0];
                }

                // Prepare an insert statement
                $sql = "INSERT INTO employees (firstname, lastname, email, userid) VALUES (?, ?, ?, $userid)";


                if ($stmt = $conn->prepare($sql)) {
                    // Bind variables to the prepared statement as parameters
                    $stmt->bind_param("sss", $param_firstname, $param_lastname, $param_email);

                    // Set parameters
                    $param_firstname = $firstname;
                    $param_lastname = $lastname;
                    $param_email = $email;

                    // Attempt to execute the prepared statement
                    if ($stmt->execute()) {
                        $sql = "UPDATE users
                        SET paired = 1
                        WHERE id = $userid";
                        $conn->query($sql);

                        // Redirect to login page
                        header("location: employees.php");
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close statement
                    $stmt->close();
                }


                // Close connection
                $conn->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="wrapper">
        <div class="logo"><img src="/imgs/nnh_logo.png" alt="Company Logo"></div>
        <h2 style="margin-top: 57px;">Link</h2>
        <p class="clearfix">Please fill this form to link your account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>First name</label>
                <input type="text" name="firstname" class="form-control <?php echo (!empty($firstname_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $firstname_err; ?></span>
            </div>
            <div class="form-group">
                <label>Last name</label>
                <input type="text" name="lastname" class="form-control <?php echo (!empty($lastname_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $lastname_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
        </form>
    </div>
</body>

</html>