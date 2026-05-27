<?php
// เริ่มต้นใช้งาน Session
session_start();

// ล้างค่าข้อมูล Session ทั้งหมด
$_SESSION = array();

// หากมีการใช้งานคุกกี้ Session ให้ลบออกด้วย
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ทำลาย Session บนเซิร์ฟเวอร์
session_destroy();

// ส่งตัวผู้ใช้กลับไปยังหน้า login.php อย่างปลอดภัย
header("Location: login.php");
exit;
?>