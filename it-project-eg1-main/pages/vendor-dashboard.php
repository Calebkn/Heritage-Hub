<?php
if (!$user || $user['role'] !== 'vendor') {
    header('Location: index.php');
    exit;
}

// Handle add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $image = 'https://images.pexels.com/photos/1029757/pexels-photo-1029757.jpeg';
    
    if ($title && $description && $price && $category && $stock) {
        if (addProduct($user['id'], $title, $description, $price, $category, $image, $stock)) {
            $success = 'Product added successfully!';
        } else {
            $error = 'Failed to add product.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    $tracking_number = null;
    
    if ($new_status === 'shipped') {
        $tracking_number = generateTrackingNumber();
    }
    
    if (updateOrderStatus($order_id, $new_status, $tracking_number)) {
        $success = 'Order status updated successfully!';
    }
}

$vendor_products = getProducts();
$vendor_products = array_filter($vendor_products, function($p) use ($user) {
    return $p['vendor_id'] == $user['id'];
});

$vendor_orders = getOrdersByUser($user['id'], 'vendor');
$total_revenue = array_sum(array_column($vendor_orders, 'total_amount'));
$total_orders = count($vendor_orders);
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Vendor Dashboard</h1>
            <p class="text-gray-600">Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</p>
        </div>

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

        <!-- Stats Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($vendor_products); ?></p>
                    </div>
                    <i class="fas fa-box text-2xl text-indigo-600"></i>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $total_orders; ?></p>
                    </div>
                    <i class="fas fa-chart-line text-2xl text-green-600"></i>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo formatPrice($total_revenue); ?></p>
                    </div>
                    <i class="fas fa-dollar-sign text-2xl text-yellow-600"></i>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Avg. Rating</p>
                        <p class="text-2xl font-bold text-gray-900">4.6</p>
                    </div>
                    <i class="fas fa-star text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Products Section -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">My Products</h2>
                        <button onclick="toggleAddProduct()" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition-colors flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Add Product</span>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <?php if (empty($vendor_products)): ?>
                        <p class="text-gray-500 text-center py-8">No products yet. Add your first product!</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($vendor_products as $product): ?>
                                <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                         class="w-16 h-16 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($product['title']); ?></h3>
                                        <p class="text-sm text-gray-600"><?php echo formatPrice($product['price']); ?> • Stock: <?php echo $product['stock']; ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Orders Section -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Orders</h2>
                </div>
                
                <div class="p-6">
                    <?php if (empty($vendor_orders)): ?>
                        <p class="text-gray-500 text-center py-8">No orders yet.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($vendor_orders as $order): ?>
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-gray-900">Order #<?php echo $order['id']; ?></span>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            <?php 
                                            echo match($order['status']) {
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'confirmed' => 'bg-blue-100 text-blue-800',
                                                'shipped' => 'bg-purple-100 text-purple-800',
                                                'delivered' => 'bg-green-100 text-green-800',
                                                default => 'bg-red-100 text-red-800'
                                            };
                                            ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3"><?php echo formatPrice($order['total_amount']); ?></p>
                                    
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <button type="submit" name="update_status" value="confirmed" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors mr-2">
                                                Confirm
                                            </button>
                                            <input type="hidden" name="new_status" value="confirmed">
                                        <?php elseif ($order['status'] === 'confirmed'): ?>
                                            <button type="submit" name="update_status" value="shipped" class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700 transition-colors mr-2">
                                                Ship
                                            </button>
                                            <input type="hidden" name="new_status" value="shipped">
                                        <?php elseif ($order['status'] === 'shipped'): ?>
                                            <button type="submit" name="update_status" value="delivered" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors mr-2">
                                                Mark Delivered
                                            </button>
                                            <input type="hidden" name="new_status" value="delivered">
                                        <?php endif; ?>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Product</h3>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" required rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price (R)</label>
                    <input type="number" name="price" step="0.01" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <input type="number" name="stock" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Category</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Fashion">Fashion</option>
                    <option value="Home">Home</option>
                    <option value="Sports">Sports</option>
                    <option value="Books">Books</option>
                </select>
            </div>
            
            <div class="flex space-x-3 pt-4">
                <button type="submit" name="add_product" class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-md font-medium hover:bg-indigo-700 transition-colors">
                    Add Product
                </button>
                <button type="button" onclick="toggleAddProduct()" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md font-medium hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAddProduct() {
    const modal = document.getElementById('addProductModal');
    modal.classList.toggle('hidden');
}
</script>