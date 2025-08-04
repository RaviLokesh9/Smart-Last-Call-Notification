<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: staff.php");
    exit();
}

$message = "";       // Variable to store success/error messages
$debugOutput = "";   // Variable to store debug output from the Python script

// Database connection details
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

// If a Flight ID is provided for fetching details, retrieve flight info
$flightDetails = [];
if (!empty($_POST['fetchFlightId'])) {
    $fetchFlightId = $_POST['fetchFlightId'];
    $stmt = $conn->prepare("SELECT * FROM flights WHERE flight_id = :flightId");
    $stmt->execute(['flightId' => $fetchFlightId]);
    $flightDetails = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if "Final Call" button was clicked
    if (isset($_POST['finalCall']) && $_POST['finalCall'] === 'true') {
        // Adjust the path to your Python interpreter and democall.py script as needed
        $command = "/usr/bin/python3 /Applications/XAMPP/xamppfiles/htdocs/my_project/democall.py 2>&1";
        exec($command, $output, $return_var);
        $debugOutput = implode("\n", $output);
        if ($return_var === 0) {
            $message = "Final call made successfully. Debug Info: " . $debugOutput;
        } else {
            $message = "Failed to make final call. Debug Info: " . $debugOutput;
        }
    } elseif (isset($_POST['flightId'])) {
        // Otherwise, update flight details
        $flightId = $_POST['flightId'];
        $departureTime = $_POST['departureTime'];
        $gateNumber = $_POST['gateNumber'];
        $flightStatus = $_POST['flightStatus'];
        
        $sql = "UPDATE flights SET departure_time = :departureTime, gate_number = :gateNumber, flight_status = :flightStatus WHERE flight_id = :flightId";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'departureTime' => $departureTime,
            'gateNumber'    => $gateNumber,
            'flightStatus'  => $flightStatus,
            'flightId'      => $flightId
        ]);
        
        if ($stmt->rowCount() > 0) {
            $message = "Flight details updated successfully.";
        } else {
            $message = "Failed to update flight details.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flight Information</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-image: url('flight2.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      margin: 0;
    }
    .flight-info-container {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
      padding: 20px 30px;
      width: 400px;
      text-align: center;
    }
    h2 {
      margin-bottom: 20px;
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
    }
  </style>
</head>
<body>
  <div class="flight-info-container">
    <h2>Flight Departure Information</h2>

    <!-- Form to fetch flight details -->
    <form method="POST" action="flightdetails.php">
      <label for="fetchFlightId">Enter Flight ID:</label>
      <input type="text" id="fetchFlightId" name="fetchFlightId" placeholder="Enter Flight ID" required>
      <button type="submit">Fetch Flight Details</button>
    </form>

    <!-- If flight details are available, display the edit/update form -->
    <?php if ($flightDetails): ?>
      <form id="flightForm" method="POST" action="flightdetails.php">
        <!-- Flight ID (hidden) -->
        <input type="hidden" name="flightId" value="<?php echo htmlspecialchars($flightDetails['flight_id']); ?>">

        <label for="departureTime">Departure Time:</label>
        <input type="time" id="departureTime" name="departureTime" value="<?php echo htmlspecialchars($flightDetails['departure_time']); ?>" disabled>

        <label for="gateNumber">Gate Number:</label>
        <input type="text" id="gateNumber" name="gateNumber" value="<?php echo htmlspecialchars($flightDetails['gate_number']); ?>" disabled>

        <label for="flightStatusSelect">Flight Status:</label>
        <select id="flightStatusSelect" disabled>
          <option value="Scheduled" <?php echo ($flightDetails['flight_status'] === 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
          <option value="Boarding" <?php echo ($flightDetails['flight_status'] === 'Boarding') ? 'selected' : ''; ?>>Boarding</option>
          <option value="Gate Closed" <?php echo ($flightDetails['flight_status'] === 'Gate Closed') ? 'selected' : ''; ?>>Gate Closed</option>
        </select>

        <!-- Hidden input to store the flight status -->
        <input type="hidden" id="flightStatus" name="flightStatus" value="<?php echo htmlspecialchars($flightDetails['flight_status']); ?>">

        <button type="button" onclick="enableEditing()">Edit</button>
        <button type="button" onclick="submitNormal()">Update Flight Details</button>
        <button type="button" onclick="submitFinalCall()">Final Call</button>
      </form>
    <?php endif; ?>

    <!-- Display success/error messages -->
    <?php if (!empty($message)): ?>
      <p id="notificationMessage" class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
  </div>

  <script>
    function enableEditing() {
      document.getElementById('departureTime').disabled = false;
      document.getElementById('gateNumber').disabled = false;
      document.getElementById('flightStatusSelect').disabled = false;
    }
    function submitNormal() {
      // Set the hidden flightStatus field from the dropdown selection
      var select = document.getElementById('flightStatusSelect');
      document.getElementById('flightStatus').value = select.value;
      document.getElementById('flightForm').submit();
    }
    function submitFinalCall() {
    // Set flightStatus to "Final Call" and submit the form
    document.getElementById('flightStatus').value = 'Final Call';

    // Submit the form to a PHP file that will handle calling the Python script
    var form = document.getElementById('flightForm');
    var formData = new FormData(form);
    
    // Make AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "call_final.php", true);  // Send request to call_final.php
    xhr.onload = function () {
        if (xhr.status == 200) {
            alert("Final call made successfully!");
            form.submit();  // Continue with form submission after successful call
        } else {
            alert("Failed to make final call.");
        }
    };
    xhr.send(formData);
}

  </script>
</body>
</html>
