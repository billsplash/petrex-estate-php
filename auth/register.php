<?php
require_once '../config/database.php';
require_once '../config/auth.php';

if (isLoggedIn()) {
    header('Location: /');
    exit;
}

$pageTitle = 'Register';
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($fullName) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $db = getDB();

        // Check if email exists
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        $existing = $check->get_result()->fetch_assoc();

        if ($existing) {
            $error = 'An account with this email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')");
            $stmt->bind_param('ssss', $fullName, $email, $phone, $hashed);

            if ($stmt->execute()) {
                $success = 'Account created successfully! You can now <a href="/auth/login.php" class="underline font-bold">login here</a>.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        $db->close();
    }
}

include '../includes/header.php';
?>

<section class="min-h-screen bg-gray-50 flex items-center justify-center py-12">
    <div class="max-w-md w-full mx-4">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center space-x-2 mb-6">
                <div class="bg-primary rounded-lg p-2">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                </div>
                <span class="text-primary font-bold text-2xl">Petrex Estate</span>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Create Account</h1>
            <p class="text-gray-500 mt-2">Join Petrex Estate today</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <?php echo $success; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="full_name" required
                        value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Your full name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                    <input type="email" name="email" required
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="your@email.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" name="phone"
                        value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="+234 800 000 0000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Minimum 6 characters">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                    <input type="password" name="confirm_password" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Repeat your password">
                </div>
                <button type="submit" class="w-full bg-primary text-white py-3.5 rounded-lg font-bold hover:bg-blue-900 transition-colors text-lg">
                    Create Account
                </button>
            </form>

            <p class="text-center text-gray-500 mt-6">
                Already have an account?
                <a href="/auth/login.php" class="text-primary font-semibold hover:underline">Login here</a>
            </p>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
