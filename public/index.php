<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once '../src/php/createOrder.php';
require_once '../src/php/preAuthorizePayment.php';
require_once '../src/php/releasePreAuthorization.php';
require_once '../src/php/refundPayment.php';
require_once '../src/php/capture.php';
header('Content-Type: application/json');

$routes = [
    'POST' => [
        '/api/orders' => 'createOrder',
        '/api/orders/preauth' => 'preauth',
        '/api/orders/charge-preauth' => 'chargePreauth',
        '/api/orders/refund' => 'refundOrder',
        '/api/orders/preauthRelease' => 'preauthRelease',
        '/api/orders/capture' => 'capturePayment'

    ]
];

// Get the request URI and method
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query strings if any
$requestUri = strtok($requestUri, '?');

// Check if route exists
if (!isset($routes[$requestMethod][$requestUri])) {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid API route']);
    exit;
}

// Read JSON input
$inputData = json_decode(file_get_contents("php://input"), true);

// Validate JSON input
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload']);
    exit;
}

// Route handling
$response = null;

switch ($routes[$requestMethod][$requestUri]) {
    case 'createOrder':
        if (!isset($inputData['amount'], $inputData['currency'], $inputData['customer'],$inputData['capture_mode'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
        $revolut = new createOrder();
        $response = $revolut->createNewOrder(
            $inputData['amount'],
            $inputData['currency'],
            $inputData['customer'],
            $inputData['capture_mode']
        );
        break;

    case 'preauth': 
        if (!isset($inputData['amount'], $inputData['currency'], $inputData['customer'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
        $preauthRevolut = new RevolutPreAuth();
        $response = $preauthRevolut->createPreAuthOrder(
            $inputData['amount'],
            $inputData['currency'],
            $inputData['customer'],
            $inputData['description'] ?? '',
            $inputData['redirect_url'] ?? ''
        );
        break;

        case 'capturePayment':
            if (!isset($inputData['order_id'], $inputData['amount'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }
            $capture = new RevolutCapture();
            $response = $capture->capturePayment(
                $inputData['order_id'],
                (int)$inputData['amount']
            );
            break;

    case 'refundOrder': 
        if (!isset($inputData['order_id'], $inputData['amount'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
        $refundApi = new refundApi();
        $response = $refundApi->issueRefund(
            $inputData['order_id'],
            $inputData['amount'],
            $inputData['description'] ?? 'Customer request'
        );
        break;

    case 'preauthRelease': 
        if (!isset($inputData['order_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
        $releasePreauth = new ReleasePreauth();
        $response = $releasePreauth->releasePreauthPayment($inputData['order_id']);
        break;
}

// Ensure a valid response is returned
if (is_array($response) && isset($response['status_code'])) {
    http_response_code($response['status_code']);
    echo json_encode($response, JSON_PRETTY_PRINT);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Unexpected server error']);
}
?>
