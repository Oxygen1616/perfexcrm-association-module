<?php
// Verify and fix photo column in tblmembership_nominations
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'perfex_crm';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if photo column exists
$check = $conn->query("SHOW COLUMNS FROM tblmembership_nominations LIKE 'photo'");
$has_photo = $check->num_rows > 0;

if (!$has_photo) {
    echo "Photo column missing - adding it now...<br>";

    // Add the photo column
    $sql = "ALTER TABLE tblmembership_nominations ADD COLUMN photo varchar(255) DEFAULT NULL AFTER symbol_id";
    if ($conn->query($sql) === TRUE) {
        echo "Photo column added successfully!<br>";
    } else {
        echo "Error adding photo column: " . $conn->error . "<br>";
    }
} else {
    echo "Photo column already exists<br>";
}

// Verify the fix
echo "<h2>Updated Table Structure:</h2>";
$cols = $conn->query("SHOW COLUMNS FROM tblmembership_nominations");
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($col = $cols->fetch_assoc()) {
    echo "<tr>";
    foreach($col as $val) {
        echo "<td>" . htmlspecialchars($val) . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>