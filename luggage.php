<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: staff.php");
    exit();
}

$message = ""; // Variable to store success/error messages

// Database connection
$host = 'localhost';
$dbname = 'airline_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $baggageId = $_POST['baggageId'];
    $holdReason = $_POST['holdReason'];

    // Update luggage status in the database
    $sql = "UPDATE luggage SET status = 'On Hold', hold_reason = :holdReason WHERE luggage_id = :baggageId";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['holdReason' => $holdReason, 'baggageId' => $baggageId]);

    if ($stmt->rowCount() > 0) {
        $message = "Luggage status updated successfully.";
    } else {
        $message = "Failed to update luggage status. Please check the Baggage ID.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luggage Hold Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('luggage.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }

        h2 {
            margin-bottom: 15px;
        }

        label {
            font-size: 1rem;
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 15px;
            font-size: 1.1rem;
            font-weight: bold;
            display: none;
        }

        .hold-reason { color: orange; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Luggage Hold Notification</h2>

        <form method="POST" action="luggage.php">
            <label for="baggageId">Baggage ID:</label>
            <input type="text" id="baggageId" name="baggageId" placeholder="Enter Baggage ID" required>

            <label for="holdReason">Reason for Hold:</label>
            <select id="holdReason" name="holdReason">
                <option value="Security Check Required">Security Check Required</option>
                <option value="Oversized Baggage">Oversized Baggage</option>
                <option value="Weight Exceeded Limit">Weight Exceeded Limit</option>
                <option value="Prohibited Items Detected">Prohibited Items Detected</option>
                <option value="Improper Tagging">Improper Tagging</option>
                <option value="Customs Clearance Issue">Customs Clearance Issue</option>
                <option value="Delayed Transfer">Delayed Transfer</option>
            </select>

            <button type="submit">Notify Passenger</button>
        </form>

        <?php if (!empty($message)): ?>
            <p id="notificationMessage" class="message hold-reason"><?php echo htmlspecialchars($message); ?></p>
            <script>
                // Show the message dynamically
                document.getElementById('notificationMessage').style.display = 'block';
            </script>
        <?php endif; ?>
    </div>
</body>
</html>