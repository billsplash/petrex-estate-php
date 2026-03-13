<?php
require_once '../config/database.php';
require_once '../config/auth.php';

$pageTitle = 'Contact Us';

$success = '';
$error   = '';
if (isset($_SESSION['inquiry_success'])) {
    $success = $_SESSION['inquiry_success'];
    unset($_SESSION['inquiry_success']);
}
if (isset($_SESSION['inquiry_error'])) {
    $error = $_SESSION['inquiry_error'];
    unset($_SESSION['inquiry_error']);
}

include '../includes/header.php';
?>

<section class="bg-primary py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Contact Us</h1>
        <p class="text-blue-200">Get in touch with our team — we are here to help</p>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            <!-- Contact Form -->
            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-bold text-primary mb-6">Send Us a Message</h2>

                <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>
                <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <form action="/actions/submit-inquiry.php" method="POST" class="space-y-5">
                    <input type="hidden" name="redirect" value="/contact/index.php">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Your full name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="your@email.com">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" name="phone"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="+234 800 000 0000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                        <textarea name="message" rows="5" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="How can we help you?"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-3.5 rounded-lg font-bold hover:bg-blue-900 transition-colors text-lg">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow p-8">
                    <h2 class="text-2xl font-bold text-primary mb-6">Our Contact Details</h2>
                    <div class="space-y-5">
                        <div class="flex items-start space-x-4">
                            <div class="bg-primary rounded-full p-3 flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Office Address</h4>
                                <p class="text-gray-600 mt-1">15 Adeola Odeku Street<br>Victoria Island, Lagos, Nigeria</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="bg-primary rounded-full p-3 flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Phone</h4>
                                <p class="text-gray-600 mt-1">+234 800 738 739</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="bg-primary rounded-full p-3 flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Email</h4>
                                <p class="text-gray-600 mt-1">info@petrex-estate.com</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="bg-primary rounded-full p-3 flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Business Hours</h4>
                                <p class="text-gray-600 mt-1">Monday – Friday: 8am – 6pm<br>Saturday: 9am – 4pm</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map placeholder -->
                <div class="bg-gray-200 rounded-2xl h-64 flex items-center justify-center">
                    <p class="text-gray-500 font-medium">📍 Victoria Island, Lagos</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
