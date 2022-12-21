<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Log In / e-pizzeria</title>
    <?php include_once 'meta.php'; ?>
</head>
<?php
if(isset($_POST['login']) && isset($_POST['password'])){
    if($_POST['login'] != "" && $_POST['password'] != ""){

        include 'database.php';

        $login = $_POST['login'];
        $password = $_POST['password'];

        $sql = "SELECT login, id_client FROM users WHERE login='$login' AND password='$password' ";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            session_start();
            $_SESSION['login'] = $row['login'];

            $_SESSION['username_id'] = $row['id_client'];

            mysqli_close($conn);
            header("Location: status.php");
        }else{
            mysqli_close($conn);
            header("Location: login.php?error=1");
        }
    }else{
        header("Location: login.php?error=1");
    }
}
?>
<body>
    <div class="container"> 
        <div class="navbar">
            <a href="index.php"><img src="images/logo.png" class="logo" alt="logo"></a>
            <header>
                <h3>e-pizzeria</h3>
                <span>Login</span>
            </header>
            <?php include_once 'nav.php'; ?>
            <div class="menu-icon menu-label">Menu</div>
            <img src="images/menu.png" class="menu-icon" onclick="togglemenu()" alt="menu">
        </div>
        <div class="row row-up">
            <div class="col-1">
                <h2>Login</h2>
                <h1>(employees only)</h1><br>
                <form method="post" autocomplete="off">
                    <label for="Login">Login:</label><br>
                    <input type="text" id="Login" name="login" required><br><br>
                    <label for="password">Password:</label><br>
                    <input type="password" id="password" name="password" required><br><br>
                    <button type="submit">Log In</button>
                </form><br>
                <p>Are you a new employee? Go <a href="signup.php">here</a> and Register</p>
            </div>
            <div class="col-2-normal">
                <?php if(isset($_GET['error']) && $_GET['error']==1){echo "<h3 style=color:red;>Login error occurred</h3><br>Try again<br><br>";}?>
                <?php if(isset($_GET['error']) && $_GET['error']==0){echo "<h3 style=color:green;>Registration completed</h3><br>You can Log In now<br><br>";}?>
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
