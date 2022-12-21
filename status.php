<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Status<?php if(isset($_GET['id']))echo " ".$_GET['id']; ?> / e-pizzeria</title>
    <?php include_once 'meta.php'; $enable_script = 0; $notification = 0; ?>
</head>

<body>
    <div class="container"> 
        <div class="navbar">
            <a href="index.php"><img src="images/logo.png" class="logo" alt="logo"></a>
            <header>
                <h3>e-pizzeria</h3>
                <span>Status</span>
            </header>
            <?php include_once 'nav.php'; ?>
            <div class="menu-icon menu-label">Menu</div>
            <img src="images/menu.png" class="menu-icon" onclick="togglemenu()" alt="menu">
        </div>
        <div class="row row-up">
            <div class="col-1">
                <!-- change of order status by a logged in employee -->
                <?php if(isset($_SESSION['login']) && (isset($_GET['id_order']) && isset($_GET['order_status']))){ 
                    include "database.php";
                    $sql = "UPDATE orders SET order_status = ".$_GET['order_status'].", id_msg = NULL WHERE id_order = ".$_GET['id_order'];
                    $result = mysqli_query($conn, $sql);

                    if(isset($_GET['message']) && $_GET['message'] !=""){
                        // checking if message is in 'messages'
                        $sql = "SELECT id_msg FROM messages WHERE message = '".$_GET['message']."'";
                        $result = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($result) != 0) {
                            $row = mysqli_fetch_row($result);
                            $id_msg = $row[0];
                        } else {
                            // creating new message in 'messages'
                            $sql = "INSERT INTO messages(message) VALUES ('".$_GET['message']."')";
                            $result = mysqli_query($conn, $sql);

                            $sql = "SELECT id_msg FROM messages WHERE message = '".$_GET['message']."'";
                            $result = mysqli_query($conn, $sql);
                            $row = mysqli_fetch_row($result);
                            $id_msg = $row[0];
                        }
                        $sql = "UPDATE orders SET id_msg = ".$id_msg." WHERE id_order = ".$_GET['id_order'];
                        $result = mysqli_query($conn, $sql);

                    }

                    // status change by the employee -> assigning the employee to the customer's order
                    if(!isset($_SESSION['username_id'])){echo 'aaaa';}
                    $sql = "UPDATE orders SET id_employee = ".$_SESSION['username_id']." WHERE id_order = ".$_GET['id_order'];
                    $result = mysqli_query($conn, $sql);
                }
                ?>

                <!-- Order reservation (anyone can reserve) -->
                <?php if(isset($_GET['id_order']) && isset($_GET['name']) && isset($_GET['last_name'])){
                    echo "Saving in the database an order with ID ".$_GET['id_order']."...<br>";
                    include "database.php";
                    $sql = "SELECT id_order FROM orders WHERE id_order = ".$_GET['id_order'];
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_row($result);
                    // checking if ID in database
                    if(mysqli_num_rows($result)>0){
                        echo "Order registration error: This ID already exists!";
                    }else{
                        echo "Checking if client exists...";
                        $sql = "SELECT id_client FROM clients WHERE name = '".$_GET['name']."' AND last_name = '".$_GET['last_name']."'";
                        $result = mysqli_query($conn, $sql);
                        
                        if(mysqli_num_rows($result) == 0) {
                            // if client is not found in database
                            echo 'creating client';
                            $sql = "INSERT INTO clients(name,last_name) VALUES ('".$_GET['name']."','".$_GET['last_name']."')";
                            $result = mysqli_query($conn, $sql);
                            $sql = "SELECT id_client FROM clients WHERE name = '".$_GET['name']."' AND last_name = '".$_GET['last_name']."'";
                            $result = mysqli_query($conn, $sql);
                        } else {
                            echo 'Client found';
                        }
                        $row = mysqli_fetch_row($result);
                        $id_client = $row[0];
                        echo "test:".$id_client;
                        
                        $sql = "INSERT INTO orders (id_order,id_pizza,id_client,bill_needed,table_num,id_dough,id_size) VALUES (".$_GET['id_order'].",".$_GET['id_pizza'].",".$id_client.",".$_GET['bill_needed'].",".$_GET['table_num'].",".$_GET['id_dough'].",".$_GET['id_size'].")";
                        $result = mysqli_query($conn, $sql);

                        // is pizza having extras (if so they are added to 'extras' table)
                        foreach($_GET["extras"] as $extra){
                            $id_ingredient = substr($extra,2);
                            if(substr($extra,0,1) == '1'){
                                $proportion = 1;
                            }
                            if(substr($extra,0,1) == '2'){
                                $proportion = 2;  
                            }
                            $sql = "INSERT INTO extras (id_ingredient,id_order,proportion) VALUES (".$id_ingredient.",".$_GET['id_order'].",".$proportion.")";
                            $result = mysqli_query($conn, $sql);
                        }
                        

                        // removing GET arguments - redirecting to url using GET id
                        mysqli_close($conn);
                        header("Location:".$_SERVER['PHP_SELF']."?id_order=".$_GET['id_order']);
                    }

                

                ?><br><br>
                    <!--button if the ID already exists - error in order registration -->
                    <form method="get">
                        <button type="submit">Change ID</button>
                    </form>
                <?php
                }else if(isset($_GET['id_order'])){
                    include "database.php";
                    // checking if id_order is in the database
                    $sql = "SELECT id_order FROM orders WHERE id_order = ".$_GET['id_order'];
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_row($result);
                    if($row[0]==""){
                        echo "Error 404: This ID does not exist!";
                    }else{
                        echo "<h2>Order status</h2>";

                        $sql = "SELECT pizza_types.name
                        FROM pizza_types, orders 
                        WHERE orders.id_order = ".$_GET['id_order']."
                        AND orders.id_pizza = pizza_types.id_pizza";

                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_row($result);

                        $pizza_name = $row[0];

                        $sql = "SELECT clients.name, 
                        clients.last_name
                        FROM clients, orders
                        WHERE orders.id_order = ".$_GET['id_order']."
                        AND orders.id_client = clients.id_client";

                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_row($result);

                        $name = $row[0];
                        $last_name = $row[1];

                        $sql = "SELECT orders.date, 
                        orders.order_status, 
                        orders.bill_needed,
                        orders.table_num
                        FROM orders
                        WHERE orders.id_order = ".$_GET['id_order'];

                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_row($result);

                        $date = $row[0];
                        $order_status = $row[1];
                        $bill_needed = $row[2];
                        $table_num = $row[3];

                        $sql = "SELECT messages.message 
                        FROM messages, orders
                        WHERE orders.id_order = ".$_GET['id_order']."
                        AND orders.id_msg = messages.id_msg";

                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_row($result);

                        // cannot save null to $message
                        if(!isset($row[0])){
                            $message = "";
                        } else {
                            $message = $row[0];
                        }
                        

                        $sql = "SELECT dough_size.size, 
                        dough_size.proportion
                        FROM dough_size, orders
                        WHERE orders.id_order = ".$_GET['id_order']."
                        AND orders.id_size = dough_size.id_size";

                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_row($result);

                        $dough_size = $row[0];
                        $dough_proportion = $row[1];

                        $sql = "SELECT dough_types.dough_type
                        FROM dough_types, orders
                        WHERE orders.id_order = ".$_GET['id_order']."
                        AND orders.id_dough = dough_types.id_dough";

                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_row($result);

                        $dough_type = $row[0];

                        $sql = "SELECT extras.id_ingredient as id_ingredient, extras.proportion as proportion
                        FROM extras
                        WHERE extras.id_order = ".$_GET['id_order'];

                        
                        // counting extras
                        $result = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($result)>0){
                            echo "<b>Extras:</b><br>";
                            $sum_of_extras = 0;
                            while($row = mysqli_fetch_assoc($result)){
                                $id_skl_2_name = $row['id_ingredient'];
                                $prop_skl = $row['proportion'];

                                $sql = "SELECT ingredients.name, ingredients.price
                                FROM ingredients
                                WHERE ingredients.id_ingredient = ".$id_skl_2_name;
                                $result2 = mysqli_query($conn, $sql);
                                $row2 = mysqli_fetch_row($result2);

                                $ingredient_name = $row2[0];
                                $ingredient_price = $row2[1];

                                echo "+ ".$ingredient_name.", $".$ingredient_price." x".$prop_skl." = <b>$".$ingredient_price*$prop_skl."</b><br>";
                                $sum_of_extras += $ingredient_price*$prop_skl;
                            }
                        }

                        
                        $sql = "SELECT clients.name, clients.last_name
                        FROM clients, orders
                        WHERE orders.id_order = ".$_GET['id_order']."
                        AND orders.id_employee = clients.id_client";

                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_row($result);
                        
                        // when the employee is assigned
                        $czy_employee = 0;
                        if(mysqli_num_rows($result)>0){
                            $czy_employee = 1;
                            $employee_name = $row[0];
                            $employee_last_name = $row[1];
                        }
                        ?>
                        <h1>Pizza <?php echo $pizza_name."</h1><h3>[ID: ".$_GET['id_order']."]</h3>"; ?> <br>
                        <h5>Client's name:</h5> <?php echo $name; ?><br>
                        <h5>Client's last name:</h5> <?php echo $last_name; ?><br>
                        <h5>Date of reservation:</h5> <?php echo $date; ?><br>
                        <h5>Table number:</h5> <?php echo $table_num; ?><br><br>
                        <h5>Dough size:</h5> <?php echo $dough_size; ?><br>
                        <h5>Dough type:</h5> <?php echo $dough_type; ?><br><br>
                        <h5>Bill to be issued (at checkout)::</h5> <?php if ($bill_needed!=0){ echo "Yes"; } else { echo "No"; } ?><br>
                        <?php if(!isset($sum_of_extras)){ $sum_of_extras = 0; }  ?>
                        <h5>Price:</h5> According to <a href="gallery.php">MENU</a> <b>+ price for extras = $<?php echo $sum_of_extras ?></b><br><br>
                        <h5>Employee:</h5><?php if ($czy_employee!=0){ echo $employee_name." ".$employee_last_name; } else { echo "Not yet assigned"; } ?><br><br>
                        <h5>Reservation status:</h5> 
                        <?php if($order_status==0){echo "Waiting for confirmation by employees";}
                        else if($order_status==1){echo "Order accepted. In progress";}
                        else if($order_status==2){
                            $notification = 1;
                            echo "Delivered to the table";}
                        else if($order_status==3){
                            echo "Successfully paid for the order";
                            if ($bill_needed!=0){
                                echo " and received the bill at checkout";
                            echo "<br><br><b>Here is the invoice in electronic version (prints this page):</b>";
                            echo "<br><a href='javascript:if(window.print)window.print()'>Print</a>";
                            echo " (for the full invoice you can print <a href='gallery.php'>MENU</a> too)";
                            }
                        }
                        else{echo "Order canceled";}; ?>
                        <br>
                        <?php if ($message!=""){ ?><br><h5>Feedback:</h5> <?php echo $message."<br>"; }
                    } ?>
                    <br>
                    <form method="get">
                        <button type="submit">Change ID</button>
                    </form><br>
                    
                    <?php if(isset($_SESSION['login'])){ ?>
                    <form method="get">
                        <input type="hidden" name="id_order" value="<?php echo "".$_GET['id_order']; ?>">
                        <label for="Pizzas">Change status:</label><br>
                        <select id="Pizzas" name="order_status">
                            <option value="0">To be accepted</option> <!-- default value of form select-->
                            <option value="1">Accept</option>
                            <option value="2">Delivered to the table</option>
                            <option value="3">Receive payment</option>
                            <option value="4">Cancel</option>
                        </select><br>
                        <div id="message_choice" style="display:none;">
                            <label for="Message">Return message (reason for the cancellation):</label><br>
                            <textarea id="Message" name="message"></textarea>
                        </div><br>
                        <button type="submit">Change status</button>
                    </form>
                    <?php } ?>
                    <?php
                    if(isset($_SESSION['login'])){include "database.php";}
                }else{
                    ?>
                    <form method="get">
                        <label for="ID">Enter order ID:</label><br>
                        <input type="number" id ="ID" name="id_order" min="100000" max="999999" placeholder="123456" required><br><br>
                        <button type="submit">Show status</button>
                    </form>
                    <?php
                } ?>
            </div>
            <div class="col-2-normal">
                <?php if(isset($_GET['id_order'])){ ?> <h4>Save your order ID</h4><br> <?php } ?>
                <p id="refreshInfo">The page refreshes automatically every 30 seconds</p><br>
                
                <canvas id="analogClock" width="102" height="102">Clock</canvas><br><br>
                <button onclick="stopClock()" id="stopClockButton">Stop updating</button>
                <br><br>
                Last updated <span id="datetime"></span><br><br>

                <!--A logged-in employee will see a list of IDs of unfinished orders here
                and in the view orders buttons to change the status.
                If name and last_name is in GET, we save it to the database (we remove last_name from get name so that there is only id),
                and if not, we show the record-->
                <?php
                    if(isset($_SESSION['login'])){
                        if(isset($_GET['id_order'])){
                            $enable_script = 1;
                        }
                        include "database.php";
                        echo "<hr><h2>Orders (latest&nbsp;on&nbsp;the&nbsp;top)</h2>";
                        echo "<h3>to be accepted:</h3>";
                        echo "<ul class='themed-list pizza-list' style=text-align:left;>";
                        $sql = "SELECT orders.id_order, pizza_types.name as pizza_name FROM pizza_types, orders WHERE orders.order_status = 0 AND orders.id_pizza = pizza_types.id_pizza ORDER BY orders.date DESC";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_array($result)){
                            echo "<li><a href=status.php?id_order=".$row[0].">".$row[0]."</a> (".$row[1].")</li>";
                        }
                        echo "</ul>";
                        echo "<h3>in progress:</h3>";
                        echo "<ul class='themed-list pizza-list' style=text-align:left;>";
                        $sql = "SELECT orders.id_order, pizza_types.name as pizza_name FROM pizza_types, orders WHERE orders.order_status = 1 AND orders.id_pizza = pizza_types.id_pizza ORDER BY orders.date DESC";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_array($result)){
                            echo "<li><a href=status.php?id_order=".$row[0].">".$row[0]."</a> (".$row[1].")</li>";
                        }
                        echo "</ul>";
                        echo "<h3>delivered to the table:</h3>";
                        echo "<ul class='themed-list pizza-list' style=text-align:left;>";
                        $sql = "SELECT orders.id_order, pizza_types.name as pizza_name FROM pizza_types, orders WHERE orders.order_status = 2 AND orders.id_pizza = pizza_types.id_pizza ORDER BY orders.date DESC";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_array($result)){
                            echo "<li><a href=status.php?id_order=".$row[0].">".$row[0]."</a> (".$row[1].")</li>";
                        }
                        echo "</ul>";
                        echo "<h3>of received payment:</h3>";
                        echo "<ul class='themed-list pizza-list' style=text-align:left;>";
                        $sql = "SELECT orders.id_order, pizza_types.name as pizza_name FROM pizza_types, orders WHERE orders.order_status = 3 AND orders.id_pizza = pizza_types.id_pizza ORDER BY orders.date DESC";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_array($result)){
                            echo "<li><a href=status.php?id_order=".$row[0].">".$row[0]."</a> (".$row[1].")</li>";
                        }
                        echo "</ul>";
                        echo "<h3>canceled:</h3>";
                        echo "<ul class='themed-list pizza-list' style=text-align:left;>";
                        $sql = "SELECT orders.id_order, pizza_types.name as pizza_name FROM pizza_types, orders WHERE orders.order_status = 4 AND orders.id_pizza = pizza_types.id_pizza ORDER BY orders.date DESC";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_array($result)){
                            echo "<li><a href=status.php?id_order=".$row[0].">".$row[0]."</a> (".$row[1].")</li>";
                        }
                        echo "</ul><hr>";
                    }
                ?>
                <br><br>
            </div>
        </div>
        <?php if ($_GET) { mysqli_close($conn); } ?>
        <?php include_once 'footer.php'; ?>
    </div>
    <div id="hidden_menu_print_frame" style="display:none"><iframe src="gallery.php" title="MENU"></iframe></div>
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
        
        // date of website update / refresh
        var dt = new Date();
        document.getElementById("datetime").innerHTML = dt.toLocaleTimeString();

        // Canvas clock script
        setupAnalogClock(document.getElementById("analogClock"), 100);
        function setupAnalogClock(canvas, clockWidth) {
            var ctx = canvas.getContext("2d");
            var centerX = canvas.width / 2;
            var centerY = canvas.height / 2;

            function tick() {
                var date = new Date();
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                drawStatic();

                var hours = date.getHours();
                ctx.strokeStyle = "black";
                ctx.lineWidth = 2;
                drawHand(clockWidth/3, hours * 30);

                var minutes = date.getMinutes();
                ctx.strokeStyle = "black";
                ctx.lineWidth = 2;
                drawHand(clockWidth/2, minutes * 6);

                var seconds = date.getSeconds();
                ctx.strokeStyle = "red";
                ctx.lineWidth = 1;
                drawHand(clockWidth/2, seconds * 6);


                function drawStatic() {
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, clockWidth/2, 0, 2 * Math.PI, false);
                    ctx.strokeStyle = "black";
                    ctx.lineWidth = 2;
                    ctx.stroke();
                    ctx.closePath();

                    ctx.beginPath();
                    ctx.arc(centerX, centerY, 2, 0, 2 * Math.PI, false);
                    ctx.fillStyle = "black";
                    ctx.fill();
                    ctx.closePath();

                    drawNumbers();

                    function drawNumbers() {
                        var i = 12;
                        ctx.strokeStyle = "black";
                        ctx.lineWidth = 2;
                        while(i > 0) {
                            ctx.save();
                            ctx.beginPath();
                            ctx.translate(centerX, centerY);
                            var angle = (i * 30) * Math.PI/180;
                            ctx.rotate(angle);
                            ctx.translate(0, -clockWidth/2);

                            ctx.moveTo(0, 0);
                            ctx.lineTo(0, 10);
                            ctx.stroke();
                            ctx.closePath();
                            ctx.restore();
                            i --;
                        }
                    }
                }

                function drawHand(length, angle) {
                    ctx.save();
                    ctx.beginPath();
                    ctx.translate(centerX, centerY);
                    ctx.rotate(-180 * Math.PI/180);
                    ctx.rotate(angle * Math.PI/180);
                    ctx.moveTo(0, 0);
                    ctx.lineTo(0, length);
                    ctx.stroke();
                    ctx.closePath();
                    ctx.restore();
                }
            }

            tick();
            intervalId = window.setInterval(tick, 1000);
            
        }

        // automatic page refresh
        function autoRefresh() {
            window.location = window.location.href;
        }
        var intervalId2 = setInterval('autoRefresh()', 30000);

        // stopping the Canvas clock
        function stopClock() {
            clearInterval(intervalId);
            clearInterval(intervalId2);
            document.getElementById("stopClockButton").innerHTML = "Refresh";
            document.getElementById("stopClockButton").onclick = autoRefresh;
            document.getElementById("refreshInfo").innerHTML = "Click the button below to check the status <br> of your order or receive an invoice after payment";
        }
        
        // script works when employee is logged in and when GET id exists
        // purpose is to avoid JavaScript warning
        // normal user is not allowed to type Cancel messages
        <?php if($enable_script==1){ ?>
            // showing the messages field when Cancel is selected
            document.getElementById('Pizzas').addEventListener('change', function (e) {
                if (e.target.value === "4") {
                    document.getElementById("message_choice").style.display = "block";
                } else {
                    document.getElementById("message_choice").style.display = "none";
                }
            });
        <?php } ?>
        
        // notification after 3 seconds when the order has the status for pickup
        // and the employee is not logged in
        <?php if(($notification == 1) && !isset($_SESSION['login'])){ ?>
            setTimeout(function() {
                stopClock();alert('Ready! The employer has delivered your order. After paying, you will receive a bill.');
            }, (3 * 1000));
        <?php } ?>

    </script>    
</body>
</html>
