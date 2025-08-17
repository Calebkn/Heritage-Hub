<?php
header('Content-Type: application/json');
require_once '../includes/functions.php';

if (!isset($_GET['tracking_number'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Tracking number required']);
    exit;
}

$tracking_number = $_GET['tracking_number'];
$tracking_info = getTrackingInfo($tracking_number);

echo json_encode($tracking_info);
?>