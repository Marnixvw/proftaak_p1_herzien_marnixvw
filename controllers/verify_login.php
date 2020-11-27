<?php
    // use strict types programming
//    declare(strict_types=1);

    ini_set('display_errors', E_ALL);
    error_reporting(1);

    if ($_SERVER['REQUEST_METHOD'] === "POST") {

        // start the session
        session_start();

        // Retrieve the login data:
        $username = htmlspecialchars($_POST['username']);
        $login_password = $_POST['password'];

        // Set the default values for the database (unsafe):
        $db_user = "marnix";
        $db_password = "marnix";
        $database = "FileUploader";

        try {
            // Create a database connection
            $db_conn = new PDO("mysql:host=localhost;port=8889;dbname=$database", $db_user, $db_password);

            // Get the hash_code for this user:
            $sql = 'SELECT password_hash FROM user WHERE username = ?;';
            $stmt = $db_conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->execute(array($username));
            $hash_code = $stmt->fetchAll()[0]['password_hash'];

            // An empty array is returned if there are zero results to fetch, or FALSE on failure.
            if ($hash_code === false) {
                throw new Exception('Could not retrieve the result');
            } else if (count($hash_code) !== 1) {
                echo "Invalid username or password.<br><br>";
                echo "Return to <a href='../views/login.html'>login page</a>.";
                exit;
            } else {
                // check the hash_code
                if (!password_verify($login_password, $hash_code)) {
                    echo "Invalid username or password.<br><br>";
                    echo "Return to <a href='../views/login.html'>login page</a>.";
                    exit;
                } else {
                    // valid login
                    echo "<p>-== Yes! You are logged in correctly! ==-</p>";
                    $_SESSION['code'] = random_int(100000, 999999);
                    //$_SESSION['time'] = now();
                    $message = "Please enter the following code to login: " . $_SESSION['code'] . "<br>" .
                        "Autogenerated mail by FileUploader-app.";
                    echo $message . "<br>";
                    var_dump($_SESSION);
                    echo "<br>";


                    require_once ('../models/MyMailer.php');
                    $mymailer = new \Marnix\MyMailer('Marnix', 'mvwoudenberg@roctilburg.nl', 'smtp.office365.com', 587);
//                    $result = $mymailer->mail('marnix.vanwoudenberg@ictmbo.nl', 'Two-factor authentication for FileUploader', $message);
//                    $result = $mymailer->guru99email('marnix.vanwoudenberg@ictmbo.nl', 'Two-factor authentication for FileUploader', $message);
                    $result = $mymailer->phpmailer('marnix.vanwoudenberg@ictmbo.nl', 'Two-factor authentication for FileUploader', $message);
                    var_dump($result);
                    exit;
                    // store session variables
                    // redirect to the user menu
                }
            }
        } catch (PDOException $e) {
            // Give an error message:
            print "Error!: " . $e->getMessage() . "<br/>";
            echo "Return to <a href='../views/login.html'>login page</a>.";
            exit();
        }
    }