<?php
require_once '../config/database.php';
require_once '../config/auth.php';

$pageTitle = 'Properties';
$db = getDB();

$nigerianStates = [
    'Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River',
    'Delta','Ebonyi','Edo','Ekiti','Enugu','FCT (Abuja)','Gombe','Imo','Jigawa','Kaduna',
    'Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun',
    'Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'
];

// Filters
$search   = trim($_GET['search'] ?? '');
$type     = $_GET['type'] ?? '';
$state    = $_GET['state'] ?? '';
$bedrooms = $_GET['bedrooms'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 10;
$offset   = ($page - 1) * $perPage;

// Build WHERE clause
$where  = ["status = 'available'"];
$params = [];
$types  = '';

if ($search !== '') {
    $where[]  = "(title LIKE ? OR location LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types   .= 'ss';
}
if ($type !== '') {
    $where[]  = "type = ?";
    $params[] = $type;
    $types   .= 's';
}
if ($state !== '') {
    $where[]  = "state = ?";
    $params[] = $state;
    $types   .= 's';
}
if ($bedrooms !== '') {
    $where[]  = "bedrooms >= ?";
    $params[] = (int)$bedrooms;
    $types   .= 'i';
}
if ($minPrice !== '') {
    $where[]  = "price >= ?";
    $params[] = (float)$minPrice;
    $types   .= 'd';
}
if ($maxPrice !== '') {
    $where[]  = "price <= ?";
    $params[] = (float)$maxPrice;
    $types   .= 'd';
}

$whereSql = implode(' AND ', $where);

// Count total
$countSql  = "SELECT COUNT(*) as total FROM properties WHERE $whereSql";
$countStmt = $db->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRows  = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $perPage);

// Fetch properties
$sql  = "SELECT * FROM properties WHERE $whereSql ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
$allTypes  = $types . 'ii';
$allParams = array_merge($params, [$perPage, $offset]);
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$db->close();

include '../includes/header.php';
?>

<section class="bg-primary py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Properties</h1>
        <p class="text-blue-200">Browse our extensive collection of properties across Nigeria</p>
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Filter Form -->
        <div class="bg-white rounded-2xl shadow p-6 mb-8">
            <form id="filter-form" method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="lg:col-span-2">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Search title or location..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">All Types</option>
                        <option value="sale" <?php echo $type === 'sale' ? 'selected' : ''; ?>>For Sale</option>
                        <option value="rent" <?php echo $type === 'rent' ? 'selected' : ''; ?>>For Rent</option>
                    </select>
                </div>
                <div>
                    <select name="state" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">All States</option>
                        <?php foreach ($nigerianStates as $s): ?>
                        <option value="<?php echo htmlspecialchars($s); ?>" <?php echo $state === $s ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <select name="bedrooms" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Any Beds</option>
                        <option value="1" <?php echo $bedrooms === '1' ? 'selected' : ''; ?>>1+ Bed</option>
                        <option value="2" <?php echo $bedrooms === '2' ? 'selected' : ''; ?>>2+ Beds</option>
                        <option value="3" <?php echo $bedrooms === '3' ? 'selected' : ''; ?>>3+ Beds</option>
                        <option value="4" <?php echo $bedrooms === '4' ? 'selected' : ''; ?>>4+ Beds</option>
                        <option value="5" <?php echo $bedrooms === '5' ? 'selected' : ''; ?>>5+ Beds</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-primary text-white px-4 py-2.5 rounded-lg font-semibold hover:bg-blue-900 transition-colors">
                        Filter
                    </button>
                    <a href="/properties/index.php" class="bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        ✕
                    </a>
                </div>
            </form>
            <!-- Price Range -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <input type="number" name="min_price" form="filter-form" placeholder="Min Price (₦)"
                    value="<?php echo htmlspecialchars($minPrice); ?>"
                    class="border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary">
                <input type="number" name="max_price" form="filter-form" placeholder="Max Price (₦)"
                    value="<?php echo htmlspecialchars($maxPrice); ?>"
                    class="border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
        </div>

        <!-- Results count -->
        <div class="flex justify-between items-center mb-6">
            <p class="text-gray-600">
                Showing <strong><?php echo count($properties); ?></strong> of <strong><?php echo $totalRows; ?></strong> properties
            </p>
        </div>

        <?php if (empty($properties)): ?>
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-500 mb-2">No properties found</h3>
            <p class="text-gray-400">Try adjusting your filters or <a href="/properties/index.php" class="text-primary hover:underline">clear all filters</a></p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($properties as $property): ?>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow group">
                <div class="relative h-48 bg-gradient-to-br from-primary to-blue-800 flex items-center justify-center">
                    <?php if (!empty($property['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($property['image_url']); ?>"
                             alt="<?php echo htmlspecialchars($property['title']); ?>"
                             class="w-full h-full object-cover">
                    <?php else: ?>
                        <svg class="w-12 h-12 text-blue-300 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                    <?php endif; ?>
                    <div class="absolute top-3 left-3">
                        <span class="<?php echo $property['type'] === 'sale' ? 'bg-primary' : 'bg-green-600'; ?> text-white text-xs font-bold px-3 py-1 rounded-full uppercase">
                            For <?php echo htmlspecialchars($property['type']); ?>
                        </span>
                    </div>
                    <?php if ($property['featured']): ?>
                    <div class="absolute top-3 right-3">
                        <span class="bg-accent text-white text-xs font-bold px-3 py-1 rounded-full">⭐ Featured</span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-lg text-gray-900 mb-1 group-hover:text-primary transition-colors">
                        <?php echo htmlspecialchars($property['title']); ?>
                    </h3>
                    <p class="text-gray-500 text-sm mb-2">
                        📍 <?php echo htmlspecialchars($property['location']); ?>
                    </p>
                    <div class="text-2xl font-bold text-primary mb-3">
                        &#8358;<?php echo number_format($property['price']); ?>
                        <?php if ($property['type'] === 'rent'): ?>
                            <span class="text-sm font-normal text-gray-500">/year</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4 border-t pt-3">
                        <span>🛏 <?php echo (int)$property['bedrooms']; ?> Beds</span>
                        <span>🚿 <?php echo (int)$property['bathrooms']; ?> Baths</span>
                        <span>📐 <?php echo htmlspecialchars($property['size']); ?> sqm</span>
                    </div>
                    <a href="/properties/single.php?id=<?php echo (int)$property['id']; ?>"
                       class="block w-full text-center bg-primary text-white py-2.5 rounded-lg font-semibold hover:bg-blue-900 transition-colors">
                        View Details
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center items-center gap-2 mt-10">
            <?php
            $queryParams = $_GET;
            if ($page > 1):
                $queryParams['page'] = $page - 1;
            ?>
            <a href="?<?php echo http_build_query($queryParams); ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">← Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): 
                $queryParams['page'] = $i;
            ?>
            <a href="?<?php echo http_build_query($queryParams); ?>"
               class="px-4 py-2 rounded-lg font-semibold <?php echo $i === $page ? 'bg-primary text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages):
                $queryParams['page'] = $page + 1;
            ?>
            <a href="?<?php echo http_build_query($queryParams); ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
