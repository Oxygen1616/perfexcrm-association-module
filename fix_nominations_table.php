<?php
// Fix the missing photo column in tblmembership_nominations
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'perfex_crm';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>Fixing Nominations Table</h1>";

// Check current table structure
$cols = $conn->query("SHOW COLUMNS FROM tblmembership_nominations");
$columns = [];
while ($col = $cols->fetch_assoc()) {
    $columns[] = $col['Field'];
}

echo "<h2>Current Columns:</h2>";
echo "<pre>";
print_r($columns);
echo "</pre>";

// Check if photo column exists
if (!in_array('photo', $columns)) {
    echo "<h2>Photo column missing - adding it now...</h2>";

    // Add the photo column
    $sql = "ALTER TABLE tblmembership_nominations ADD COLUMN photo varchar(255) DEFAULT NULL AFTER symbol_id";
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green'>Photo column added successfully!</p>";
    } else {
        echo "<p style='color:red'>Error adding photo column: " . $conn->error . "</p>";
    }
} else {
    echo "<h2>Photo column already exists</h2>";
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