<?php

session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Check empty fields
    if (
        empty($full_name) ||
        empty($username) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please complete all required fields.";

    }

    // Validate email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }

    // Password length
    elseif (strlen($password) < 6) {

        $error = "Password must contain at least 6 characters.";

    }

    // Confirm password
    elseif ($password !== $confirm_password) {

        $error = "Password and confirm password do not match.";

    }

    else {

        // Check existing username or email
        $check = $conn->prepare(
            "SELECT id
             FROM admins
             WHERE username = ? OR email = ?
             LIMIT 1"
        );

        $check->bind_param(
            "ss",
            $username,
            $email
        );

        $check->execute();

        $existing = $check
            ->get_result()
            ->fetch_assoc();

        $check->close();


        if ($existing) {

            $error = "Username or email is already registered.";

        }

        else {

            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // All registered accounts become Staff
            $role = "Staff";

            // Account active by default
            $status = "Active";

            $stmt = $conn->prepare(
                "INSERT INTO admins
                (
                    full_name,
                    username,
                    email,
                    phone_number,
                    password,
                    role,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssssss",
                $full_name,
                $username,
                $email,
                $phone_number,
                $password_hash,
                $role,
                $status
            );

            if ($stmt->execute()) {

                $stmt->close();

                header(
                    "Location: admin_login.php?registered=1"
                );

                exit();

            }

            else {

                $error = "Unable to create staff account.";

            }

            $stmt->close();

        }

    }

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

    <title>Staff Registration | Doon University</title>

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
            margin: 0;
            padding: 40px 15px;
        }

        .register-box {
            width: 100%;
            max-width: 600px;
            margin: auto;
            background: var(--surface);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
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
            margin: 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            color: var(--text);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 7px;
        }

        .required {
            color: #dc2626;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            font-size: 14px;
            transition: 0.3s;
        }

        input:focus {
            border-color: var(--primary);
        }

        .error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: var(--primary);
            color: white;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: var(--primary-dark);
        }

        .login-section {
            text-align: center;
            border-top: 1px solid var(--border);
            margin-top: 25px;
            padding-top: 20px;
            color: var(--muted);
            font-size: 14px;
        }

        .login-section a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13px;
        }

        @media (max-width: 600px) {

            .register-box {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

        }

    </style>

</head>

<body>

<div class="register-box">

    <div class="header">

        <div class="icon">
            👨‍💼
        </div>

        <h2>Create Staff Account</h2>

        <p class="subtitle">
            Register to access the application management system
        </p>

    </div>


    <?php if (!empty($error)): ?>

        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">

            <label>
                Full Name
                <span class="required">*</span>
            </label>

            <input
                type="text"
                name="full_name"
                placeholder="Enter your full name"
                value="<?php
                    echo htmlspecialchars(
                        $_POST['full_name'] ?? ''
                    );
                ?>"
                required
            >

        </div>


        <div class="form-row">

            <div class="form-group">

                <label>
                    Username
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="username"
                    placeholder="Choose a username"
                    value="<?php
                        echo htmlspecialchars(
                            $_POST['username'] ?? ''
                        );
                    ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Phone Number
                </label>

                <input
                    type="text"
                    name="phone_number"
                    placeholder="Enter phone number"
                    value="<?php
                        echo htmlspecialchars(
                            $_POST['phone_number'] ?? ''
                        );
                    ?>"
                >

            </div>

        </div>


        <div class="form-group">

            <label>
                Email Address
                <span class="required">*</span>
            </label>

            <input
                type="email"
                name="email"
                placeholder="Enter your email address"
                value="<?php
                    echo htmlspecialchars(
                        $_POST['email'] ?? ''
                    );
                ?>"
                required
            >

        </div>


        <div class="form-row">

            <div class="form-group">

                <label>
                    Password
                    <span class="required">*</span>
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Minimum 6 characters"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Confirm Password
                    <span class="required">*</span>
                </label>

                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Re-enter password"
                    required
                >

            </div>

        </div>


        <button
            type="submit"
            class="btn-register"
        >
            Create Staff Account
        </button>

    </form>


    <div class="login-section">

        Already have a staff account?

        <a href="admin_login.php">
            Sign In
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