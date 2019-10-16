<?php
    if(isset($_POST['pin']) && isset($_POST['status'])) {
        $pin = $_POST['pin'];
        $status = $_POST['status'];

        die($pin . " : ". $status);
        // echo shell_exec('usertest write ' . $pin . ' ' . $status);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>RPi Pin Manager</title>
        <style>
            span {
                display: block;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <form method="POST" id="update">
                <span>GPIO Pin Number <input type="text" id="pin"/></span>
                <span>GPIO Pin Value
                    <select id="status">
                        <option value="0" selected>0</option>
                        <option value="1">1</option>
                    </select>
                </span>
                <input type="submit" id="update" value="Update Status"/>
            </form>
        </div>

        <script>
            let form = document.querySelector("form#update"),
                pin = document.querySelector("input#pin"),
                status = document.querySelector("select#status");

            form.addEventListener("submit", function(e) {
                e.preventDefault();
                let post = new XMLHttpRequest();
                post.onreadystatechange = function() {
                    if(post.readyState == 4 && post.status == 200) {
                        console.log("[POST] Response", post.responseText);
                    }
                }
                post.open("POST", "index.php", true);
                post.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                post.send("pin=" + pin.value + "&status=" + status.value);
                console.log("[POST] Send", pin.value, status.value);
            }, true);
        </script>
    </body>
</html>