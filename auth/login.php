<?php
require_once '../config/database.php';
require_once '../config/auth.php';

if (isLoggedIn()) {
    header('Location: /');
    exit;
}

$pageTitle = 'Login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter your email and password.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $db->close();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['role']      = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: /admin/index.php');
            } else {
                header('Location: /');
            }
            exit;
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
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
            <h1 class="text-3xl font-bold text-gray-900">Welcome Back</h1>
            <p class="text-gray-500 mt-2">Sign in to your account</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="your@email.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="••••••••">
                </div>
                <button type="submit" class="w-full bg-primary text-white py-3.5 rounded-lg font-bold hover:bg-blue-900 transition-colors text-lg">
                    Sign In
                </button>
            </form>

            <p class="text-center text-gray-500 mt-6">
                Don't have an account?
                <a href="/auth/register.php" class="text-primary font-semibold hover:underline">Register here</a>
            </p>
        </div>

        <p class="text-center text-gray-400 text-xs mt-6">
            Default admin: admin@petrex-estate.com / password
        </p>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
