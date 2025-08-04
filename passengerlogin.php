<?php
// Start the session
session_start();

// Database connection details
$host = 'localhost';
$dbname = 'airline_db';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password

// Connect to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pnr = $_POST['pnr'];
    $lastName = $_POST['lastName'];

    // Query to check if PNR and last name match
    $sql = "SELECT b.pnr, p.last_name, f.departure_date, f.departure_time, f.departure_place, f.gate_number
            FROM bookings b
            JOIN passengers p ON b.passenger_id = p.passenger_id
            JOIN flights f ON b.flight_id = f.flight_id
            WHERE b.pnr = :pnr AND (p.last_name = :lastName OR p.email = :lastName)";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['pnr' => $pnr, 'lastName' => $lastName]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Store the result in the session and redirect to passengerdetails.php
        $_SESSION['passenger_details'] = $result;
        header("Location: passengerdetails.php");
        exit();
    } else {
        // Show an alert and stay on the same page
        echo "<script>alert('Invalid PNR or Last Name. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Passenger Login</title>
    <style>
        /* Your existing CSS styles */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://www.concentric.io/wp-content/uploads/2023/08/Untitled-design-35-1200x600-1.jpg');
            background-size: cover;
            background-position: center;
            z-index: -1;
            opacity: 0.7;
        }

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
            cursor: pointer;
        }

        .navbar-right a:hover {
            color: #28A745;
        }

        .container {
            position: relative;
            z-index: 1;
            width: 450px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            text-align: center;
            margin-top: 80px;
        }

        h1 {
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px;
            background-color: #28A745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="background-image"></div>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="navbar-left">
            <img src="logo.ico" alt="Flight Logo">
            <h2>Flight Board</h2>
        </div>
        <div class="navbar-right">
            <a href="mainpage.php">Back to Login</a>
            <a href="about.php">About</a>
        </div>
    </div>

    <!-- Login Form -->
    <div class="container">
        <h1>Flight Passenger Login</h1>
        <form method="POST" action="passengerlogin.php">
            <label for="pnr">PNR Number:</label>
            <input type="text" id="pnr" name="pnr" placeholder="PNR/Booking Reference" required>
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
            <button type="submit">Get Details</button>
        </form>
    </div>
</body>
</html>