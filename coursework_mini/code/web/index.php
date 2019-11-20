<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>RFID Login Prototype</title>
        <link rel="stylesheet" href="main.css"/>
    </head>

    <body>
        <div class="container">
            <?php
                if(isset($_SESSION['username'])){
            ?>
            <h1>Welcome <?php echo $_SESSION['username']; ?></h1>
            <a href="login.php?do=logout">Log Out</a>
            <?php
                } else {
            ?>
            <h1 class="login">Login</h1>
            <form method="POST" action="login.php">
                <input type="text" class="login-input" name="username" placeholder="Username" autocomplete="no" autofocus/>
                <input type="password" class="login-input" name="password" placeholder="Password"/>
                <input type="submit" class="login-submit" name="login" value="Login"/>
            </form>
            <?php
                }
            ?>
        </div>
    </body>
</html>