<?php
require_once '../config/database.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

$name       = trim($_POST['name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$message    = trim($_POST['message'] ?? '');
$propertyId = isset($_POST['property_id']) && (int)$_POST['property_id'] > 0 ? (int)$_POST['property_id'] : null;
$redirect   = $_POST['redirect'] ?? '/contact/index.php';

// Validate redirect to prevent open redirect - must start with / but not //
if (!preg_match('#^/[^/]#', $redirect) && $redirect !== '/') {
    $redirect = '/contact/index.php';
}

if (empty($name) || empty($email) || empty($message)) {
    $_SESSION['inquiry_error'] = 'Please fill in all required fields.';
    header("Location: $redirect");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['inquiry_error'] = 'Please enter a valid email address.';
    header("Location: $redirect");
    exit;
}

$db = getDB();
$stmt = $db->prepare("INSERT INTO inquiries (name, email, phone, message, property_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('ssssi', $name, $email, $phone, $message, $propertyId);

if ($stmt->execute()) {
    $_SESSION['inquiry_success'] = 'Your message has been sent successfully! We will get back to you soon.';
} else {
    $_SESSION['inquiry_error'] = 'Failed to send your message. Please try again.';
}

$db->close();
header("Location: $redirect");
exit;
?>
