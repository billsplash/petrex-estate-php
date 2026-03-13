<?php
require_once '../config/database.php';
require_once '../config/auth.php';

$pageTitle = 'Our Agents';
$db = getDB();

$stmt = $db->prepare("SELECT * FROM agents ORDER BY properties_sold DESC");
$stmt->execute();
$agents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$db->close();

include '../includes/header.php';
?>

<section class="bg-primary py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Our Agents</h1>
        <p class="text-blue-200">Meet the experienced professionals who will help you find your dream property</p>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($agents)): ?>
        <div class="text-center py-16 text-gray-500">No agents found.</div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php foreach ($agents as $agent): ?>
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <!-- Avatar -->
                <div class="w-24 h-24 rounded-full mx-auto mb-4 overflow-hidden bg-primary flex items-center justify-center">
                    <?php if (!empty($agent['avatar_url'])): ?>
                        <img src="<?php echo htmlspecialchars($agent['avatar_url']); ?>"
                             alt="<?php echo htmlspecialchars($agent['full_name']); ?>"
                             class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-white text-3xl font-bold">
                            <?php echo strtoupper(substr($agent['full_name'], 0, 1)); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <h3 class="font-bold text-xl text-gray-900 mb-1"><?php echo htmlspecialchars($agent['full_name']); ?></h3>
                <p class="text-primary text-sm font-semibold mb-3">Real Estate Agent</p>

                <!-- Rating Stars -->
                <div class="flex justify-center mb-3">
                    <?php
                    $rating = round($agent['rating']);
                    for ($i = 1; $i <= 5; $i++):
                    ?>
                        <span class="text-xl <?php echo $i <= $rating ? 'text-accent' : 'text-gray-300'; ?>">★</span>
                    <?php endfor; ?>
                    <span class="ml-2 text-sm text-gray-500">(<?php echo number_format((float)$agent['rating'], 1); ?>)</span>
                </div>

                <p class="text-gray-500 text-sm mb-4 leading-relaxed"><?php echo htmlspecialchars($agent['bio'] ?? ''); ?></p>

                <div class="border-t pt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-center gap-2 text-gray-600">
                        <span>📧</span>
                        <a href="mailto:<?php echo htmlspecialchars($agent['email']); ?>" class="hover:text-primary truncate">
                            <?php echo htmlspecialchars($agent['email']); ?>
                        </a>
                    </div>
                    <div class="flex items-center justify-center gap-2 text-gray-600">
                        <span>📞</span>
                        <a href="tel:<?php echo htmlspecialchars($agent['phone']); ?>" class="hover:text-primary">
                            <?php echo htmlspecialchars($agent['phone']); ?>
                        </a>
                    </div>
                    <div class="mt-3 font-semibold text-primary">
                        <?php echo (int)$agent['properties_sold']; ?> Properties Sold
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="py-12" style="background: linear-gradient(135deg, #1a3c6e, #0f2447);">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">Want to Join Our Team?</h2>
        <p class="text-blue-200 mb-6">We are always looking for passionate real estate professionals to join Petrex Estate.</p>
        <a href="/contact/index.php" class="bg-accent hover:bg-yellow-500 text-white font-bold px-8 py-3 rounded-xl transition-colors inline-block">
            Contact Us
        </a>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
