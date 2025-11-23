<?php
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
            $message = "<span style='color:green;'>Registered Successfully!</span>";
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
<link rel="stylesheet" href="../css/designers.css" />
<link rel="stylesheet" href="../css/settings.css" />

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
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  margin: 0;
  padding: 20px 0;
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
  text-align: center;
  width: 360px;
  animation: fadeInUp 1.2s ease forwards;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

input, select {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 1px solid var(--border);
  border-radius: 8px;
  font-size: 16px;
  transition: 0.3s;
}

input:focus, select:focus {
  border-color: var(--brand);
  box-shadow: 0 0 5px rgba(59,77,59,0.3);
}

input[type="submit"] {
  background: var(--brand);
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
  width: 100%;
}

input[type="submit"]:hover {
  background: var(--primary-btn-hover);
  box-shadow: 0 6px 15px rgba(59,77,59,0.25);
}

/* ✔️ checkbox مضبوط 100% */
.terms-label {
  display: flex;
  flex-direction: row-reverse; 
  justify-content: flex-end;
  align-items: center;
  width: 100%; 
  gap: 10px;
  font-size: 14px;
  margin-top: 10px;
  cursor: pointer;
}

.terms-label input {
  margin: 0;
}

/* ✔️ تنسيق رابط Login */
.links a {
  color: var(--brand);
  font-weight: 700;
  text-decoration: none;
}

.links a:hover {
  color: var(--primary-btn-hover);
  text-decoration: underline;
}
.terms-row {
  display: flex;
  align-items: center;
  justify-content: flex-start; /* يخلي الكلام يمين والمربع يسار */
  width: 100%;
  gap: 10px;
  margin-top: 10px;
}

.terms-row input {
  width: 18px;       /* يمنع تمدد المربع */
  height: 18px;
  margin: 0;
}

.terms-row label {
  font-size: 14px;
  cursor: pointer;
  white-space: nowrap; /* يمنع تكسير الكلام ويخليه بسطر واحد */
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

    <!-- ✔️ DOB بدون سنوات مستقبلية -->
    <input type="date" name="dob" max="2025-12-31" required>

    <input type="text" name="address" placeholder="Address" required>

    <!-- ✔️ checkbox مضبوط -->
    <div class="terms-row">
    <input type="checkbox" id="terms" required>
    <label for="terms">I accept the Terms of Use</label>
</div>

    <input type="submit" value="Register">
  </form>

  <p id="message"><?php echo $message; ?></p>

  <div class="links">
    Already have an account?
    <a href="login.html">Login here</a>
  </div>
</div>

<footer>
  <div class="footer-content">
    <p class="footer-text">
      © 2025 DECORIA — All rights reserved |
      <a href="terms.html">Terms & Conditions</a>
    </p>
    <img src="../photo/darlfooter.jpeg" class="footer-image">
  </div>
</footer>

</body>
</html>
