<?php
require_once '../config/database.php';
require_once '../config/auth.php';

$db = getDB();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: /properties/index.php');
    exit;
}

$stmt = $db->prepare("SELECT p.*, a.full_name AS agent_name, a.email AS agent_email, a.phone AS agent_phone, a.bio AS agent_bio FROM properties p LEFT JOIN agents a ON p.agent_id = a.id WHERE p.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$property = $stmt->get_result()->fetch_assoc();

if (!$property) {
    header('Location: /properties/index.php');
    exit;
}

$pageTitle = $property['title'];

// Related properties (same state, different id)
$relStmt = $db->prepare("SELECT * FROM properties WHERE state = ? AND id != ? AND status = 'available' LIMIT 3");
$relStmt->bind_param('si', $property['state'], $id);
$relStmt->execute();
$related = $relStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$db->close();

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

<section class="bg-primary py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-blue-200 text-sm mb-2">
            <a href="/" class="hover:text-white">Home</a> &rsaquo;
            <a href="/properties/index.php" class="hover:text-white">Properties</a> &rsaquo;
            <span class="text-white"><?php echo htmlspecialchars($property['title']); ?></span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-bold text-white"><?php echo htmlspecialchars($property['title']); ?></h1>
    </div>
</section>

<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Property Image -->
                <div class="bg-gradient-to-br from-primary to-blue-800 rounded-2xl h-80 flex items-center justify-center mb-6 overflow-hidden">
                    <?php if (!empty($property['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($property['image_url']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>" class="w-full h-full object-cover rounded-2xl">
                    <?php else: ?>
                        <svg class="w-20 h-20 text-blue-300 opacity-40" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                    <?php endif; ?>
                </div>

                <!-- Details -->
                <div class="bg-white rounded-2xl shadow p-6 mb-6">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="<?php echo $property['type'] === 'sale' ? 'bg-primary' : 'bg-green-600'; ?> text-white text-sm font-bold px-4 py-1.5 rounded-full uppercase">
                            For <?php echo htmlspecialchars($property['type']); ?>
                        </span>
                        <span class="bg-gray-100 text-gray-700 text-sm font-semibold px-4 py-1.5 rounded-full capitalize">
                            <?php echo htmlspecialchars($property['status']); ?>
                        </span>
                        <?php if ($property['featured']): ?>
                        <span class="bg-accent text-white text-sm font-bold px-4 py-1.5 rounded-full">⭐ Featured</span>
                        <?php endif; ?>
                    </div>

                    <div class="text-4xl font-bold text-primary mb-1">
                        &#8358;<?php echo number_format($property['price']); ?>
                        <?php if ($property['type'] === 'rent'): ?>
                            <span class="text-lg font-normal text-gray-500">/year</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-500 mb-6">📍 <?php echo htmlspecialchars($property['location']); ?>, <?php echo htmlspecialchars($property['state']); ?></p>

                    <!-- Property Features -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-xl">
                        <div class="text-center">
                            <div class="text-2xl">🛏</div>
                            <div class="font-bold text-gray-900"><?php echo (int)$property['bedrooms']; ?></div>
                            <div class="text-sm text-gray-500">Bedrooms</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl">🚿</div>
                            <div class="font-bold text-gray-900"><?php echo (int)$property['bathrooms']; ?></div>
                            <div class="text-sm text-gray-500">Bathrooms</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl">🚽</div>
                            <div class="font-bold text-gray-900"><?php echo (int)$property['toilets']; ?></div>
                            <div class="text-sm text-gray-500">Toilets</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl">📐</div>
                            <div class="font-bold text-gray-900"><?php echo htmlspecialchars($property['size']); ?></div>
                            <div class="text-sm text-gray-500">sqm</div>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-3">Property Description</h3>
                    <p class="text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
                </div>

                <!-- Inquiry Form -->
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Send an Inquiry</h3>

                    <?php if ($success): ?>
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>

                    <form action="/actions/submit-inquiry.php" method="POST" class="space-y-4">
                        <input type="hidden" name="property_id" value="<?php echo (int)$property['id']; ?>">
                        <input type="hidden" name="redirect" value="/properties/single.php?id=<?php echo (int)$property['id']; ?>">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" name="name" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary"
                                    placeholder="Your full name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" name="email" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary"
                                    placeholder="your@email.com">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" name="phone"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="+234 800 000 0000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea name="message" rows="4" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="I am interested in this property and would like more information..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-bold hover:bg-blue-900 transition-colors">
                            Send Inquiry
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Agent Card -->
                <?php if (!empty($property['agent_name'])): ?>
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="font-bold text-lg text-gray-900 mb-4">Listed By</h3>
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-14 h-14 rounded-full bg-primary flex items-center justify-center">
                            <span class="text-white text-xl font-bold"><?php echo strtoupper(substr($property['agent_name'], 0, 1)); ?></span>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900"><?php echo htmlspecialchars($property['agent_name']); ?></div>
                            <div class="text-gray-500 text-sm">Real Estate Agent</div>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2 text-gray-600">
                            <span>📧</span> <?php echo htmlspecialchars($property['agent_email'] ?? ''); ?>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <span>📞</span> <?php echo htmlspecialchars($property['agent_phone'] ?? ''); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Property Summary -->
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="font-bold text-lg text-gray-900 mb-4">Property Summary</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Type:</span>
                            <span class="font-semibold capitalize"><?php echo htmlspecialchars($property['type']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status:</span>
                            <span class="font-semibold capitalize"><?php echo htmlspecialchars($property['status']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">State:</span>
                            <span class="font-semibold"><?php echo htmlspecialchars($property['state']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Bedrooms:</span>
                            <span class="font-semibold"><?php echo (int)$property['bedrooms']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Bathrooms:</span>
                            <span class="font-semibold"><?php echo (int)$property['bathrooms']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Toilets:</span>
                            <span class="font-semibold"><?php echo (int)$property['toilets']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Size:</span>
                            <span class="font-semibold"><?php echo htmlspecialchars($property['size']); ?> sqm</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Listed:</span>
                            <span class="font-semibold"><?php echo date('M d, Y', strtotime($property['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Properties -->
        <?php if (!empty($related)): ?>
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-primary mb-6">Related Properties in <?php echo htmlspecialchars($property['state']); ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($related as $rel): ?>
                <div class="bg-white rounded-2xl shadow hover:shadow-lg transition-shadow group overflow-hidden">
                    <div class="h-40 bg-gradient-to-br from-primary to-blue-800 flex items-center justify-center relative">
                        <?php if (!empty($rel['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($rel['image_url']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <svg class="w-10 h-10 text-blue-300 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors">
                            <?php echo htmlspecialchars($rel['title']); ?>
                        </h4>
                        <p class="text-gray-500 text-sm mb-2">📍 <?php echo htmlspecialchars($rel['location']); ?></p>
                        <div class="font-bold text-primary">&#8358;<?php echo number_format($rel['price']); ?></div>
                        <a href="/properties/single.php?id=<?php echo (int)$rel['id']; ?>" class="mt-3 block text-center bg-primary text-white py-2 rounded-lg text-sm font-semibold hover:bg-blue-900 transition-colors">View</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
