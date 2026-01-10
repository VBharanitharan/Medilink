<?php
require_once __DIR__ . '/app.php';

// Simple verification email using mail()
// On localhost it usually won't actually send, but the code won't error.
function sendVerificationEmail(string $toEmail, string $name, string $token): bool {
    $verifyUrl = BASE_URL . '/verify.php?email=' . urlencode($toEmail) . '&token=' . urlencode($token);
    $subject = 'MediLink - Verify your email';
    $message = "Hello {$name},\n\n"
        . "Thank you for registering on MediLink.\n\n"
        . "Please click the link below to verify your email address:\n\n"
        . $verifyUrl . "\n\n"
        . "If you did not create this account, you can ignore this email.\n\n"
        . "Regards,\nMediLink Team";
    $headers = 'From: no-reply@medilink.local' . "\r\n" .
               'Reply-To: no-reply@medilink.local' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();
    return mail($toEmail, $subject, $message, $headers);
}
?>