<?php
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$products = getProducts($category, $search);

// Get unique categories
$all_products = getProducts();
$categories = array_unique(array_column($all_products, 'category'));

// Handle purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_product'])) {
    if (!$user) {
        header('Location: index.php?page=login');
        exit;
    }
    
    if ($user['role'] !== 'buyer') {
        $error = 'Only buyers can purchase products';
    } else {
        $product_id = $_POST['product_id'];
        $product = getProductById($product_id);
        
        if ($product) {
            $order_id = createOrder(
                $user['id'],
                $product['vendor_id'],
                $product_id,
                1,
                $product['price'],
                '123 Default Address, City, State 12345'
            );
            $success = 'Order placed successfully! Order ID: ' . $order_id;
        }
    }
}
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">All Products</h1>
            
            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <!-- Search and Filter -->
            <form method="GET" class="flex flex-col md:flex-row gap-4 mb-6">
                <input type="hidden" name="page" value="products">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                           placeholder="Search products..."
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="relative">
                    <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <select name="category" class="pl-10 pr-8 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors">
                    Search
                </button>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-md card-hover overflow-hidden">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>" 
                         class="w-full h-48 object-cover">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-indigo-600 font-medium bg-indigo-50 px-2 py-1 rounded">
                                <?php echo htmlspecialchars($product['category']); ?>
                            </span>
                            <div class="flex items-center space-x-1">
                                <i class="fas fa-star text-yellow-400"></i>
                                <span class="text-sm text-gray-600"><?php echo $product['rating']; ?></span>
                                <span class="text-xs text-gray-400">(<?php echo $product['reviews']; ?>)</span>
                            </div>
                        </div>
                        
                        <h3 class="font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
                        
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-lg font-bold text-gray-900"><?php echo formatPrice($product['price']); ?></span>
                            <span class="text-xs text-gray-500">Stock: <?php echo $product['stock']; ?></span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">by <?php echo htmlspecialchars($product['vendor_name']); ?></span>
                            <form method="POST" class="inline">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" name="buy_product" 
                                        <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>
                                        class="bg-indigo-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center space-x-1">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span><?php echo $product['stock'] == 0 ? 'Out of Stock' : 'Buy Now'; ?></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">No products found matching your criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</div>