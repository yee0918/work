<?php
// 資料庫連接設定
$servername = "192.168.195.20"; // 資料庫伺服器地址
$username = "everyone";        // 資料庫用戶名
$password = "12345678";        // 資料庫密碼
$dbname = "專題";          // 資料庫名稱

// 建立資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 取得從表單提交的資料
    $event_name = trim($_POST['event_name']);
    $description = trim($_POST['description']);
    $event_date = trim($_POST['event_date']);
    $event_time = trim($_POST['event_time']);
    $location = trim($_POST['location']);
    $fee_m = trim($_POST['fee_m']);
    $fee_nm = trim($_POST['fee_nm']);
    $link = trim($_POST['registration_link']);
    
    // 檢查檔案是否上傳
    if (isset($_FILES['post_url']) && $_FILES['post_url']['error'] == 0) {
        $upload_dir = "uploads/"; // 上傳目錄
        $file_name = basename($_FILES['post_url']['name']);
        $target_file = $upload_dir . $file_name;

        // 檢查檔案是否是有效的圖片（根據檔案類型過濾，根據需要修改）
        $file_type = mime_content_type($_FILES['post_url']['tmp_name']);
        if (!in_array($file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
            die("請上傳有效的圖片檔案。");
        }

        // 移動檔案到指定目錄
        if (!move_uploaded_file($_FILES['post_url']['tmp_name'], $target_file)) {
            die("檔案上傳失敗。");
        }

        // 檔案上傳成功，保存檔案的路徑
        $post_url = $target_file;
    } else {
        die("請上傳檔案。");
    }

    // 基本資料驗證
    if (empty($event_name) || empty($event_date) || empty($event_time)) {
        die("請填寫所有必填欄位。");
    }

    // 確保日期格式正確
    $date_pattern = "/^\d{4}-\d{2}-\d{2}$/";
    if (!preg_match($date_pattern, $event_date)) {
        die("活動日期格式錯誤，請使用 YYYY-MM-DD 格式。");
    }

    // 確保時間格式正確
    $time_pattern = "/^\d{2}:\d{2}$/";
    if (!preg_match($time_pattern, $event_time)) {
        die("活動時間格式錯誤，請使用 HH:MM 格式。");
    }

    // 預處理 SQL 查詢，防止 SQL 注入
    $sql = "INSERT INTO events2 (event_name, description, event_date, event_time, location, fee_m, fee_nm, post_url, registration_link)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // 綁定參數
    $stmt->bind_param("sssssssss", $event_name, $description, $event_date, $event_time, $location, $fee_m, $fee_nm, $post_url, $link);

    // 執行 SQL 語句並檢查結果
    if ($stmt->execute()) {
        echo "活動新增成功！";
        header("Location: index.php");
    } else {
        echo "錯誤: " . $stmt->error;
    }

    // 關閉預處理語句和資料庫連線
    $stmt->close();
}

// 關閉資料庫連線
$conn->close();
?>
