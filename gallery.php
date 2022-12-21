<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gallery / e-pizzeria</title>
    <?php include_once 'meta.php'; ?>
    <link rel="stylesheet" href="gallery.css">
</head>

<body>
    <div class="container"> 
        <div class="navbar">
            <a href="index.php"><img src="images/logo.png" class="logo" alt="logo"></a>
            <header>
                <h3>e-pizzeria</h3>
                <span>Gallery</span>
            </header>
            <?php include_once 'nav.php'; ?>
            <div class="menu-icon menu-label">Menu</div>
            <img src="images/menu.png" class="menu-icon" onclick="togglemenu()" alt="menu">
        </div>
        <div class="row row-up">
            
            <div class="col-1">
                <?php 
                    include "database.php";
                    $sql = "SELECT id_pizza,name,url FROM pizza_types WHERE url is not NULL";
                ?>

                <!-- Gallery container -->
                <br>
                <div class="slideshow-container">
                    <?php
                        $i = 1;
                        $result = mysqli_query($conn, $sql);
                        $amount = mysqli_num_rows($result);
                        while($row = mysqli_fetch_assoc($result)){
                    ?>
                    <!-- Full-width images with number and caption text  -->
                    <div class="mySlides fade">
                        <div class="numbertext"><?php echo $i ?> / <?php echo $amount ?></div>
                        <img src="<?php echo $row['url'] ?>" style="max-width:100%;" alt="<?php echo $row['name'] ?>">
                        <div class="text"><?php echo $row['name'] ?></div>
                    </div>

                    <?php
                            $i++;
                        } 
                    ?>

                    <!-- prev and next buttons -->
                    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                    <a class="next" onclick="plusSlides(1)">&#10095;</a>
                    </div>
                    <br>

                    <!-- dots and pointers -->
                    <div style="text-align:center">
                    <?php for($i=1;$i<=$amount;$i++){ ?>
                    <span class="dot" onclick="currentSlide(<?php echo $i; ?>)"></span>
                    <?php } ?>
                </div>
            </div>
            <div class="col-2-normal">
                Latest menu, for day <?php echo date("Y-m-d"); echo "<br><a href='javascript:if(window.print)window.print()'>Print</a>"; ?>
                <h1>Check our products</h1>
                <br>
                <?php 
                    $sql = "SELECT pizza_types.name as pizza, ingredients.name as ingredient, is_standard, ingredients.price as price FROM pizza_types, ingredients, recipes WHERE recipes.id_pizza = pizza_types.id_pizza AND recipes.id_ingredient = ingredients.id_ingredient";
                    $result = mysqli_query($conn, $sql);
                ?>
                <?php
                $previous_pizza = "";
                while($row = mysqli_fetch_assoc($result)){
                    $selected_pizza = $row['pizza'];

                    $is_standard = $row['is_standard'];
                    
                    if($selected_pizza != $previous_pizza){
                        $price = 0;
                    ?>
                        </ul>
                        <hr>
                        <h3><?php echo $row['pizza'];?></h3>
                        <h5><?php if($is_standard){ echo "Standard pizza"; }else{ echo "Non-standard pizza";}?></h5>
                        <ul class="pizza-list">
                        <?php } $price += $row['price'];
                        echo "<li>".$row['ingredient']."</li>";

                        $sql = "SELECT id_dough, price FROM dough_types WHERE dough_types.id_dough = 1";
                        $result2 = mysqli_query($conn, $sql);
                        $row2 = mysqli_fetch_array($result2);
                        $price_thin_dough = $row2[1];

                        $sql = "SELECT id_dough, price FROM dough_types WHERE dough_types.id_dough = 2";
                        $result2 = mysqli_query($conn, $sql);
                        $row2 = mysqli_fetch_array($result2);
                        $price_thick_dough = $row2[1];
                        
                        $sql = "SELECT size, proportion FROM dough_size";
                        $result2 = mysqli_query($conn, $sql);
                        while($row2 = mysqli_fetch_assoc($result2)){
                            $pizza_size = $row2['size'];
                            $multiplier = $row2['proportion'];
                        
                        
                            $price_thin_pizza = $price_thin_dough + $price;
                            $price_thick_pizza = $price_thick_dough + $price;
                            echo "<p><br><u>- Pizza ".$pizza_size.":</u><br>";
                            if($is_standard) {
                                $discount_perc = 20;
                                echo "Thin dough: $".($price_thin_pizza*$multiplier)." <i>(+ price for extras x ".$multiplier.")</i> / <u>$".(($price_thin_pizza*(100-$discount_perc)/100)*$multiplier)." (without extras)</u> <br>";
                                echo "Thick dough: $".($price_thick_pizza*$multiplier)." <i>(+ price for extras x ".$multiplier.")</i> / <u>$".(($price_thick_pizza*(100-$discount_perc)/100)*$multiplier)." (without extras)</u></p>";
                            } else {
                                echo "Thin dough: <u>$".($price_thin_pizza*$multiplier)."</u><br>";
                                echo "Thick dough: <u>$".($price_thick_pizza*$multiplier)."</u></p>";
                            }
                        }

                        ?>
                        
                    <?php
                    $previous_pizza = $selected_pizza;
                }
                mysqli_close($conn); ?>
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

        // RESTAURANT MENU (Gallery)
        var slideIndex = 1;
        showSlides(slideIndex);
        // controlling prev and next
        function plusSlides(n) {
        showSlides(slideIndex += n);
        }
        // updating image
        function currentSlide(n) {
            showSlides(slideIndex = n);
        }
        // showing image
        function showSlides(n) {
            var i;
            var slides = document.getElementsByClassName("mySlides");
            var dots = document.getElementsByClassName("dot");
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex-1].style.display = "block";
            dots[slideIndex-1].className += " active";
        }
    </script>    
</body>
</html>
