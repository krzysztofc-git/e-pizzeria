<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register / e-pizzeria</title>
    <?php include_once 'meta.php'; ?>
</head>
<?php
$access_code = "123456";

// Register is protected with a code that the boss gives employees for registration
// the secret code can be displayed in the right column of the page (<div class="col-2-normal">)
if(isset($_POST['code']) && (strcmp($_POST['code'], $access_code) != 0)){
    header("Location: signup.php?error=1");
}else if(isset($_POST['login']) && isset($_POST['password']) && isset($_POST['name']) && isset($_POST['last_name']) ){
    if($_POST['login'] != "" && $_POST['password'] != "" && $_POST['name'] != "" && $_POST['last_name'] != ""){
        
        include 'database.php';
        $login = $_POST['login'];
        $password = $_POST['password'];

        $sql = "SELECT login FROM users WHERE login='$login'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 0){
            // Register in database

            echo "Checking if client exists";
            $sql = "SELECT id_client FROM clients WHERE name = '".$_POST['name']."' AND last_name = '".$_POST['last_name']."'";
            $result = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($result) == 0) {
                // client does not exist
                echo 'creating client';
                $sql = "INSERT INTO clients(name,last_name) VALUES ('".$_POST['name']."','".$_POST['last_name']."')";
                $result = mysqli_query($conn, $sql);
                $sql = "SELECT id_client FROM clients WHERE name = '".$_POST['name']."' AND last_name = '".$_POST['last_name']."'";
                $result = mysqli_query($conn, $sql);
            } else {
                echo 'Client found';
            }
            $row = mysqli_fetch_row($result);
            $id_client = $row[0];
            echo "test:".$id_client;

            $sql = "INSERT INTO users (login, password, id_client) VALUES ('$login','$password',$id_client);";
            $result = mysqli_query($conn, $sql);
            mysqli_close($conn);
            header("Location: login.php?error=0");
        }else{
            mysqli_close($conn);
            header("Location: signup.php?error=2");
        }
    }else{
        header("Location: signup.php?error=2");
    }
}
?>
<body>
    <div class="container"> 
        <div class="navbar">
            <a href="index.php"><img src="images/logo.png" class="logo" alt="logo"></a>
            <header>
                <h3>e-pizzeria</h3>
                <span>Register</span>
            </header>
            <?php include_once 'nav.php'; ?>
            <div class="menu-icon menu-label">Menu</div>
            <img src="images/menu.png" class="menu-icon" onclick="togglemenu()" alt="menu">
        </div>
        <div class="row row-up">
            <div class="col-1">
                <h2>Register</h2>
                <h1>(employees only)</h1><br>
                <form method="post" autocomplete="off">
                    <label for="Login">Login:</label><br>
                    <input type="text" id="Login" name="login" required><br><br>
                    <label for="password">Password:</label><br>
                    <input type="password" id="password" name="password" required><br><br>
                    <label for="Name">Name:</label><br>
                    <input type="text" id="Name" name="name" required><br><br>
                    <label for="LastName">Last name:</label><br>
                    <input type="text" id="LastName" name="last_name" required><br><br>
                    <label for="Code">Access code:</label><br>
                    <input type="password" id="Code" name="code" required><br><br>
                    <button type="submit">Register</button>
                </form><br>
                <p>Already have an account? Go <a href="login.php">here</a> and Log In</p>
            </div>
            <div class="col-2-normal">
                <?php
                    // secret access code given below for easy registration (website testing)
                    //echo "(Access code to $access_code)<br>"
                ?>
                <?php if(isset($_GET['error']) && $_GET['error']==1){echo "<h3 style=color:red;>Access code is not right</h3><br>Try again or ask your administrator if the code is correct<br><br>";}?>
                <?php if(isset($_GET['error']) && $_GET['error']==2){echo "<h3 style=color:red;>Login is taken</h3><br>Try something else<br><br>";}?>
            </div>
        </div>
        
        <?php include_once 'footer.php'; ?>
    </div>
    <script>
        var menuList = document.getElementById("menuList");
        menuList.style.maxHeight = "0px";
        function togglemenu(){
            if(menuList.style.maxHeight== "0px"){
                menuList.style.maxHeight = "220px";
            }else{
                menuList.style.maxHeight = "0px";
            }
        }
    </script>    
</body>
</html>
