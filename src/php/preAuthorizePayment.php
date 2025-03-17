<?php
require_once 'config.php';

class RevolutPreAuth {
    private $apiUrl;
    private $apiKey;

    public function __construct() {
        $this->apiUrl = getenv('REVOLUT_API_BASE_URL') ?: ($_ENV['REVOLUT_API_BASE_URL'] ?? REVOLUT_API_BASE_URL);
        $this->apiKey = getenv('REVOLUT_API_KEY') ?: ($_ENV['REVOLUT_API_KEY'] ?? '');

        $this->apiUrl = rtrim($this->apiUrl, '/');
        $this->apiKey = trim($this->apiKey);
        if (empty($this->apiUrl) || empty($this->apiKey)) {
            error_log("⚠️ Error: API credentials missing.");
            throw new Exception("API credentials are missing. Please check your environment settings.");
        }
    }

    private function sendRequest($endpoint, $method, $data = []) {
        $url = "{$this->apiUrl}$endpoint";
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
            'amount' => $amount,                      
            'currency' => strtoupper($currency),     
            'capture_mode' => 'manual',            
            'merchant_order_ext_ref' => $merchantOrderId, 
            'description' => $description,           
            'customer' => ['email' => $customerEmail], 
            'redirect_url' => $redirectUrl,          
            'statement_descriptor_suffix' => 'PreAuth' 
        ];

        return $this->sendRequest('/api/orders', 'POST', $data);
    }
}


?>
