<?php
$servername = "192.168.195.20";
$username = "everyone";
$password = "12345678";
$dbname = "專題";

// 連接資料庫
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 查詢本學期活動，按照 id 或時間排序 (最新的在最上方)
$sql = "SELECT event_name, registration_link FROM Events2 ORDER BY event_date DESC LIMIT 3";
$result = $conn->query($sql);

$activities = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $activities[] = '<a href="' . $row['registration_link'] . '" target="_blank">' . $row['event_name'] . '</a>';
        
    }
}
$conn->close();

header('Content-Type: application/json');
echo json_encode($activities);
?>
