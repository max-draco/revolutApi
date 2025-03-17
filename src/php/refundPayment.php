<?php
require_once 'config.php'; // Ensure this loads .env variables properly

class refundApi {
    private $apiUrl;
    private $apiKey;

    public function __construct() {
        // Load API credentials from environment variables
        $this->apiUrl = getenv('REVOLUT_API_BASE_URL') ?: ($_ENV['REVOLUT_API_BASE_URL'] ?? REVOLUT_API_BASE_URL);
        $this->apiKey = getenv('REVOLUT_API_KEY') ?: ($_ENV['REVOLUT_API_KEY'] ?? '');

        // Ensure values are properly formatted
        $this->apiUrl = rtrim($this->apiUrl, '/');
        $this->apiKey = trim($this->apiKey);

        // Validate credentials
        if (empty($this->apiUrl) || empty($this->apiKey)) {
            error_log("⚠️ Error: API credentials missing.");
            throw new Exception("API credentials are missing. Please check your environment settings.");
        }
    }

    private function sendRequest($endpoint, $method, $data = []) {
        $url = "{$this->apiUrl}$endpoint"; // Ensure correct API structure
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->apiKey}",
            "Content-Type: application/json",
            "Accept: application/json"
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

    public function issueRefund($orderId, $amount, $description) {
        if (empty($orderId)) {
            return ['error' => 'Order ID is required for refunds.'];
        }
        if ($amount <= 0) {
            return ['error' => 'Invalid refund amount. Must be greater than zero.'];
        }

        $data = [
            'amount' => $amount, // Amount in minor units (e.g., cents)
            'description' => $description
        ];

        return $this->sendRequest("/api/1.0/orders/{$orderId}/refund", 'POST', $data);
    }
}

?>
