<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Main page / e-pizzeria</title>
    <?php include_once 'meta.php'; ?>
</head>

<body>
    <div class="container"> 
        <div class="navbar">
            <a href="index.php"><img src="images/logo.png" class="logo" alt="logo"></a>
            <header>
                <h3>e-pizzeria</h3>
                <span>Main page</span>
            </header>
            <?php include_once 'nav.php'; ?>
            <div class="menu-icon menu-label">Menu</div>
            <img src="images/menu.png" class="menu-icon" onclick="togglemenu()" alt="menu">
        </div>
        <div class="row">
            <div class="col-1">
                <h2>New flavors</h2>
                <h3>See what's new in e-pizzeria</h3>
                <form method="get" action="reservation.php">
                    <button type="submit">Reserve now!</button>
                </form>
                <h4>-20% for standard variants (no extra ingredients)</h4>
            </div>
            <div class="col-2">
                <img src="images/pizza_index.png" class="controller" alt="pizza image">
                <figcaption style="position:absolute; bottom:15px; left:30px; font-size:10px; font-weight:bold;">
                    <a href="https://www.flaticon.com/free-icons/pizza" style="color:black;" title="pizza icons">Pizza icons created by Smashicons - Flaticon</a>
                </figcaption>
                <div class="color-box"></div>
                <div class="add-btn">
                    <a href="gallery.php">
                        <img src="images/add.png" alt="add button">
                        <p><small>See our pizzas</small></p>
                    </a>
                </div>
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
