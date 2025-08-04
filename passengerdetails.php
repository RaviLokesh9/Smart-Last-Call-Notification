<?php
// Start the session
session_start();

// Check if passenger details are available in the session
if (!isset($_SESSION['passenger_details'])) {
    // Redirect to the login page if no details are found
    header("Location: passengerlogin.php");
    exit();
}

// Get passenger details from the session
$details = $_SESSION['passenger_details'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Details</title>
    <style>
        /* Reuse the same styles as passengerlogin.php */
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

        p {
            font-size: 18px;
            margin: 10px 0;
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

    <!-- Passenger Details -->
    <div class="container">
        <h1>Passenger Details</h1>
        <div id="details">
            <p><strong>PNR:</strong> <?php echo htmlspecialchars($details['pnr']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($details['last_name']); ?></p>
            <p><strong>Departure Date:</strong> <?php echo htmlspecialchars($details['departure_date']); ?></p>
            <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($details['departure_time']); ?></p>
            <p><strong>Departure Place:</strong> <?php echo htmlspecialchars($details['departure_place']); ?></p>
            <p><strong>Gate Number:</strong> <?php echo htmlspecialchars($details['gate_number']); ?></p>
        </div>
    </div>
</body>
</html>