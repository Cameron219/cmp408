<?php
// If read POST request is sent
    if(isset($_POST['read'])) {
        if(isset($_POST['pin'])) {
            $pin = trim($_POST['pin']); // Trim excess whitespace at the beginning or end
            if(is_numeric($pin) && $pin >= 0 && $pin <= 30) {
                // Pin is a number & between 0 & 30 (inclusive)
                echo "Valid\n";
                echo shell_exec("sudo /home/pi/usertest read " . $_POST['pin']);
            } else {
                echo "Invalid Pin";
            }
        } else {
            echo "No pin specified";
        }
        die();
    } else if(isset($_POST['write'])) { // If write POST request is sent
        if(isset($_POST['pin']) && isset($_POST['value'])){
            $pin = trim($_POST['pin']); // Get pin & value & trim excess whitespace
            $value = trim($_POST['value']);
            if($value == "0" || $value == "1"){ // If valid value (0/1)
                if(is_numeric($pin) && $pin >= 0 && $pin <= 30){ // If pin is number and between 0 - 30
                    echo "Valid\n";
                    echo shell_exec("sudo /home/pi/usertest write " . $_POST['pin'] . " " . $_POST['value']);
                } else {
                    echo "Invalid Pin";
                }
            } else {
                echo "Invalid Pin Status";
            }
        } else {
            echo "No Pin/Value specified";
        }
        die(); // Stop execution.
    }
?>
<!DOCTYPE html>
<html>
    <head>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet"> 
        <style>
            html {
                margin: 0;
                padding: 0;
            }

            body {
                background: #310542;
                color: white;
                font-family: 'Open Sans', sans-serif;
            }

            div.pin-container {
                text-align: center;
                width: 100%;
            }

            div.pin-container span.label {
                font-size: 128px;
            }

            div.pin-container input#pin {
                font-size: 128px;
                width: 150px;
                background: #310542;
                border: none;
                border-bottom: solid 1px white;
                color: white;
            }

            span.loading {
                display: none;
            }

            img.light {
                display: block;
                margin: 0 auto;
            }

            img#light-on {
                display: none;
            }
        </style>
    </head>

    <body>
            <div class="pin-container">
                <span class="label">Pin # </span>
                <input type="text" name="pin" id="pin" maxlength="2" autocomplete="off" autofocus/>
                <br/><span class="loading">Loading...</span>
            </div>
            <img class="light" id="light-on" src="light-on.png" alt="on"/>
            <img class="light" id="light-off" src="light-off.png" alt="off"/>
    </body>

    <script>
        let pin = document.querySelector("input#pin"),
            light_on = document.querySelector("#light-on"),
            light_off = document.querySelector("#light-off"),
            loading = document.querySelector("span.loading"),
            light = false,
            read, write, timeout;

        /**
            * Toggle the correct image to show
         */
        function show_light() {
            light_on.style.display = light ? "block" : "none";
            light_off.style.display = light ? "none" : "block";
        }
        
        /**
            * Flip the current state of the LED
         */
        function toggle_light(e) {
            if(pin && pin.value && pin.value !== "" && !isNaN(pin.value)) {
                light = !light;
                toggle_loading(true);
                write_value(pin.value, light ? 1 : 0);
            } else {
                alert("Please enter a valid GPIO Pin Value");
            }
        }
        
        /**
            * Send a POST request to read the current value of the pin
         */
        function read_value(pno) {
            if(read && read.abort) read.abort();
            read = new XMLHttpRequest();
            read.onreadystatechange = function() {
                if(read.readyState == 4) {
                    if(read.status == 200) { // If the server response is 200 OK
                        toggle_loading(false);
                        let resp = read.responseText;
                        if(resp.indexOf("val:") > -1) { // If the server responds with a pin value
                            let value = resp[resp.indexOf("val:") + 4]; // Get the pin value
                            if(value == 0 || value == 1) {
                                light = value == 1; // Set the pin value (true if 1, false if 0)
                                show_light(); // Show the correct light
                            } else {
                                console.log("Unexpected Value", value);
                            }
                        } else {
                            console.log("Unexpected response", resp);
                        }
                    }
                }
            }
            read.open("POST", "index.php", true);
            read.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            read.send("read&pin=" + pno);
        }
        

        /**
            * Send a POST request to write the value of the current pin
         */
        function write_value(pno, val) {
            if(write && write.abort) write.abort();
            write = new XMLHttpRequest();
            write.onreadystatechange = function() {
                if(write.readyState == 4) {
                    if(write.status == 200) { // If the server response is 200 OK
                        toggle_loading(false);
                        let resp = write.responseText;
                        if(resp.indexOf("val:") > -1) { // If the server respinds with a pin value
                            let value = resp[resp.indexOf("val:") + 4]; // Get the pin value
                            if(value == 0 || value == 1) {
                                light = value == 1; // Set the pin value
                                show_light(); // Show the correct light
                            } else {
                                console.log("Unexpected Value", value);
                            }
                        } else {
                            console.log("Unexpected Response", resp);
                        }
                    } else {
                        console.log("Unexpected Server Response: " + write.status);
                    }
                }
            }
            write.open("POST", "index.php", true);
            write.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            write.send("write&pin=" + pno + " &value=" + val)
        }

        /**
            * Show a loading message
         */
        function toggle_loading(state) {
            loading.style.display = state ? "block" : "none";
        }

        // Set listener for clicking light
        light_on.addEventListener("click", toggle_light, true);
        light_off.addEventListener("click", toggle_light, false);

        /**
            * Listen for keystrokes on pin input
            * After 400ms of no input, send a read POST request
            * Updates the image to the corrent one based on the pin value
         */
        pin.addEventListener("keydown", function(e) {
            //console.log("KEYDOWN - Starting timeout");
            // If a timeout is currently waiting to trigger, clear it.
            clearTimeout(timeout);
            // Set a timeout for 400ms, so as to allow the user time to type the pin
            // Stops a request being sent for every keystroke
            timeout = setTimeout(function() {
                // If the user has actually entered a "valid" pin
                if(pin && pin.value && pin.value !== "" && !isNaN(pin.value)){
                    toggle_loading(true);
                    // console.log("KEYDOWN - Reached timeout");
                    read_value(pin.value);
                }
            }, 400);
        }, true);
    </script>
</html>
