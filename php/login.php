<?php
session_start();

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "root", "decoria", 8889);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // نقرأ المدخلات من الفورم
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {

        $stmt = $conn->prepare("
            SELECT userID, name, role, password 
            FROM user 
            WHERE email = ?
            LIMIT 1
        ");

        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {

                // كلمة السر مخزنة كـ MD5
                if (md5($password) === $row['password']) {

                    // تسجيل الدخول ناجح
                    $_SESSION['user_id'] = (int)$row['userID'];
                    $_SESSION['name']    = $row['name'];
                    $_SESSION['role']    = $row['role'];

                    // 🔥 التوجيه: الكل يروح على الهوم بيج
                    header('Location: ../html/home.php');
                    exit();

                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'Invalid email or password.';
            }

            $stmt->close();
        } else {
            $error = 'Database error, please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | DECORIA</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
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
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow: hidden;
    }

    @keyframes bgMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .login-container {
      background: var(--card);
      padding: 40px;
      border-radius: 14px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
      text-align: center;
      width: 360px;
      opacity: 0;
      transform: translateY(40px);
      animation: fadeInUp 1.2s ease forwards;
      transition: all 0.3s ease;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-container:hover {
      box-shadow: 0 10px 35px rgba(0,0,0,0.12);
      transform: scale(1.02);
    }

    h1 {
      font-family: 'Playfair Display', serif;
      color: var(--brand);
      margin-bottom: 20px;
      font-size: 28px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 16px;
      transition: 0.3s;
    }

    input:focus {
      border-color: var(--brand);
      outline: none;
      box-shadow: 0 0 5px rgba(59,77,59,0.3);
      transform: scale(1.01);
    }

    button {
      background: var(--brand);
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
      width: 100%;
      margin-top: 10px;
    }

    button:hover {
      background: var(--primary-btn-hover);
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(59,77,59,0.25);
    }

    .links {
      margin-top: 20px;
      font-size: 15px;
      animation: fadeIn 1.8s ease forwards;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .links a {
      color: var(--brand);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }

    .links a:hover {
      color: var(--primary-btn-hover);
      text-decoration: underline;
    }

    .error-msg {
      color: #b00020;
      font-size: 14px;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h1>Welcome Back</h1>

    <form action="login.php" method="post">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>

      <?php if ($error): ?>
        <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
    </form>

    <div class="links">
      <p>Don't have an account? <a href="register.php">Register here</a></p>
      <p><a href="home.php">← Back to Home</a></p>
    </div>
  </div>
</body>
</html>
