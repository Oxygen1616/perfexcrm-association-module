<?php
echo "<h1>Final Verification of All Fixes</h1>";

// Test 1: Check if photo column exists
echo "<h2>1. Photo Column Verification</h2>";
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'perfex_crm';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SHOW COLUMNS FROM tblmembership_nominations LIKE 'photo'");
if ($result->num_rows > 0) {
    echo "<p style='color:green'>✓ Photo column EXISTS in tblmembership_nominations</p>";
} else {
    echo "<p style='color:red'>✗ Photo column MISSING from tblmembership_nominations</p>";
    echo "<p>Adding photo column now...</p>";
    $sql = "ALTER TABLE tblmembership_nominations ADD COLUMN photo varchar(255) DEFAULT NULL AFTER symbol_id";
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green'>✓ Photo column added successfully!</p>";
    } else {
        echo "<p style='color:red'>✗ Error adding photo column: " . $conn->error . "</p>";
    }
}

// Test 2: Check vote.php fix
echo "<h2>2. Vote Dropdown Fix Verification</h2>";
$voteFile = file_get_contents('modules/membership/views/public/vote.php');
if (strpos($voteFile, 'membership_election_untitled') !== false &&
    strpos($voteFile, 'isset($election[\"title\"]) && $election[\"title\"] !== \"\"') !== false) {
    echo "<p style='color:green'>✓ Vote dropdown properly handles missing titles</p>";
} else {
    echo "<p style='color:red'>✗ Vote dropdown fix not found</p>";
}

// Test 3: Check dashboard.php fix
echo "<h2>3. Dashboard Money Formatting Verification</h2>";
$dashboardFile = file_get_contents('modules/membership/views/public/dashboard.php');
if (strpos($dashboardFile, 'app_format_money($invoice[\"total\"], get_base_currency())') !== false) {
    echo "<p style='color:green'>✓ Dashboard money formatting is correct</p>";
} else {
    echo "<p style='color:red'>✗ Dashboard money formatting issue</p>";
}

// Test 4: Check position methods
echo "<h2>4. Position Methods Verification</h2>";
$modelFile = file_get_contents('modules/membership/models/Membership_model.php');
if (strpos($modelFile, 'public function create_position') !== false &&
    strpos($modelFile, 'public function update_position') !== false) {
    echo "<p style='color:green'>✓ Position methods exist</p>";
} else {
    echo "<p style='color:red'>✗ Position methods missing</p>";
}

// Test 5: Check vote history
echo "<h2>5. Vote History Implementation Verification</h2>";
$clientFile = file_get_contents('modules/membership/controllers/Client.php');
$voteHistoryView = file_get_contents('modules/membership/views/public/vote_history.php');
$langFile = file_get_contents('modules/membership/language/english/membership_lang.php');

if (strpos($clientFile, 'public function vote_history') !== false) {
    echo "<p style='color:green'>✓ vote_history() method in Client.php</p>";
} else {
    echo "<p style='color:red'>✗ vote_history() method missing</p>";
}

if (file_exists('modules/membership/views/public/vote_history.php') &&
    strpos($voteHistoryView, 'membership_vote_history') !== false) {
    echo "<p style='color:green'>✓ vote_history.php view exists</p>";
} else {
    echo "<p style='color:red'>✗ vote_history.php view missing</p>";
}

if (strpos($langFile, 'membership_vote_history') !== false) {
    echo "<p style='color:green'>✓ membership_vote_history language key exists</p>";
} else {
    echo "<p style='color:red'>✗ membership_vote_history language key missing</p>";
}

// Test 6: Check notices fix
echo "<h2>6. Notices Fix Verification</h2>";
$noticesFile = file_get_contents('modules/membership/views/admin/notices.php');
if (strpos($noticesFile, 'data-toggle=\"modal\" data-target=\"#noticeModal\"') !== false) {
    echo "<p style='color:green'>✓ Notices button properly triggers modal</p>";
} else {
    echo "<p style='color:red'>✗ Notices button fix not found</p>";
}

$conn->close();
echo "<h2>Verification Complete</h2>";
?>