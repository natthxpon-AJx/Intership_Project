<?php
// ไฟล์: login_process.php
session_start();
require 'db.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล Internship_Project

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header("Location: login.php?error=empty");
        exit;
    }

    try {
        // ค้นหาบัญชีผู้ใช้ในฐานข้อมูล Internship_Project
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ตรวจสอบรหัสผ่านที่แฮชไว้ว่าตรงกับรหัสผ่านที่ส่งมาหรือไม่
            if (password_verify($password, $user['password_hash'])) {
                
                // ตรวจสอบว่าบัญชีโดนระงับใช้งานหรือไม่
                if ($user['status'] == 'inactive') {
                    header("Location: login.php?error=suspended");
                    exit;
                }

                // หากสำเร็จให้บันทึกสถานะผู้ใช้ลงใน Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['role'] = $user['role'];

                // พาผู้ใช้ที่ผ่านเข้าสู่ระบบ ไปยังหน้าหลัก (account.php)
                header("Location: account.php");
                exit;
            }
        }
        
        // หากกรอกชื่อผู้ใช้หรือรหัสผ่านผิดพลาด
        header("Location: login.php?error=invalid");
        exit;

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>