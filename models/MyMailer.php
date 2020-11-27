<?php

    namespace Marnix;

    ini_set('display_errors', E_ALL);
    error_reporting(1);

    include_once('vendor\phpmailer\phpmailer\src\PHPMailer.php');
    include_once('vendor\phpmailer\phpmailer\src\SMTP.php');
    include_once('vendor\phpmailer\phpmailer\src\Exception.php');

//    use PHPMailer\PHPMailer\PHPMailer as P;
//    use PHPMailer\PHPMailer\SMTP as S;
//    use PHPMailer\PHPMailer\Exception as E;

    require 'vendor/autoload.php';

    class MyMailer
    {
        private string $name;
        private string $sender;
        private string $smtp_server;
        private int $smtp_port;

        public function __construct($name, $sender, $smtp_server, $smtp_port)
        {
            $this->name = $name;
            $this->sender = $sender;
            $this->smtp_server = $smtp_server;
            $this->smtp_port = $smtp_port;
        }

        public function phpmailer($to, $subject, $message): bool
        {
            $result = false;

            // Instantiation and passing `true` enables exceptions
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;              // Enable verbose debug output
                $mail->isSMTP();                                    // Send using SMTP
                $mail->Host = 'smtp.office365.com';                 // Set the SMTP server to send through
                $mail->SMTPAuth = true;                             // Enable SMTP authentication
                $mail->Username = 'mvwoudenberg@ogt013.nl';         // SMTP username
                // NEVER TYPE YOUR PASSWORD IN A FILE!
                $mail->Password = $this->getPassword();             // SMTP password (partly encrypted)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port = 587;                                  // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                //Recipients
                $mail->setFrom($this->sender, $this->name);
                $mail->addAddress($to);     // Add a recipient
                $mail->addReplyTo($this->sender, 'Admin');

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->AltBody = htmlspecialchars_decode($message);

                // Send the email!
                $result = $mail->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            return $result;
        }

        private function getPassword(): string
        {
            return base64_decode('V3JTPzROcSQ=');
        }

        public function mail($to, $subject, $body): bool
        {
            // Is the OS Windows or Mac or Linux
            if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
                $eol = "\r\n";
            } elseif (strtoupper(substr(PHP_OS, 0, 3) == 'MAC')) {
                $eol = "\r";
            } else {
                $eol = "\n";
            }

            // File for Attachment
//            $f_name = "../../letters/" . $letter;    // use relative path OR ELSE big headaches. $letter is my file for attaching.
//            $handle = fopen($f_name, 'rb');
//            $f_contents = fread($handle, filesize($f_name));
//            $f_contents = chunk_split(
//                base64_encode($f_contents)
//            );    //Encode The Data For Transition using base64_encode();
//            $f_type = filetype($f_name);
//            fclose($handle);

            // To Email Address
//            $emailaddress = "user@example.com";

            // Message Subject
//            $emailsubject = "Heres An Email with a PDF" . date("Y/m/d H:i:s");

            // Common Headers
            $headers = 'From: "' . $this->sender . '"' . $eol;
            $headers .= 'Reply-To: "' . $this->sender . '"' . $eol;
            $headers .= 'Return-Path: "' . $this->sender . '"' . $eol;     // these two to set reply address
            $headers .= "Message-ID:<" . date("Y-m-d H:i:s") . " TheSystem@" . $_SERVER['SERVER_NAME'] . ">" . $eol;
            $headers .= "X-Mailer: PHP v" . phpversion() . $eol;           // These two to help avoid spam-filters
            // Boundry for marking the split & Multitype Headers
            $mime_boundary = md5(time());
            $headers .= 'MIME-Version: 1.0' . $eol;
            $headers .= "Content-Type: multipart/related; boundary=\"" . $mime_boundary . "\"" . $eol;
            $msg = "";

            // Setup for text OR html
            $msg .= "Content-Type: multipart/alternative" . $eol;

            // Text Version
            $msg .= "--" . $mime_boundary . $eol;
            $msg .= "Content-Type: text/plain; charset=iso-8859-1" . $eol;
            $msg .= "Content-Transfer-Encoding: 8bit" . $eol;
            $msg .= "This is a multi-part message in MIME format." . $eol;
            $msg .= "If you are reading this, please update your email-reading-software." . $eol;
            $msg .= "+ + Text Only Email from Genius Jon + +" . $eol . $eol;

            // HTML Version
            $msg .= "--" . $mime_boundary . $eol;
            $msg .= "Content-Type: text/html; charset=iso-8859-1" . $eol;
            $msg .= "Content-Transfer-Encoding: 8bit" . $eol;

            // ADD THE BODY HERE!
            $msg .= $body . $eol . $eol;

            // Finished
            $msg .= "--" . $mime_boundary . "--" . $eol . $eol;   // finish with two eol's for better security. see Injection.

            // SEND THE EMAIL
            ini_set(sendmail_from, $this->sender);  // the INI lines are to force the From Address to be used !
            $success = mail($to, $subject, $msg, $headers);
            ini_restore(sendmail_from);

            return $success;
        }

        private function sanitize_my_email($field)
        {
            $field = filter_var($field, FILTER_SANITIZE_EMAIL);
            if (filter_var($field, FILTER_VALIDATE_EMAIL)) {
                return true;
            } else {
                return false;
            }
        }

        public function guru99email($to, $subject, $message): bool
        {
//            $to_email = $this->sender;
//            $subject = 'Testing PHP Mail';
//            $message = 'This mail is sent using the PHP mail ';
            $headers = 'From: "' . $this->sender . '"';
            //check if the email address is invalid $secure_check
            $secure_check = $this->sanitize_my_email($to);
            $result = false;
            if ($secure_check == false) {
                echo "<script>console.log('Invalid input');</script>";
            } else { //send email
                $result = mail($to, $subject, $message, $headers);
                echo "<script>console.log('This email is sent using PHP Mail');</script>";
            }

            return $result;
        }

    }