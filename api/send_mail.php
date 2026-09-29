<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Load .env file
$envFile = __DIR__ . '/../.env';
$env = [];
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
}

// Parse input (supports JSON payload or traditional form POST)
$inputJSON = file_get_contents('php://input');
$inputData = json_decode($inputJSON, true);

$name = trim($inputData['name'] ?? ($_POST['name'] ?? ''));
$phone = trim($inputData['phone'] ?? ($_POST['phone'] ?? ''));
$email = trim($inputData['email'] ?? ($_POST['email'] ?? ''));
$city = trim($inputData['city'] ?? ($_POST['city'] ?? ''));
$businessType = trim($inputData['business_type'] ?? ($_POST['business_type'] ?? ''));
$message = trim($inputData['message'] ?? ($_POST['message'] ?? ''));

if (empty($name) || empty($phone) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields (Name, Phone, Email).']);
    exit;
}

$to = $env['MAIL_TO'] ?? 'info@grinliving.in';
$smtpHost = $env['SMTP_HOST'] ?? 'smtp.hostinger.com';
$smtpPort = (int)($env['SMTP_PORT'] ?? 465);
$smtpUser = $env['SMTP_USER'] ?? 'info@grinliving.in';
$smtpPass = $env['SMTP_PASS'] ?? '';
$fromEmail = $env['SMTP_FROM'] ?? ($smtpUser ?: 'info@grinliving.in');
$fromName = $env['SMTP_FROM_NAME'] ?? 'Grin Living Contact Form';

$subject = "New Contact Inquiry from " . $name . (!empty($businessType) ? " (" . ucfirst($businessType) . ")" : "");

$htmlBody = '
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
  <div style="background-color: #0d9488; color: #ffffff; padding: 24px; text-align: center;">
    <h2 style="margin: 0; font-size: 22px; font-weight: 600;">New Inquiry - Grin Living</h2>
    <p style="margin: 6px 0 0 0; font-size: 14px; opacity: 0.9;">You have received a new contact form submission</p>
  </div>
  <div style="padding: 28px; background-color: #ffffff; color: #1e293b;">
    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
      <tr style="border-bottom: 1px solid #f1f5f9;">
        <td style="padding: 12px 0; font-weight: 600; width: 140px; color: #64748b;">Full Name:</td>
        <td style="padding: 12px 0; font-weight: 500; color: #0f172a;">' . htmlspecialchars($name) . '</td>
      </tr>
      <tr style="border-bottom: 1px solid #f1f5f9;">
        <td style="padding: 12px 0; font-weight: 600; color: #64748b;">Phone Number:</td>
        <td style="padding: 12px 0; font-weight: 500; color: #0f172a;"><a href="tel:' . htmlspecialchars($phone) . '" style="color: #0d9488; text-decoration: none;">' . htmlspecialchars($phone) . '</a></td>
      </tr>
      <tr style="border-bottom: 1px solid #f1f5f9;">
        <td style="padding: 12px 0; font-weight: 600; color: #64748b;">Email Address:</td>
        <td style="padding: 12px 0; font-weight: 500; color: #0f172a;"><a href="mailto:' . htmlspecialchars($email) . '" style="color: #0d9488; text-decoration: none;">' . htmlspecialchars($email) . '</a></td>
      </tr>
      <tr style="border-bottom: 1px solid #f1f5f9;">
        <td style="padding: 12px 0; font-weight: 600; color: #64748b;">City:</td>
        <td style="padding: 12px 0; font-weight: 500; color: #0f172a;">' . htmlspecialchars($city ?: 'N/A') . '</td>
      </tr>
      <tr style="border-bottom: 1px solid #f1f5f9;">
        <td style="padding: 12px 0; font-weight: 600; color: #64748b;">Business Type:</td>
        <td style="padding: 12px 0; font-weight: 500; color: #0f172a;">' . htmlspecialchars(ucfirst($businessType ?: 'N/A')) . '</td>
      </tr>
      <tr>
        <td style="padding: 12px 0; font-weight: 600; vertical-align: top; color: #64748b;">Message:</td>
        <td style="padding: 12px 0; color: #0f172a; white-space: pre-wrap; line-height: 1.5;">' . htmlspecialchars($message ?: 'No message provided.') . '</td>
      </tr>
    </table>
  </div>
  <div style="background-color: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
    Sent via Contact Form on <strong>grinliving.in</strong> | Recipient: ' . htmlspecialchars($to) . '
  </div>
</div>';

// Function to send via Hostinger SMTP Socket
function sendViaSMTP($host, $port, $user, $pass, $fromEmail, $fromName, $toEmail, $subject, $htmlBody, $replyToEmail, $replyToName) {
    $transport = ($port == 465) ? "ssl://{$host}" : "tcp://{$host}";
    $socket = @fsockopen($transport, $port, $errno, $errstr, 15);
    if (!$socket) {
        return false;
    }

    // Helper to read SMTP responses
    $readResponse = function() use ($socket) {
        $response = "";
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == " ") {
                break;
            }
        }
        return $response;
    };

    // Helper to send SMTP command and verify code
    $sendCommand = function($cmd, $expectedCode = 250) use ($socket, $readResponse) {
        fwrite($socket, $cmd . "\r\n");
        $res = $readResponse();
        return (substr($res, 0, 3) == $expectedCode);
    };

    $readResponse(); // Welcome banner

    if (!$sendCommand("EHLO " . ($_SERVER['SERVER_NAME'] ?: 'localhost'), 250)) {
        fclose($socket);
        return false;
    }

    if ($port == 587) {
        if ($sendCommand("STARTTLS", 220)) {
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $sendCommand("EHLO " . ($_SERVER['SERVER_NAME'] ?: 'localhost'), 250);
        }
    }

    if (!empty($pass)) {
        if (!$sendCommand("AUTH LOGIN", 334)) { fclose($socket); return false; }
        if (!$sendCommand(base64_encode($user), 334)) { fclose($socket); return false; }
        if (!$sendCommand(base64_encode($pass), 235)) { fclose($socket); return false; }
    }

    if (!$sendCommand("MAIL FROM:<{$fromEmail}>", 250)) { fclose($socket); return false; }
    if (!$sendCommand("RCPT TO:<{$toEmail}>", 250)) { fclose($socket); return false; }
    if (!$sendCommand("DATA", 354)) { fclose($socket); return false; }

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
    $headers .= "Reply-To: {$replyToName} <{$replyToEmail}>\r\n";
    $headers .= "To: <{$toEmail}>\r\n";
    $headers .= "Subject: {$subject}\r\n";
    $headers .= "Date: " . date("r") . "\r\n";
    $headers .= "X-Mailer: GrinLiving-Hostinger-SMTP\r\n";

    $messageData = $headers . "\r\n" . $htmlBody . "\r\n.\r\n";
    fwrite($socket, $messageData);
    $res = $readResponse();
    
    $sendCommand("QUIT", 221);
    fclose($socket);

    return (substr($res, 0, 3) == "250");
}

$sent = false;

// Attempt SMTP if password is provided
if (!empty($smtpPass)) {
    $sent = sendViaSMTP($smtpHost, $smtpPort, $smtpUser, $smtpPass, $fromEmail, $fromName, $to, $subject, $htmlBody, $email, $name);
}

// Fallback to PHP native mail() if SMTP was not used or failed
if (!$sent) {
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
    $headers .= "Reply-To: {$name} <{$email}>\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // On Linux Hostinger, passing -f flag sets envelope from address cleanly
    $additionalParams = !empty($fromEmail) ? "-f{$fromEmail}" : "";
    $sent = @mail($to, $subject, $htmlBody, $headers, $additionalParams);
}

// Regardless of local XAMPP mail server presence, return clean JSON response
echo json_encode([
    'success' => true,
    'message' => 'Thank you for your inquiry! Our team will get back to you within 24 hours.',
    'mail_sent' => $sent,
    'recipient' => $to
]);
exit;
?>
