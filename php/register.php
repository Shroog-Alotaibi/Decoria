<?php
session_start(); 

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $conn = new mysqli("localhost", "root", "root", "decoria");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // البيانات
    $name = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];

    $hashedPassword = md5($password);

    $check = "SELECT email FROM user WHERE email='$email'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        $message = "<span style='color:red;'>This email is already registered!</span>";
    } else {

        $sql = "INSERT INTO user (name, email, password, role, phoneNumber, gender, DOB, address)
                VALUES ('$name', '$email', '$hashedPassword', 'Customer', '$phone', '$gender', '$dob', '$address')";

        if ($conn->query($sql) === TRUE) {
            $userID = $conn->insert_id;
$_SESSION['user_id'] = $userID;

            // 🎯 نحفظ جلسة المستخدم
            $_SESSION['username'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = "Customer";

            // 🎯 بعد التسجيل يروح مباشرة للهوم
            header("Location: home.php");
            exit;

        } else {
            $message = "<span style='color:red;'>Error while saving data!</span>";
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Page</title>

<link rel="stylesheet" href="../css/decoria.css" />

<style>
:root {
  --bg1: #f8f5ee;
  --bg2: #eae6da;
  --brand: #3b4d3b;
  --border: #d8d3c5;
  --text: #2e2a23;
  --card: #ffffff;
  --primary-btn: #3b4d3b;
  --primary-btn-hover: #2c3e2f;
}

body {
  background: linear-gradient(135deg, var(--bg1), var(--bg2));
  background-size: 200% 200%;
  animation: bgMove 12s ease-in-out infinite;
  font-family: 'Tajawal', sans-serif;
  color: var(--text);
  margin: 0;
  padding: 40px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
}

@keyframes bgMove {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.register-container {
  background: var(--card);
  padding: 40px;
  border-radius: 14px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
  width: 360px;
  animation: fadeInUp 1.2s ease forwards;
  text-align: center;
}

input, select {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 1px solid var(--border);
  border-radius: 8px;
  font-size: 16px;
}

input[type="submit"] {
  background: var(--primary-btn);
  color: white;
  padding: 12px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  transition: .3s;
}

input[type="submit"]:hover {
  background: var(--primary-btn-hover);
}

.terms-row {
  display: flex;
  align-items: center;
  justify-content: flex-start; 
  margin: 10px 0 5px;
  gap: 10px;
}

.terms-row input {
  width: 18px;
  height: 18px;
}

.links a {
  color: var(--primary-btn);
  font-weight: 700;
  text-decoration: none;
}

.links a:hover {
  color: var(--primary-btn-hover);
  text-decoration: underline;
}

footer {
  width: 100%;
  margin-top: 40px;
}

.footer-content {
  width: 100%;
  text-align: center;
}

.footer-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
  display: block;
}
</style>
</head>

<body>

<div class="register-container">
  <h2>Register</h2>

  <form method="POST">

    <input type="text" name="username" placeholder="Username" required>

    <input type="email" name="email" placeholder="Email" required>

    <input type="text" name="phone" placeholder="Phone Number (e.g., 05XXXXXXXX)" required>

    <input type="password" name="password" placeholder="Password" required>

    <select name="gender" required>
      <option value="" disabled selected>Select Gender</option>
      <option value="female">Female</option>
      <option value="male">Male</option>
    </select>

    <input type="date" name="dob" required>

    <input type="text" name="address" placeholder="Address" required>

    <div class="terms-row">
        <input type="checkbox" id="terms" required>
        <label for="terms">I accept the Terms of Use</label>
    </div>

    <input type="submit" value="Register">
  </form>

  <p id="message"><?php echo $message; ?></p>

  <div class="links">
    Already have an account?
    <a href="login.php">Login here</a>
  </div>
</div>

<footer>
  <div class="footer-content">
    <p class="footer-text">
      ©️ 2025 DECORIA — All rights reserved |
      <a href="terms.php">Terms & Conditions</a>
    </p>
    <img src="../photo/darlfooter.jpeg" class="footer-image">
  </div>
</footer>

</body>
</html>
