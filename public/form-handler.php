<?php

$receiving_email = 'jacobhandw@gmail.com';
$subject_prefix  = '[Livana Soul Doula]';

$allowed_origins = [
    'https://livanasouldoula.com',
    'https://www.livanasouldoula.com',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: $origin");
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$name         = trim($input['name'] ?? '');
$email        = trim($input['email'] ?? '');
$phone        = trim($input['phone'] ?? '');
$consultation = trim($input['consultation'] ?? '');
$message      = trim($input['message'] ?? '');

$errors = [];
if ($name === '') $errors[] = 'Name is required.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if ($message === '') $errors[] = 'Message is required.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

$consultationMap = [
    'yes'   => 'Yes, I\'d like to schedule a consultation',
    'no'    => 'No, I have other questions',
    'maybe' => 'I\'m not sure yet',
];
$consultationLabel = $consultationMap[$consultation] ?? 'Not specified';

$subject = "$subject_prefix New Inquiry from $name";

$html  = '<!DOCTYPE html>';
$html .= '<html lang="en"><head><meta charset="UTF-8"/>';
$html .= '<meta name="viewport" content="width=device-width,initial-scale=1"/>';
$html .= '<title>New Inquiry</title></head>';
$html .= '<body style="margin:0;padding:0;background:#f8f4ff;font-family:\'Georgia\',serif;">';

$html .= '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f4ff;padding:40px 0;">';
$html .= '<tr><td align="center">';
$html .= '<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">';

$html .= '<tr><td style="background:linear-gradient(135deg,#6b5b95,#8b7faa);border-radius:12px 12px 0 0;padding:36px 40px;">';
$html .= '<table width="100%" cellpadding="0" cellspacing="0"><tr>';
$html .= '<td>';
$html .= '<p style="margin:0 0 4px 0;font-size:11px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:rgba(255,255,255,0.6);">Livana Soul Doula</p>';
$html .= '<h1 style="margin:0;font-size:22px;font-weight:400;color:#ffffff;letter-spacing:0.02em;font-family:\'Georgia\',serif;">New Contact Inquiry</h1>';
$html .= '</td>';
$html .= '<td align="right" style="vertical-align:middle;">';
$html .= '<span style="display:inline-block;background:rgba(255,255,255,0.15);color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:6px 14px;border-radius:100px;">New Message</span>';
$html .= '</td>';
$html .= '</tr></table>';
$html .= '</td></tr>';

$html .= '<tr><td style="background:#ffffff;padding:36px 40px;">';

$html .= '<p style="margin:0 0 20px 0;font-size:11px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;color:#6b7280;">Contact Details</p>';

$html .= '<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">';

$html .= '<tr>';
$html .= '<td style="padding:12px 16px;background:#f9fafb;border-radius:8px 8px 0 0;border-bottom:1px solid #e5e7eb;width:160px;">';
$html .= '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#6b7280;">Name</p>';
$html .= '</td>';
$html .= '<td style="padding:12px 16px;background:#f9fafb;border-radius:8px 8px 0 0;border-bottom:1px solid #e5e7eb;">';
$html .= '<p style="margin:0;font-size:14px;color:#111827;font-family:\'Georgia\',serif;">' . htmlspecialchars($name) . '</p>';
$html .= '</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td style="padding:12px 16px;background:#ffffff;border-bottom:1px solid #e5e7eb;">';
$html .= '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#6b7280;">Email</p>';
$html .= '</td>';
$html .= '<td style="padding:12px 16px;background:#ffffff;border-bottom:1px solid #e5e7eb;">';
$html .= '<a href="mailto:' . htmlspecialchars($email) . '" style="font-size:14px;color:#6b5b95;text-decoration:none;font-family:\'Georgia\',serif;">' . htmlspecialchars($email) . '</a>';
$html .= '</td>';
$html .= '</tr>';

if ($phone !== '') {
    $html .= '<tr>';
    $html .= '<td style="padding:12px 16px;background:#f9fafb;border-bottom:1px solid #e5e7eb;">';
    $html .= '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#6b7280;">Phone</p>';
    $html .= '</td>';
    $html .= '<td style="padding:12px 16px;background:#f9fafb;border-bottom:1px solid #e5e7eb;">';
    $html .= '<a href="tel:' . htmlspecialchars($phone) . '" style="font-size:14px;color:#6b5b95;text-decoration:none;font-family:\'Georgia\',serif;">' . htmlspecialchars($phone) . '</a>';
    $html .= '</td>';
    $html .= '</tr>';
}

$html .= '<tr>';
$html .= '<td style="padding:12px 16px;background:#ffffff;border-radius:0 0 0 8px;">';
$html .= '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#6b7280;">Consultation</p>';
$html .= '</td>';
$html .= '<td style="padding:12px 16px;background:#ffffff;border-radius:0 0 8px 0;">';
$html .= '<p style="margin:0;font-size:14px;color:#111827;font-family:\'Georgia\',serif;">' . htmlspecialchars($consultationLabel) . '</p>';
$html .= '</td>';
$html .= '</tr>';

$html .= '</table>';

$html .= '<p style="margin:0 0 12px 0;font-size:11px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;color:#6b7280;">Message</p>';
$html .= '<div style="background:#f8f4ff;border-left:3px solid #8b7faa;border-radius:0 8px 8px 0;padding:18px 20px;">';
$html .= '<p style="margin:0;font-size:14px;color:#374151;line-height:1.75;font-family:\'Georgia\',serif;font-style:italic;">' . nl2br(htmlspecialchars($message)) . '</p>';
$html .= '</div>';

$html .= '</td></tr>';

$html .= '<tr><td style="background:#f9fafb;border-top:1px solid #e5e7eb;border-radius:0 0 12px 12px;padding:20px 40px;">';
$html .= '<table width="100%" cellpadding="0" cellspacing="0"><tr>';
$html .= '<td><p style="margin:0;font-size:11px;color:#9ca3af;">Submitted via <a href="https://livanasouldoula.com" style="color:#6b5b95;text-decoration:none;">livanasouldoula.com</a></p></td>';
$html .= '<td align="right"><p style="margin:0;font-size:11px;color:#9ca3af;">' . date('M j, Y · g:i A T') . '</p></td>';
$html .= '</tr></table>';
$html .= '</td></tr>';

$html .= '</table>';
$html .= '</td></tr></table>';
$html .= '</body></html>';

$headers  = "From: Livana Soul Doula <site@livanasouldoula.com>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$sent = mail($receiving_email, $subject, $html, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => "Thank you — I'll respond within 24-48 hours."]);
} else {
    error_log("mail() failed for $email - check mail config");
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please email directly at jacobhandw@gmail.com.']);
}