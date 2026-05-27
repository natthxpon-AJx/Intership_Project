<?php
// ไฟล์: db.php
$host = 'localhost';
$dbname = 'Internship_Project'; // เปลี่ยนชื่อฐานข้อมูลเป็น Internship_Project
$username = 'root'; // ค่าเริ่มต้นของ XAMPP
$password = '';     // ค่าเริ่มต้นของ XAMPP มักจะไม่มีรหัสผ่าน

try {
    // เชื่อมต่อฐานข้อมูลด้วย PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // ตั้งค่าให้แจ้งเตือนเมื่อเกิด Error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ทดสอบการเชื่อมต่อ (เอาคอมเมนต์ออกเพื่อทดสอบ)
    // echo "เชื่อมต่อฐานข้อมูล Internship_Project สำเร็จ"; 
} catch(PDOException $e) {
    echo "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
    exit;
}
?>
