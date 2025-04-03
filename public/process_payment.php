<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

$apiType = $_POST['api_type'] ?? '';
$requiredFields = ['orderNumber', 'amount', 'currency'];
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        echo json_encode([
            'success' => false,
            'message' => "Missing required field: $field"
        ]);
        exit;
    }
}

$amount = round(floatval($_POST['amount']) * 100);
$endpoint = '';
$requestData = [];

switch ($apiType) {
    case 'createOrder':
        $endpoint = '/api/orders';
        $requestData = [
            'amount' => $amount,
            'currency' => $_POST['currency'],
            'capture_mode' => $_POST['capture_mode'] ?? 'AUTOMATIC',
            'customer' => [
                'email' => $_POST['customerEmail'] ?? 'customer@example.com',
                'firstName' => $_POST['firstName'] ?? 'Richard',
                'lastName' => $_POST['lastName'] ?? 'Little'
            ],
            'order_id' => $_POST['orderNumber'],
            'description' => 'Payment for ' . $_POST['productCode'],
            'merchant_order_ext_ref' => $_POST['orderNumber'],
            'settlement_currency' => $_POST['currency'],
            'payment_method_types' => ['CARD']
        ];
        break;
        
    case 'preauth':
        $endpoint = '/api/orders/preauth';
        $requestData = [
            'amount' => $amount,
            'currency' => $_POST['currency'],
            'customer' => [
                'email' => $_POST['customerEmail'] ?? 'customer@example.com',
                'firstName' => $_POST['firstName'] ?? 'Richard',
                'lastName' => $_POST['lastName'] ?? 'Little'
            ],
            'description' => 'Pre-auth for ' . $_POST['productCode'],
            'order_id' => $_POST['orderNumber'],
            'redirect_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/success.php'
        ];
        break;
        
    case 'chargePreauth':
        $endpoint = '/api/orders/charge-preauth';
        $requestData = [
            'order_id' => $_POST['orderNumber'],
            'amount' => $amount
        ];
        break;
        
    case 'refundOrder':
        $endpoint = '/api/orders/refund';
        $requestData = [
            'order_id' => $_POST['orderNumber'],
            'amount' => $amount,
            'description' => 'Refund for ' . $_POST['productCode']
        ];
        break;
        
    case 'preauthRelease':
        $endpoint = '/api/orders/preauthRelease';
        $requestData = [
            'order_id' => $_POST['orderNumber']
        ];
        break;
        
    case 'capturePayment':
        $endpoint = '/api/orders/capture';
        $requestData = [
            'order_id' => $_POST['orderNumber'],
            'amount' => $amount
        ];
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid API type'
        ]);
        exit;
}

$apiUrl = 'https://pay.muslih.tech' . $endpoint;

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode([
        'success' => false,
        'message' => 'cURL error: ' . curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

$responseData = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON response from API',
        'raw_response' => $response
    ]);
    exit;
}
$success = $httpCode >= 200 && $httpCode < 300;

$result = [
    'success' => $success,
    'status_code' => $httpCode
];

if ($success && ($apiType === 'createOrder' || $apiType === 'preauth')) {
    $publicId = $responseData['public_id'] ?? $responseData['token'] ?? null;
    
    if ($publicId) {
        $result['public_id'] = $publicId;
    }
}

$result['response'] = $responseData;
echo json_encode($result);

