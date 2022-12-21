<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reservation / e-pizzeria</title>
    <?php include_once 'meta.php'; ?>
</head>

<body>
    <div class="container"> 
        <div class="navbar">
            <a href="index.php"><img src="images/logo.png" class="logo" alt="logo"></a>
            <header>
                <h3>e-pizzeria</h3>
                <span>Reservation</span>
            </header>
            <?php include_once 'nav.php'; ?>
            <div class="menu-icon menu-label">Menu</div>
            <img src="images/menu.png" class="menu-icon" onclick="togglemenu()" alt="menu">
        </div>
        <div class="row row-up">
            <div class="col-1">
                
                <?php
                    include "database.php";
                    // randomizing ID until it does not exist in database
                    do {
                        $random = rand(100000,999999);
                        $sql = "SELECT id_order FROM orders WHERE id_order=".$random;
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_array($result);
                    } while ($row!=NULL);
                ?>
                <form method="get" action="status.php">
                    <input type="hidden" name="id_order" value="<?php echo $random; ?>">
                    <label for="Pizza">Select pizza:</label><br>
                    <select id="Pizza" name="id_pizza">
                    <?php
                        $sql = "SELECT id_pizza,name FROM pizza_types";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)){
                    ?>
                        <option value="<?php echo $row['id_pizza']; ?>">Pizza <?php echo $row['name']; ?></option>
                    <?php
                        }
                    ?>
                    </select><br><br>

                    <label for="Size">Select size:</label><br>
                    <select id="Size" name="id_size" required>
                    <?php
                        $sql = "SELECT id_size, size FROM dough_size";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)){
                    ?>
                    <option value="<?php echo $row['id_size']; ?>"><?php echo $row['size']; ?></option>
                    <?php
                        }
                    ?>
                    </select><br><br>

                    <label for="Dough_size">Select dough size:</label><br>
                    <select id="Dough_size" name="id_dough" required>
                    <?php
                        $sql = "SELECT id_dough, dough_type FROM dough_types";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)){
                    ?>
                    <option value="<?php echo $row['id_dough']; ?>"><?php echo $row['dough_type']; ?></option>
                    <?php
                        }
                    ?>
                    </select><br><br>

                    <label for="Extras">Select Add-ons (Multi-Ingredient Selection <br>(Hold Ctrl)):</label><br>
                    <select id="Extras" name="extras[]" multiple>
                    <?php
                        $sql = "SELECT id_ingredient, name, price FROM ingredients";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)){
                    ?>
                    <option value="1_<?php echo $row['id_ingredient']; ?>"><?php echo $row['name'].", price: $".$row['price'].""; ?></option>
                    <option value="2_<?php echo $row['id_ingredient']; ?>"><?php echo $row['name']." x2, price: $".($row['price']*2).""; ?></option>
                    <?php
                        }
                        mysqli_close($conn);
                    ?>
                    </select><br><br>

                    <label for="Table_num">Table number:</label><br>
                    <input type="number" id="Table_num" name="table_num" required><br><br>

                    <label for="Name">Client's name:</label><br>
                    <input type="text" id="Name" name="name" required><br><br>
                    <label for="LastName">Client's last name:</label><br>
                    <input type="text" id="LastName" name="last_name" required><br><br>

                    <label for="Bill_needed">Do you need a bill?:</label><br>
                    <select id="Bill_needed" name="bill_needed" required>
                        <option value=1>Yes</option>
                        <option value=0>No</option>
                    </select><br><br>
                    
                    <button type="submit">Reserve</button>  
                </form>
            </div>
            <div class="col-2-normal">
                Reserve your pizza, be sure to enter all the information correctly.<br><br>
                
                <div class="add-btn like-button">
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
