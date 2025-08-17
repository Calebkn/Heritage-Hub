<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user = getUserById($_SESSION['user_id']);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $orders = getOrdersByUser($user['id'], $user['role']);
        echo json_encode($orders);
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($user['role'] === 'buyer') {
            // Create order
            $order_id = createOrder(
                $user['id'],
                $data['vendor_id'],
                $data['product_id'],
                $data['quantity'],
                $data['total_amount'],
                $data['shipping_address']
            );
            echo json_encode(['order_id' => $order_id]);
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'Only buyers can create orders']);
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($user['role'] === 'vendor') {
            $tracking_number = null;
            if ($data['status'] === 'shipped') {
                $tracking_number = generateTrackingNumber();
            }
            
            $success = updateOrderStatus($data['order_id'], $data['status'], $tracking_number);
            echo json_encode(['success' => $success, 'tracking_number' => $tracking_number]);
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'Only vendors can update order status']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>