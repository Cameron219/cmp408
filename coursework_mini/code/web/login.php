<?php
    session_start();
    $username = "admin";
    $password = "admin";
    $INVALID = -1;
    $WAIT    = 0;
    $VALID   = 1;

    if(isset($_GET['do'])) {
        if($_GET['do'] == "scan") {
            $response = shell_exec("./scanner.py");

            if($response == $VALID) {
                $_SESSION['username'] = 'admin';
            }
            die($response);
        } else if($_GET['do'] == "logout") {
            unset($_SESSION['username']);
            header("Location: /index.php");
            die();
        }
    }

    if(isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password'])) {
        $user = stripslashes($_POST['username']);
        $pass = stripslashes($_POST['password']);
        if($user == $username && $pass == $password) {
            // echo "Valid";
        } else {
            header("Location: /index.php");
            die();
        }
    } else {
        header("Location: /index.php");
        die();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>RFID Login Prototype</title>
        <link rel="stylesheet" href="main.css"/>
    </head>

    <body>
        <div class="error">Error: <span class="error-message">Failed to do something....</span></div>
        <div class="container">
            <img class="nfc" src="nfc.png"/>
            <h2 class="message">Please scan RFID Card</h2>
        </div>

        <script>
            let RESPONSE_INVALID = -1,
                RESPONSE_WAIT = 0,
                RESPONSE_VALID = 1;

            let interval = setInterval(check_if_scanned, 1000),
                error_timeout;

            let error_div = document.querySelector("div.error"),
                error_span = document.querySelector("span.error-message"),
                nfc_img = document.querySelector("img.nfc");

            function check_if_scanned() {
                let get = new XMLHttpRequest();
                get.onreadystatechange = function() {
                    if(get.readyState == 4) {
                        if(get.status == 200) {
                            let resp = get.responseText;
                            if(resp == RESPONSE_WAIT) {
                                nfc_img.src = "nfc.png";
                                return;
                            } else if(resp == RESPONSE_VALID){
                                nfc_img.src = "nfc-green.png";
                                // clearInterval(interval);
                                setTimeout(function(){
                                    window.location.href = "index.php";
                                }, 1000);
                            } else {
                                nfc_img.src = "nfc-red.png";
                                show_error(resp == RESPONSE_INVALID ? "Invalid Scan" : "Unexpected response: " + resp);
                            }
                        } else if(get.status !== 0) {
                            show_error("HTTP Request returned " + get.status + " : scan.php");
                        }
                    }
                }
                get.open("GET", "login.php?do=scan", true);
                get.send();
            }
            
            function show_error(message) {
                console.error(message);
                error_span.innerText = message;
                error_div.style.display = "block";
                clearTimeout(error_timeout);
                error_timeout = setTimeout(function() { error_div.style.display = 'none'; }, 5e3);
            }

            setInterval(function() {
                // window.location.href = "index.php";
            },60000); // 60 seconds
        </script>
    </body>
</html>