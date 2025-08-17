<?php
require_once 'config/database.php';

function getUserById($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createUser($email, $name, $avatar, $role, $google_id = null) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO users (email, name, avatar, role, google_id, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$email, $name, $avatar, $role, $google_id]);
    return $pdo->lastInsertId();
}

function getProducts($category = null, $search = null) {
    global $pdo;
    $sql = "SELECT p.*, u.name as vendor_name FROM products p 
            JOIN users u ON p.vendor_id = u.id 
            WHERE 1=1";
    $params = [];
    
    if ($category) {
        $sql .= " AND p.category = ?";
        $params[] = $category;
    }
    
    if ($search) {
        $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($product_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT p.*, u.name as vendor_name 
        FROM products p 
        JOIN users u ON p.vendor_id = u.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$product_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addProduct($vendor_id, $title, $description, $price, $category, $image, $stock) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO products (vendor_id, title, description, price, category, image, stock, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    return $stmt->execute([$vendor_id, $title, $description, $price, $category, $image, $stock]);
}

function createOrder($buyer_id, $vendor_id, $product_id, $quantity, $total_amount, $shipping_address) {
    global $pdo;
    $order_id = 'ORD' . strtoupper(substr(uniqid(), -6));
    
    $stmt = $pdo->prepare("
        INSERT INTO orders (id, buyer_id, vendor_id, product_id, quantity, total_amount, shipping_address, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->execute([$order_id, $buyer_id, $vendor_id, $product_id, $quantity, $total_amount, $shipping_address]);
    return $order_id;
}

function getOrdersByUser($user_id, $role) {
    global $pdo;
    $field = ($role === 'buyer') ? 'buyer_id' : 'vendor_id';
    
    $stmt = $pdo->prepare("
        SELECT o.*, p.title as product_title, p.image as product_image, 
               u.name as vendor_name, b.name as buyer_name
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        JOIN users u ON o.vendor_id = u.id 
        JOIN users b ON o.buyer_id = b.id 
        WHERE o.$field = ? 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateOrderStatus($order_id, $status, $tracking_number = null) {
    global $pdo;
    $sql = "UPDATE orders SET status = ?, updated_at = NOW()";
    $params = [$status];
    
    if ($tracking_number) {
        $sql .= ", tracking_number = ?";
        $params[] = $tracking_number;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $order_id;
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function getTrackingInfo($tracking_number) {
    // Mock tracking data - in real implementation, this would call shipping API
    return [
        [
            'status' => 'In Transit',
            'location' => 'Distribution Center - Johannesburg',
            'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'description' => 'Package is on its way to destination'
        ],
        [
            'status' => 'Shipped',
            'location' => 'Warehouse - Cape Town',
            'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'description' => 'Package has been shipped'
        ]
    ];
}

function generateTrackingNumber() {
    return 'TRK' . strtoupper(substr(uniqid(), -9));
}

function formatPrice($price) {
    return 'R' . number_format($price, 2);
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    
    return date('M j, Y', strtotime($datetime));
}
?>