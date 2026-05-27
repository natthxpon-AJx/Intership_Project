<?php
// ไฟล์: register_process.php
require 'db.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // เข้ารหัสรหัสผ่าน
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // เตรียมคำสั่ง SQL บันทึกข้อมูล
        $sql = "INSERT INTO users (username, password_hash, first_name, last_name, role, status) 
                VALUES (:username, :password_hash, :first_name, :last_name, 'user', 'active')";
        
        $stmt = $conn->prepare($sql);
        
        // ผูกค่าตัวแปรและทำงาน
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        
        if ($stmt->execute()) {
            // แก้ไขจุดนี้: เปลี่ยนปลายทางให้ตรงกับชื่อไฟล์ที่คุณตั้งไว้ (Login.php)
            echo "<script>
                    alert('ลงทะเบียนสำเร็จ! เข้าสู่ระบบได้เลย');
                    window.location.href = 'Login.php'; 
                  </script>";
        }
    } catch(PDOException $e) {
        // กรณี Username ซ้ำ
        if ($e->getCode() == 23000) {
            echo "<script>
                    alert('ข้อผิดพลาด: มีชื่อผู้ใช้งาน (Username) นี้ในระบบแล้ว');
                    window.history.back();
                  </script>";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>