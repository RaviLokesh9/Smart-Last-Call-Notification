<?php
session_start();

// Database connection details
$host = 'localhost';
$dbname = 'airline_db';
$username = 'root';
$password = '';

// Connect to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $staffID = $_POST['staffID'];
    $designation = $_POST['designation'];

    // Query to check staff credentials
    $sql = "SELECT * FROM staff WHERE staff_id = :staffID AND designation = :designation";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['staffID' => $staffID, 'designation' => $designation]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($staff) {
        // Login successful
        $_SESSION['staff_id'] = $staff['staff_id'];
        $_SESSION['designation'] = $staff['designation'];

        // Redirect based on designation
        if ($staff['designation'] === 'IGF') {
            header("Location: luggage.php");
        } else {
            header("Location: flightdetails.php");
        }
        exit();
    } else {
        // Login failed
        header("Location: staff.php?error=Invalid Staff ID or Designation");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: url('staff.jpg') no-repeat center center/cover;
        }

        /* Navigation Bar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        .navbar-left {
            display: flex;
            align-items: center;
        }

        .navbar-left img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .navbar-left h2 {
            margin: 0;
        }

        .navbar-right a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
            transition: color 0.3s;
        }

        .navbar-right a:hover {
            color: #007bff;
        }

        /* Login Container */
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            margin-top: 80px;
        }

        h2 {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="navbar-left">
            <img src="logo.ico" alt="Flight Logo">
            <h2>Flight Board</h2>
        </div>
        <div class="navbar-right">
            <a href="mainpage.html">Back to Login</a>
            <a href="about.html">About</a>
        </div>
    </div>

    <!-- Login Form -->
    <div class="login-container">
        <h2>Staff Login</h2>
        <form method="POST" action="staff.php">
            <label for="designation">Designation:</label>
            <select id="designation" name="designation">
                <option value="IGA">IGA</option>
                <option value="IGF">IGF</option>
            </select>

            <label for="staffID">Staff ID:</label>
            <input type="text" id="staffID" name="staffID" placeholder="Enter Staff ID" required>

            <button type="submit">Login</button>
        </form>

        <?php if (isset($_GET['error'])): ?>
            <p class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
    </div>

</body>
</html>
