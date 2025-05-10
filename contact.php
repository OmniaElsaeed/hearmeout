<?php
header('Content-Type: application/json');

// إعدادات اتصال قاعدة البيانات
$host = "localhost";
$username = "root";
$password = "";
$dbname = "hearmrout_db";

try {
    // إنشاء اتصال PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // التحقق من أن الطريقة POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method Not Allowed");
    }

    // استقبال وتنظيف البيانات
    $fullName = htmlspecialchars($_POST['fullName'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST['message'] ?? '');

    // التحقق من البيانات المطلوبة
    if (empty($fullName) || empty($email) || empty($message)) {
        throw new Exception("All fields are required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    // إعداد استعلام الإدراج
    $stmt = $conn->prepare("INSERT INTO contacts (full_name, email, message) VALUES (:fullName, :email, :message)");
    
    // ربط القيم
    $stmt->bindParam(':fullName', $fullName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':message', $message);
    
    // تنفيذ الإدراج
    $stmt->execute();

    // إرجاع رسالة نجاح
    echo json_encode([
        'success' => true,
        'message' => 'تم حفظ البيانات بنجاح في قاعدة البيانات'
    ]);

} catch (Exception $e) {
    // في حالة حدوث خطأ
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    // إغلاق الاتصال
    $conn = null;
}
?>