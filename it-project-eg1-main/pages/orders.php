<?php
if (!$user || $user['role'] !== 'buyer') {
    header('Location: index.php');
    exit;
}

$user_orders = getOrdersByUser($user['id'], 'buyer');
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Orders</h1>
            <p class="text-gray-600">Track and manage your orders</p>
        </div>

        <?php if (empty($user_orders)): ?>
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="fas fa-box text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
                <p class="text-gray-600">Start shopping to see your orders here!</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($user_orders as $order): ?>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <span class="text-lg font-semibold text-gray-900">Order #<?php echo $order['id']; ?></span>
                                    <span class="px-3 py-1 text-sm font-medium rounded-full 
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
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Placed on</p>
                                    <p class="font-medium"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4 mb-4 p-4 bg-gray-50 rounded-lg">
                                <img src="<?php echo htmlspecialchars($order['product_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($order['product_title']); ?>" 
                                     class="w-16 h-16 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($order['product_title']); ?></h3>
                                    <p class="text-sm text-gray-600">Quantity: <?php echo $order['quantity']; ?></p>
                                    <p class="text-sm text-gray-600">Vendor: <?php echo htmlspecialchars($order['vendor_name']); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900"><?php echo formatPrice($order['total_amount']); ?></p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-<?php echo match($order['status']) {
                                        'pending' => 'clock text-yellow-500',
                                        'confirmed' => 'check-circle text-blue-500',
                                        'shipped' => 'truck text-purple-500',
                                        'delivered' => 'box text-green-500',
                                        default => 'clock text-gray-500'
                                    }; ?>"></i>
                                    <span class="text-sm text-gray-600">
                                        <?php 
                                        echo match($order['status']) {
                                            'pending' => 'Order is being processed',
                                            'confirmed' => 'Order confirmed, preparing for shipment',
                                            'shipped' => 'Order is on its way',
                                            'delivered' => 'Order has been delivered',
                                            default => 'Order status unknown'
                                        };
                                        ?>
                                    </span>
                                </div>
                                
                                <?php if ($order['tracking_number']): ?>
                                    <button onclick="toggleTracking('<?php echo $order['id']; ?>')" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                        Track Package
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Tracking Information -->
                            <?php if ($order['tracking_number']): ?>
                                <div id="tracking-<?php echo $order['id']; ?>" class="mt-6 border-t border-gray-200 pt-6 hidden">
                                    <div class="flex items-center space-x-2 mb-4">
                                        <i class="fas fa-truck text-indigo-600"></i>
                                        <h4 class="font-medium text-gray-900">Tracking: <?php echo $order['tracking_number']; ?></h4>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <?php 
                                        $tracking_info = getTrackingInfo($order['tracking_number']);
                                        foreach ($tracking_info as $index => $info): 
                                        ?>
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-3 h-3 rounded-full <?php echo $index === 0 ? 'bg-indigo-600' : 'bg-gray-300'; ?>"></div>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <p class="font-medium text-gray-900"><?php echo $info['status']; ?></p>
                                                        <p class="text-sm text-gray-500"><?php echo date('M d, Y H:i', strtotime($info['timestamp'])); ?></p>
                                                    </div>
                                                    <div class="flex items-center space-x-2 mt-1">
                                                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                                                        <p class="text-sm text-gray-600"><?php echo $info['location']; ?></p>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-1"><?php echo $info['description']; ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleTracking(orderId) {
    const trackingDiv = document.getElementById('tracking-' + orderId);
    trackingDiv.classList.toggle('hidden');
}
</script>