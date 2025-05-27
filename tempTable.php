<?php
// Database credentials
$servername = "localhost";
$username = "ben";
$password = "password";
$dbname = "ben";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to select data
$sql = "SELECT temp1, temp2, temp3, timedate FROM temp2 ORDER BY id DESC";
$result = $conn->query($sql);

// Output the full HTML page using echo
echo '<!DOCTYPE html>';
echo '<html>';
echo '<head>';
echo '<title>Temperature Log</title>';
echo '<style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        table {
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
      </style>';
echo '</head>';
echo '<body>';

echo '<h2>Temperature Data</h2>';

echo '<table>';
echo '<tr>
        <th>Temperature 1 (Â°C)</th>
        <th>Temperature 2 (Â°C)</th>
        <th>Temperature 3 (Â°C)</th>
        <th>Timestamp</th>
      </tr>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['temp1']}</td>
                <td>{$row['temp2']}</td>
                <td>{$row['temp3']}</td>
                <td>{$row['timedate']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No data available</td></tr>";
}

echo '</table>';
echo '</body>';
echo '</html>';

$conn->close();
?>
