<?php

session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare(
        "SELECT id, username, password, full_name, role, status
         FROM admins
         WHERE username = ?
         LIMIT 1"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if (
        $admin &&
        $admin['status'] === 'Active' &&
        password_verify($password, $admin['password'])
    ) {

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_role'] = $admin['role'];

        header("Location: admin_dashboard.php");
        exit();

    } else {

        $error = "Invalid username or password.";

    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Staff Login | Doon University</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        :root {
            --primary: #1e3a8a;
            --primary-dark: #172554;
            --bg: #f3f4f6;
            --surface: #ffffff;
            --border: #e5e7eb;
            --text: #1f2937;
            --muted: #6b7280;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 30px 15px;
        }

        .login-box {
            background: var(--surface);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 430px;
            text-align: center;
        }

        .icon {
            width: 65px;
            height: 65px;
            background: #eff6ff;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 15px;
            font-size: 28px;
        }

        h2 {
            color: var(--primary);
            margin: 0 0 5px;
        }

        .subtitle {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: var(--primary);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: var(--primary-dark);
        }

        .error {
            color: #b91c1c;
            background: #fee2e2;
            border: 1px solid #fecaca;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .success {
            color: #166534;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 28px 0 20px;
            color: #9ca3af;
            font-size: 12px;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .register-text {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 12px;
        }

        .btn-register {
            display: block;
            width: 100%;
            padding: 12px;
            color: var(--primary);
            background: #eff6ff;
            border: 2px solid #bfdbfe;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: #dbeafe;
            border-color: var(--primary);
        }

        .back-link {
            display: block;
            margin-top: 25px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13px;
        }

        .back-link:hover {
            color: var(--primary);
        }

    </style>

</head>

<body>

<div class="login-box">

    <div class="icon">
        👨‍💼
    </div>

    <h2>Staff Portal</h2>

    <p class="subtitle">
        Sign in to manage student applications
    </p>

    <?php if (!empty($error)): ?>

        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <?php if (isset($_GET['registered'])): ?>

        <div class="success">
            Account registered successfully. You can now sign in.
        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">

            <label>Username</label>

            <input
                type="text"
                name="username"
                placeholder="Enter your username"
                required
            >

        </div>


        <div class="form-group">

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >

        </div>


        <button
            type="submit"
            class="btn-login"
        >
            Sign In
        </button>

    </form>


  

 <div style="
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
">

    <p style="
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 12px;
    ">
        Don't have a staff account?
    </p>

    <a
        href="admin_register.php"
        style="
            display: block;
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            background: #eff6ff;
            color: #1e3a8a;
            border: 2px solid #bfdbfe;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        "
    >
        Create Staff Account
    </a>

</div>


    <a
        href="index.php"
        class="back-link"
    >
        &larr; Back to Student Portal
    </a>

</div>

</body>
</html>