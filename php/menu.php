<?php
if (!isset($_SESSION)) {
    session_start();
}

$role = $_SESSION['role'] ?? '';
?>

<?php if ($role === 'Customer'): ?>

    <a href="home.php">Home</a>
    <a href="designers.php">Designers</a>

    <a href="booking.php">Booking</a>
    <a href="mybookings.php">My Booking</a>
    <a href="meeting.php">Meeting</a>
    <a href="settings.php">Settings</a>
    <a href="logout.php" style="color:red; font-weight:bold;">Logout</a>

<?php elseif ($role === 'Designer'): ?>

    <a href="home.php">Home</a>
    <a href="designers.php">Designers</a>
    <a href="request.php">Requests</a>
    <a href="designer-profile.php">My Profile</a>
    <a href="settings.php">Settings</a>
    <a href="logout.php" style="color:red; font-weight:bold;">Logout</a>

<?php else: ?>
     <a href="home.php">Home</a>
    <a href="designers.php">Designers</a>
   
 

<?php endif; ?>

