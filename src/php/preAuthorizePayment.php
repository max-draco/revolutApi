<?php
require_once 'config.php'; // Ensure this loads .env variables properly

class RevolutPreAuth {
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

    public function createPreAuthOrder($amount, $currency, $customerEmail, $merchantOrderId, $description, $redirectUrl) {
        if ($amount <= 0) {
            return ['error' => 'Invalid amount. Must be greater than zero.'];
        }

        $data = [
            'amount' => $amount,                      // Amount in minor units (e.g., cents)
            'currency' => strtoupper($currency),     // ISO 4217 format (e.g., "USD", "GBP")
            'capture_mode' => 'manual',              // Enables pre-authorization
            'merchant_order_ext_ref' => $merchantOrderId, // Unique order ID for tracking
            'description' => $description,           // Short description
            'customer' => ['email' => $customerEmail], // Customer's email
            'redirect_url' => $redirectUrl,          // Redirect URL after payment
            'statement_descriptor_suffix' => 'PreAuth' // Appears in bank statement
        ];

        return $this->sendRequest('/api/orders', 'POST', $data);
    }
}


?>
