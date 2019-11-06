<?php
    $username = "admin";
    $password = "admin";
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
            let interval = setInterval(check_if_scanned, 1000);
            let error_timeout;
            let error_div = document.querySelector("div.error"),
                error_span = document.querySelector("span.error-message");

            function check_if_scanned() {
                let get = new XMLHttpRequest();
                get.onreadystatechange = function() {
                    if(get.readyState == 4) {
                        if(get.status == 200) {

                        } else {
                            show_error("HTTP Request returned " + get.status + " : scan.php");
                            // clearInterval(interval);
                        }
                    }
                }
                get.open("GET", "scan.php", true);
                get.send();
            }
            
            function show_error(message) {
                console.error(message);
                error_span.innerText = message;
                error_div.style.display = "block";
                clearTimeout(error_timeout);
                error_timeout = setTimeout(function() { error_div.style.display = 'none'; }, 5e3);
            }
        </script>
    </body>
</html>