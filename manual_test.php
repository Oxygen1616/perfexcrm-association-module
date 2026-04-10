<?php
// Manual test to check database connectivity and query results
// This bypasses CodeIgniter to test the raw database queries

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'perfex_crm';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>Manual Database Test</h1>";

// Test 1: Check if we can query the elections table
echo "<h2>Test 1: Basic table query</h2>";
$result = $conn->query("SELECT COUNT(*) as count FROM tblmembership_elections");
$row = $result->fetch_assoc();
echo "<p>Total elections in table: " . $row['count'] . "</p>";

// Test 2: Check elections with status = 'active'
echo "<h2>Test 2: Elections with status = 'active'</h2>";
$result = $conn->query("SELECT id, title, status FROM tblmembership_elections WHERE status = 'active'");
echo "<p>Active elections count: " . $result->num_rows . "</p>";
if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test 3: Check currently active elections (date-based)
echo "<h2>Test 3: Currently active elections (date-based)</h2>";
$now = date('Y-m-d H:i:s');
$result = $conn->query("SELECT id, title, status, start_date, end_date FROM tblmembership_elections WHERE status = 'active' AND start_date <= '$now' AND end_date >= '$now'");
echo "<p>Currently active elections count: " . $result->num_rows . "</p>";
if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Status</th><th>Start Date</th><th>End Date</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['end_date']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test 4: Check what the get_elections function in the model would return
// This simulates: $this->db->where('status', $status)->order_by('start_date', 'DESC')->get($this->table_elections)->result_array();
echo "<h2>Test 4: Simulating get_elections('active')</h2>";
$result = $conn->query("SELECT * FROM tblmembership_elections WHERE status = 'active' ORDER BY start_date DESC");
echo "<p>Simulated get_elections('active') count: " . $result->num_rows . "</p>";
if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Status</th><th>Start Date</th><th>End Date</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['end_date']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test 5: Check if there are any candidates for these elections
echo "<h2>Test 5: Checking for candidates</h2>";
if ($result->num_rows > 0) {
    // Reset result pointer
    $result->data_seek(0);
    while ($election = $result->fetch_assoc()) {
        $election_id = $election['id'];
        $candidate_result = $conn->query("SELECT COUNT(*) as count FROM tblmembership_candidates WHERE election_id = $election_id");
        $candidate_row = $candidate_result->fetch_assoc();
        echo "<p>Election ID " . $election_id . " (" . htmlspecialchars($election['title']) . ") has " . $candidate_row['count'] . " candidates</p>";
    }
}

$conn->close();
?>