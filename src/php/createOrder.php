<?php
require_once 'config.php';

class createOrder {
    private $apiUrl;
    private $apiKey;
    public function __construct() {
        // Load API URL and Key from environment variables
        $this->apiUrl = getenv('REVOLUT_API_BASE_URL') ?: ($_ENV['REVOLUT_API_BASE_URL'] ?? REVOLUT_API_BASE_URL);
        $this->apiKey = getenv('REVOLUT_API_KEY') ?: ($_ENV['REVOLUT_API_KEY'] ?? '');
    
        // Ensure values are properly trimmed
        $this->apiUrl = rtrim($this->apiUrl, '/');
        $this->apiKey = trim($this->apiKey);
    
        // Debugging: Log API Key presence (DO NOT print the key in production!)
        if (empty($this->apiUrl) || empty($this->apiKey)) {
            error_log("⚠️ Error: API credentials missing. Check .env or server environment variables.");
            throw new Exception("API credentials are missing. Please check the server configuration.");
        }
    }
    
    private function sendRequest($endpoint, $method, $data = []) {
        $url = "{$this->apiUrl}/$endpoint"; // Construct full API URL
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->apiKey}", // Bearer token authentication
            "Content-Type: application/json",
            "Accept: application/json",
            "Revolut-Api-Version: 2024-09-01"
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'status_code' => 500,
                'error' => "cURL Error: " . $error
            ];
        }

        return [
            'status_code' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }

    public function createNewOrder($amount, $currency, $customer, $capture_mode) {
        if ($amount <= 0) {
            return ['error' => 'Invalid amount. It must be greater than zero.'];
        }
       

        $data = [
            'amount' => $amount,
            'currency' => strtoupper($currency),
            'customer' => $customer,
            'capture_mode' => $capture_mode, // Enables pre-authorization
        ];

        return $this->sendRequest('api/orders', 'POST', $data);
    }
}

// Example Usage (this will not execute unless explicitly called)
$revolutOrder = new createOrder();
$response = $revolutOrder->createNewOrder(500, 'GBP', 'ORDER12345', 'customer@example.com');

// Uncomment to test
// echo json_encode($response, JSON_PRETTY_PRINT);
?>
