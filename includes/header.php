<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . SITE_NAME : SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a3c6e',
                        accent: '#f59e0b',
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <div class="bg-primary rounded-lg p-2">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                </div>
                <span class="text-primary font-bold text-xl">Petrex Estate</span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-gray-700 hover:text-primary font-medium transition-colors">Home</a>
                <a href="/properties/index.php" class="text-gray-700 hover:text-primary font-medium transition-colors">Properties</a>
                <a href="/agents/index.php" class="text-gray-700 hover:text-primary font-medium transition-colors">Agents</a>
                <a href="/blog/index.php" class="text-gray-700 hover:text-primary font-medium transition-colors">Blog</a>
                <a href="/contact/index.php" class="text-gray-700 hover:text-primary font-medium transition-colors">Contact</a>
            </div>

            <!-- Auth Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                <?php if (isLoggedIn()): ?>
                    <span class="text-gray-700 font-medium">
                        <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>
                    </span>
                    <?php if (isAdmin()): ?>
                        <a href="/admin/index.php" class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-900 transition-colors">Dashboard</a>
                    <?php endif; ?>
                    <a href="/auth/logout.php" class="border border-primary text-primary px-4 py-2 rounded-lg font-medium hover:bg-primary hover:text-white transition-colors">Logout</a>
                <?php else: ?>
                    <a href="/auth/login.php" class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-900 transition-colors">Login</a>
                    <a href="/auth/register.php" class="border border-primary text-primary px-4 py-2 rounded-lg font-medium hover:bg-primary hover:text-white transition-colors">Register</a>
                <?php endif; ?>
            </div>

            <!-- Mobile hamburger -->
            <button id="menuToggle" class="md:hidden text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path id="menuIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden pb-4">
            <div class="flex flex-col space-y-3">
                <a href="/" class="text-gray-700 hover:text-primary font-medium py-2 border-b border-gray-100">Home</a>
                <a href="/properties/index.php" class="text-gray-700 hover:text-primary font-medium py-2 border-b border-gray-100">Properties</a>
                <a href="/agents/index.php" class="text-gray-700 hover:text-primary font-medium py-2 border-b border-gray-100">Agents</a>
                <a href="/blog/index.php" class="text-gray-700 hover:text-primary font-medium py-2 border-b border-gray-100">Blog</a>
                <a href="/contact/index.php" class="text-gray-700 hover:text-primary font-medium py-2 border-b border-gray-100">Contact</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="/admin/index.php" class="text-gray-700 hover:text-primary font-medium py-2 border-b border-gray-100">Dashboard</a>
                    <?php endif; ?>
                    <a href="/auth/logout.php" class="text-red-600 hover:text-red-800 font-medium py-2">Logout</a>
                <?php else: ?>
                    <a href="/auth/login.php" class="bg-primary text-white px-4 py-2 rounded-lg font-medium text-center">Login</a>
                    <a href="/auth/register.php" class="border border-primary text-primary px-4 py-2 rounded-lg font-medium text-center">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
    document.getElementById('menuToggle').addEventListener('click', function() {
        var menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    });
</script>
