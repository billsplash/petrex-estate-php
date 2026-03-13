<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';
requireAdmin();

$pageTitle = 'Add Property';

$nigerianStates = [
    'Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River',
    'Delta','Ebonyi','Edo','Ekiti','Enugu','FCT (Abuja)','Gombe','Imo','Jigawa','Kaduna',
    'Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun',
    'Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'
];

$error = '';

$db = getDB();
$agentsRes = $db->prepare("SELECT id, full_name FROM agents ORDER BY full_name");
$agentsRes->execute();
$agents = $agentsRes->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $location    = trim($_POST['location'] ?? '');
    $state       = trim($_POST['state'] ?? '');
    $bedrooms    = (int)($_POST['bedrooms'] ?? 0);
    $bathrooms   = (int)($_POST['bathrooms'] ?? 0);
    $toilets     = (int)($_POST['toilets'] ?? 0);
    $size        = (float)($_POST['size'] ?? 0);
    $type        = $_POST['type'] ?? 'sale';
    $status      = $_POST['status'] ?? 'available';
    $featured    = isset($_POST['featured']) ? 1 : 0;
    $imageUrl    = trim($_POST['image_url'] ?? '');
    $agentId     = !empty($_POST['agent_id']) ? (int)$_POST['agent_id'] : null;

    if (empty($title) || empty($location) || empty($state) || $price <= 0) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $db->prepare("INSERT INTO properties (title, description, price, location, state, bedrooms, bathrooms, toilets, size, type, status, featured, image_url, agent_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssdssiiidssisi', $title, $description, $price, $location, $state, $bedrooms, $bathrooms, $toilets, $size, $type, $status, $featured, $imageUrl, $agentId);

        if ($stmt->execute()) {
            $db->close();
            header('Location: /admin/properties/index.php?saved=1');
            exit;
        } else {
            $error = 'Failed to save property. Please try again.';
        }
    }
}
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#1a3c6e', accent: '#f59e0b' } } } }</script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100">
<div class="flex">
    <?php include '../includes/sidebar.php'; ?>
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add Property</h1>
                <p class="text-gray-500">Create a new property listing</p>
            </div>
            <a href="/admin/properties/index.php" class="text-primary hover:underline">← Back to Properties</a>
        </div>

        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow p-8">
            <form method="POST" action="" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Title *</label>
                    <input type="text" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="e.g. Luxury 4 Bedroom Duplex">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Property description..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price (₦) *</label>
                    <input type="number" name="price" required value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" min="0" step="0.01"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                    <input type="text" name="location" required value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="e.g. Lekki Phase 1, Lagos">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                    <select name="state" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Select State</option>
                        <?php foreach ($nigerianStates as $s): ?>
                        <option value="<?php echo htmlspecialchars($s); ?>" <?php echo (($_POST['state'] ?? '') === $s) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="sale" <?php echo (($_POST['type'] ?? 'sale') === 'sale') ? 'selected' : ''; ?>>For Sale</option>
                        <option value="rent" <?php echo (($_POST['type'] ?? '') === 'rent') ? 'selected' : ''; ?>>For Rent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="available" <?php echo (($_POST['status'] ?? 'available') === 'available') ? 'selected' : ''; ?>>Available</option>
                        <option value="sold" <?php echo (($_POST['status'] ?? '') === 'sold') ? 'selected' : ''; ?>>Sold</option>
                        <option value="rented" <?php echo (($_POST['status'] ?? '') === 'rented') ? 'selected' : ''; ?>>Rented</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bedrooms</label>
                    <input type="number" name="bedrooms" value="<?php echo (int)($_POST['bedrooms'] ?? 0); ?>" min="0"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bathrooms</label>
                    <input type="number" name="bathrooms" value="<?php echo (int)($_POST['bathrooms'] ?? 0); ?>" min="0"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Toilets</label>
                    <input type="number" name="toilets" value="<?php echo (int)($_POST['toilets'] ?? 0); ?>" min="0"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size (sqm)</label>
                    <input type="number" name="size" value="<?php echo htmlspecialchars($_POST['size'] ?? ''); ?>" min="0" step="0.01"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agent</label>
                    <select name="agent_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">No Agent</option>
                        <?php foreach ($agents as $agent): ?>
                        <option value="<?php echo (int)$agent['id']; ?>" <?php echo (($_POST['agent_id'] ?? '') == $agent['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($agent['full_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                    <input type="url" name="image_url" value="<?php echo htmlspecialchars($_POST['image_url'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="https://example.com/image.jpg">
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="featured" <?php echo isset($_POST['featured']) ? 'checked' : ''; ?> class="w-5 h-5 rounded">
                        <span class="text-sm font-medium text-gray-700">Mark as Featured Property</span>
                    </label>
                </div>
                <div class="md:col-span-2 flex gap-4">
                    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-900 transition-colors">
                        Save Property
                    </button>
                    <a href="/admin/properties/index.php" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-xl font-bold hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
