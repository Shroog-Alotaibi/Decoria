<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config.php";


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$clientID = $_SESSION['user_id'];


$designers_query = "
SELECT d.designerID, u.name, d.specialty
FROM designer d
JOIN user u ON u.userID = d.designerID
";
$designers_result = $conn->query($designers_query);


$designs_result = null;
$selectedDesigner = null;

if (isset($_POST['designerID']) && !empty($_POST['designerID'])) {
    $selectedDesigner = intval($_POST['designerID']);

    $designs_query = "
    SELECT designID, title 
    FROM design
    WHERE designerID = $selectedDesigner
    ";
    $designs_result = $conn->query($designs_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DECORIA | Booking</title>

    <link rel="stylesheet" href="../css/decoria.css" />
    <link rel="stylesheet" href="../css/booking.css" />
    <link rel="stylesheet" href="../css/meeting.css" />
</head>

<body>

<header class="site-header">
    <div class="container header-container">
        <div class="brand">
            <img src="../photo/Logo.png.png" alt="DECORIA Logo" class="logo">
        </div>
        <p class="welcome-text">Book Your Designer</p>
        <div class="header-buttons">
            <button class="menu-toggle" id="openSidebar">☰</button>
        </div>
    </div>
</header>

<div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <?php include("menu.php"); ?>
</div>

<div id="overlay"></div>

<main class="container">
    <h2 class="section-title">Book Your Designer</h2>

   
    <form class="booking-form" method="POST" action="booking.php" enctype="multipart/form-data">

  
        <label for="designer">Choose Designer:</label>
        <select id="designer" name="designerID" required onchange="this.form.submit()">
            <option value="">Select a Designer</option>
            <?php 
            if ($designers_result && $designers_result->num_rows > 0) {
                while($row = $designers_result->fetch_assoc()) {
                    $selected = ($selectedDesigner == $row['designerID']) ? "selected" : "";
                    echo "<option value='{$row['designerID']}' $selected>
                            {$row['name']} ({$row['specialty']})
                          </option>";
                }
            }
            ?>
        </select>

       
        <label for="design">Choose design:</label>
        <select id="design" name="designID" required>
            <?php
            if ($selectedDesigner && $designs_result && $designs_result->num_rows > 0) {
                echo "<option value=''>Select a Design</option>";
                while($d = $designs_result->fetch_assoc()){
                    echo "<option value='{$d['designID']}'>{$d['title']}</option>";
                }
            } else {
                echo "<option value=''>Please select a designer first</option>";
            }
            ?>
        </select>

      
        <?php if ($selectedDesigner): ?>
        
        <label for="date">Choose Date:</label>
        <input type="date" id="date" name="date" required>

        <label for="time">Choose Time:</label>
        <input type="time" id="time" name="time" required>

        <label for="transactionPhoto">Upload Transaction Photo:</label>
        <input type="file" id="transactionPhoto" name="transactionPhoto" accept="image/*" required>

        <div class="btn-row">
            <button   class="form-buttonsbutton"type="submit" formaction="process_booking.php" class="form-buttonsbutton">Confirm Booking</button>
                
        </div>

        <?php endif; ?>

    </form>

</main>

<footer>
    <div class="footer-content">
        <p class="footer-text">
            © 2025 DECORIA — All rights reserved
        </p>
        <img src="../photo/darlfooter.jpeg" alt="DECORIA Footer Image" class="footer-image">
    </div>
</footer>

<script src="../js/sidebar.js"></script>

</body>
</html>

<?php $conn->close(); ?>
