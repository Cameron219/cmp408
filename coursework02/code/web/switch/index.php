<?php
    if(isset($_POST['read'])) {
        echo shell_exec("sudo /home/pi/usertest read " . $_POST['pin']);
        die("");
    } else if(isset($_POST['write'])) {
        echo shell_exec("sudo /home/pi/usertest write " . $_POST['pin'] . " " . $_POST['value']);
        die("");
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
                <input type="text" name="pin" id="pin" maxlength="2" value="23" autocomplete="off"/>
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

        function show_light() {
            light_on.style.display = light ? "block" : "none";
            light_off.style.display = light ? "none" : "block";
        }

        function toggle_light(e) {
            if(pin && pin.value && pin.value !== "" && !isNaN(pin.value)) {
                light = !light;
                toggle_loading(true);
                write_value(pin.value, light ? 1 : 0);
            } else {
                alert("Please enter a valid GPIO Pin Value");
            }
        }

        function read_value(pno) {
            if(read && read.abort) read.abort();
            read = new XMLHttpRequest();
            read.onreadystatechange = function() {
                if(read.readyState == 4) {
                    if(read.status == 200) {
                        toggle_loading(false);
                        let resp = read.responseText;
                        if(resp.indexOf("val:") > -1) {
                            let value = resp[resp.indexOf("val:") + 4];
                            if(value == 0 || value == 1) {
                                light = value == 1;
                                show_light();
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

        function write_value(pno, val) {
            if(write && write.abort) write.abort();
            write = new XMLHttpRequest();
            write.onreadystatechange = function() {
                if(write.readyState == 4) {
                    if(write.status == 200) {
                        toggle_loading(false);
                        let resp = write.responseText;
                        if(resp.indexOf("val:") > -1) {
                            let value = resp[resp.indexOf("val:") + 4];
                            if(value == 0 || value == 1) {
                                light = value == 1;
                                show_light();
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

        function toggle_loading(state) {
            loading.style.display = state ? "block" : "none";
        }

        light_on.addEventListener("click", toggle_light, true);
        light_off.addEventListener("click", toggle_light, false);

        pin.addEventListener("keydown", function(e) {
            console.log("KEYDOWN - Starting timeout");
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                if(pin && pin.value && pin.value !== "" && !isNaN(pin.value)){
                    toggle_loading(true);
                    console.log("KEYDOWN - Reached timeout");
                    read_value(pin.value);
                }
            }, 400);
        }, true);

        read_value(23);
    </script>
</html>
