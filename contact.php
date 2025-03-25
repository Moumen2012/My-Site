<?php
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$mail = new PHPMailer(true);

// تفعيل وضع التصحيح
$mail->SMTPDebug = 3; // (0 لإيقاف، 3 لرؤية كل التفاصيل)
$mail->Debugoutput = function($str, $level) {
    file_put_contents('debug.log', date('Y-m-d H:i:s')." - $level - $str\n", FILE_APPEND);
};

try {
    // إعدادات SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'bramoumen6202@gmail.com';
    $mail->Password = 'كلمة_مرور_التطبيق_الخاصة_بك'; // استبدلها بالكلمة التي حصلت عليها
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    // التحقق من المدخلات
    if(empty($_POST['name']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('بيانات غير صالحة');
    }

    // محتوى الإيميل
    $mail->setFrom($_POST['email'], $_POST['name']);
    $mail->addAddress('bramoumen6202@gmail.com', 'Moumen');
    $mail->addReplyTo($_POST['email'], $_POST['name']);
    $mail->Subject = 'رسالة جديدة من ' . $_POST['name'];
    $mail->Body = "
        <h3>تفاصيل الرسالة</h3>
        <p><strong>الاسم:</strong> {$_POST['name']}</p>
        <p><strong>البريد:</strong> {$_POST['email']}</p>
        <p><strong>الرسالة:</strong><br>{$_POST['message']}</p>
    ";
    $mail->AltBody = strip_tags($_POST['message']); // نسخة نصية عادية

    // الإرسال
    if(!$mail->send()) {
        throw new Exception('لم يتم الإرسال');
    }
    
    echo 'تم الإرسال بنجاح! سيتم الرد عليك خلال 24 ساعة';
    
} catch (Exception $e) {
    // تسجيل الخطأ في ملف
    error_log('Error: ' . $e->getMessage());
    
    // رسالة آمنة للمستخدم
    echo 'عذراً، حدث خطأ تقني. يرجى المحاولة لاحقاً أو مراسلتنا مباشرة على bramoumen6202@gmail.com';
    
    // (اختياري) إرسال تنبيه لك بالخطأ
    $admin_mail = new PHPMailer(true);
    $admin_mail->setFrom('noreply@yourdomain.com', 'System Alert');
    $admin_mail->addAddress('bramoumen6202@gmail.com');
    $admin_mail->Subject = 'فشل إرسال رسالة موقع';
    $admin_mail->Body = "تفاصيل الخطأ: " . $e->getMessage();
    @$admin_mail->send(); // @ لتجنب أخطاء داخل الأخطاء
}
?>