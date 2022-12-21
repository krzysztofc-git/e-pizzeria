<nav>
    <ul id="menuList">
        <li><a href="index.php">Main Page</a></li>
        <li><a href="reservation.php">Reserve</a></li>
        <li><a href="status.php">Check Order</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            } 
            if(isset($_SESSION['login'])){
                ?><li><a href="logout.php">Log Off <?php echo $_SESSION['login']; ?></a></li><?php
            }else{
                ?><li><a href="login.php">Login / Register</a></li><?php
            }
        ?>
    </ul>
</nav>
