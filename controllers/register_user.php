<?php

    // use strict types programming
//    declare(strict_types=1);
    ini_set('display_errors', E_ALL);
    error_reporting(1);

    if ($_SERVER['REQUEST_METHOD'] === "POST") {

        // Check if the values are correct
        if ($_POST['password'] !== $_POST['password_repeat']) {
            // passwords are NOT identical
            echo "Passwords do not match.<br>";
            exit;
        }
        // TODO: Validation on input
        // ...

        // Retrieve the login data:
        $fullname = htmlspecialchars($_POST['name']);
        $username = htmlspecialchars($_POST['username']);
        $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT); // htmlspecialchars()
        $email = htmlspecialchars($_POST['email']);

        // Set the default values for the database (unsafe):
        $db_user = "marnix";
        $db_password = "marnix";
        $database = "FileUploader";

        try {
            // Create a database connection
            $db_conn = new PDO("mysql:host=localhost;port=8889;dbname=$database", $db_user, $db_password);

            //// Check if the username is not already in use:

            // Inser the user into to database, using prepare()
            $sql = 'INSERT INTO user (username, name, password_hash, email) VALUES (?, ?, ?, ?);';
            $stmt = $db_conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->execute(array($username, $fullname, $password_hash, $email));

            if ($stmt->rowCount() === 1) {
                echo "<p>You are succesfully registered with the username: $username</p>";
                header('refresh:5; url=../views/login.html');
            } else if ($stmt->errorCode() === '23000') {
                if (strpos($stmt->errorInfo()[2], 'username')) {
                    echo "<p>Username already exists, please try another username.<br><br>";
                } else if (strpos($stmt->errorInfo()[2], 'email')) {
                    echo "<p>Emailaddress already exists, please use another email or login with your existing account.<br><br>";
                } else {
                    echo "<i>" . $stmt->errorInfo()[2] . " (" . $stmt->errorCode() . ")</i><br>";
                }
                echo "Return to <a href='../views/register.html'>Home</a>.";
                exit;
            } else {
                echo "<p>There was a problem registration. Please contact the system administrator.<br><br>";
                echo "<i>" . $stmt->errorInfo()[2] . " (" . $stmt->errorCode() . ")</i><br>";
                echo "Return to <a href='../views/register.html'>Home</a>.";
                exit;
            }
        } catch (PDOException $e) {
            // Give an error message:
            print "Error!: " . $e->getMessage() . " (" . $e->getCode() . ")<br/>";
            echo "Return to <a href='../views/register.html'>Home</a>.";
            exit();
        }

    }