<?php
// Get card details from POST or set defaults for testing
$reservationNumber = $_POST["resnumber"] ?? 'RC87687IW';
$itemNumber = $_POST["itemnumber"] ?? 'CDAR7869879';
$firstName = $_POST["firstname"] ?? 'Richard';
$lastName = $_POST["lastname"] ?? 'Little';
$cardNumber = $_POST["cardnop"] ?? '1212121212121212';
$expMonth = $_POST["expmonth"] ?? '06';
$expYear = $_POST["expyear"] ?? '2026';
$cvv = $_POST["cvv"] ?? '123';
$amount = $_POST["amount"] ?? '10.00';
$currency = $_POST["currency"] ?? 'GBP';
$email = $_POST["email"] ?? 'customer@example.com';

// Convert amount to pennies for Revolut API
$amountInPennies = round(floatval($amount));

// Create a unique order ID if not provided
$orderId = $_POST["order_id"] ?? 'ORD-' . time() . '-' . rand(100, 999);

// Prepare the request payload for Revolut API - using the exact format provided
$requestPayload = [
    'amount' => $amountInPennies,
    'currency' => $currency,
    'customer' => '5322cbc3-0add-4b52-8ce1-a3f4f66ad045', // Using the customer ID provided
    'capture_mode' => 'manual'
];

// For debugging purposes
$debug = false;

if (!$debug) {
    // Make the API request to pay.muslih.tech/api/orders
    $ch = curl_init('https://pay.muslih.tech/api/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestPayload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        header("Location: error.php?message=" . urlencode("cURL Error: $error") . "&code=CONNECTION_ERROR");
        exit;
    }
    
    curl_close($ch);
    
    // Parse the response
    $responseData = json_decode($response, true);
    
    // Check if we have a token for the Revolut checkout (based on the sample response)
    if ($httpCode >= 200 && $httpCode < 300 && isset($responseData['response']['token'])) {
        $token = $responseData['response']['token'];
        $paymentId = $responseData['response']['id'] ?? '';
    } else {
        // Handle API error
        $errorMessage = $responseData['message'] ?? 'Unknown error occurred';
        header("Location: error.php?message=" . urlencode($errorMessage) . "&code=" . urlencode($responseData['code'] ?? 'API_ERROR'));
        exit;
    }
} else {
    // For debugging - simulate a successful response
    $token = 'test_token';
    $paymentId = 'test-payment-id';
}

// Format card number for display (mask all but last 4 digits)
$formattedCard = chunk_split(str_replace(' ', '', $cardNumber), 4, ' ');
$maskedCard = preg_replace('/\d(?=\d{4})/', '*', $formattedCard);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php
    // Include the bundled CSS - updated path for webpack
    $cssFiles = glob('assets/css/*.css');
    foreach ($cssFiles as $css) {
        echo '<link rel="stylesheet" href="/' . $css . '">';
    }
    ?>
    <!-- Add official Revolut Checkout embed script -->
    <script>
        !function(e,o,t){var n={sandbox:"https://sandbox-merchant.revolut.com/embed.js",prod:"https://merchant.revolut.com/embed.js"},r={sandbox:"https://sandbox-merchant.revolut.com/upsell/embed.js",prod:"https://merchant.revolut.com/upsell/embed.js"},l=function(e){var n=function(e){var t=o.createElement("script");return t.id="revolut-checkout",t.src=e,t.async=!0,o.head.appendChild(t),t}(e);return new Promise((function(e,r){n.onload=()=> e(),n.onerror=()=> {o.head.removeChild(n),r(new Error(t+" failed to load"))}}))},u=function(){if(window.RevolutCheckout===i||!window.RevolutCheckout)throw new Error(t+" failed to load")},c={},d={},i=function o(r,d){return c[d=d||"prod"]?Promise.resolve(c[d](r)):l(n[d]).then((function(){return u(),c[d]=window.RevolutCheckout,e[t]=o,c[d](r)}))};i.payments=function(o){var r=o.mode||"prod",d={locale:o.locale||"auto",publicToken:o.publicToken||null};return c[r]?Promise.resolve(c[r].payments(d)):l(n[r]).then((function(){return u(),c[r]=window.RevolutCheckout,e[t]=i,c[r].payments(d)}))},i.upsell=function(e){var o=e.mode||"prod",n={locale:e.locale||"auto",publicToken:e.publicToken||null};return d[o]?Promise.resolve(d[o](n)):l(r[o]).then((function(){if(!window.RevolutUpsell)throw new Error(t+" failed to load");return d[o]=window.RevolutUpsell,delete window.RevolutUpsell,d[o](n)}))},e[t]=i}(window,document,"RevolutCheckout");
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Complete Your Payment</h1>
            <p>Please review your order details before proceeding</p>
        </div>
        
        <!-- Card Display -->
        <div class="card-display">
            <div class="card-brand">
                <i class="fab fa-cc-visa"></i>
            </div>
            <div class="card-chip"></div>
            <div class="card-number-display">
                <?php echo trim($maskedCard); ?>
            </div>
            <div class="card-holder">Card Holder</div>
            <div class="card-name"><?php echo htmlspecialchars("$firstName $lastName"); ?></div>
            <div class="card-expiry-display">
                <span class="card-expiry-label">Expires</span>
                <?php echo htmlspecialchars("$expMonth/$expYear"); ?>
            </div>
        </div>
        
        <div class="order-details">
            <div class="detail-row">
                <span class="detail-label">Reservation:</span>
                <span class="detail-value"><?php echo htmlspecialchars($reservationNumber); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Item:</span>
                <span class="detail-value"><?php echo htmlspecialchars($itemNumber); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount:</span>
                <span class="detail-value"><?php echo htmlspecialchars("$currency $amount"); ?></span>
            </div>
        </div>
        
        <button id="pay-button" class="button">
            <span id="button-text">Complete Payment</span>
        </button>
        
        <button id="cancel-button" class="button button-secondary" style="margin-top: 1rem;">Cancel</button>
        
        <div id="message"></div>
    </div>
    
    <script>
    // Store card details in JavaScript variables for direct access
    const cardDetails = {
        token: "<?php echo htmlspecialchars($token); ?>",
        paymentId: "<?php echo htmlspecialchars($paymentId); ?>",
        cardNumber: "<?php echo htmlspecialchars($cardNumber); ?>",
        expiryMonth: "<?php echo htmlspecialchars($expMonth); ?>",
        expiryYear: "<?php echo htmlspecialchars($expYear); ?>",
        cvv: "<?php echo htmlspecialchars($cvv); ?>",
        amount: <?php echo $amountInPennies; ?>,
        currency: "<?php echo htmlspecialchars($currency); ?>"
    };
    
    // Inline script to handle the payment process
    document.addEventListener('DOMContentLoaded', function() {
        const payButton = document.getElementById('pay-button');
        const messageDiv = document.getElementById('message');
        
        if (!payButton) return;
        
        // Auto-trigger the payment process when the page loads
        setTimeout(function() {
            processPayment();
        }, 1000); // Short delay to ensure everything is loaded
        
        // Also allow manual triggering
        payButton.addEventListener('click', function() {
            processPayment();
        });
        
        function processPayment() {
            const buttonText = document.getElementById('button-text');
            
            // Show loading state
            buttonText.innerHTML = '<span class="loader"></span> Processing...';
            payButton.disabled = true;
            
            console.log("Initializing payment with token:", cardDetails.token);
            console.log("Card details to be passed:", {
                cardNumber: cardDetails.cardNumber,
                expiryMonth: cardDetails.expiryMonth,
                expiryYear: cardDetails.expiryYear,
                cvv: cardDetails.cvv
            });
            
            // Initialize Revolut Checkout with the token
            window.RevolutCheckout(cardDetails.token, 'sandbox')
                .then(function(instance) {
                    console.log("Revolut instance created successfully");
                    
                    // Open the payment popup with card details
                    instance.payWithPopup({
                        // Pass card details to Revolut
                        prefill: {
                            cardNumber: cardDetails.cardNumber,
                            expiryMonth: cardDetails.expiryMonth,
                            expiryYear: cardDetails.expiryYear,
                            cvv: cardDetails.cvv
                        },
                        // Save payment method for future
                        savePaymentMethodFor: 'merchant',
                        // Handle successful payment
                        onSuccess() {
                            console.log('Payment successful');
                            messageDiv.className = 'success';
                            messageDiv.textContent = 'Payment successful! Redirecting...';
                            messageDiv.style.display = 'block';
                            
                            // Redirect to success page with payment ID
                            setTimeout(function() {
                                window.location.href = 'success.php?orderId=' + 
                                    encodeURIComponent(cardDetails.paymentId) + 
                                    '&amount=' + encodeURIComponent(cardDetails.amount) + 
                                    '&currency=' + encodeURIComponent(cardDetails.currency);
                            }, 2000);
                        },
                        // Handle payment errors
                        onError(error) {
                            console.error('Payment error:', error);
                            buttonText.textContent = 'Complete Payment';
                            payButton.disabled = false;
                            
                            messageDiv.className = 'error';
                            messageDiv.textContent = 'Payment error: ' + error.message;
                            messageDiv.style.display = 'block';
                        },
                        // Handle user cancellation
                        onCancel() {
                            console.log('Payment cancelled');
                            buttonText.textContent = 'Complete Payment';
                            payButton.disabled = false;
                            
                            messageDiv.className = 'error';
                            messageDiv.textContent = 'Payment was cancelled.';
                            messageDiv.style.display = 'block';
                        }
                    });
                })
                .catch(function(error) {
                    console.error('Failed to create Revolut Checkout instance:', error);
                    buttonText.textContent = 'Complete Payment';
                    payButton.disabled = false;
                    
                    messageDiv.className = 'error';
                    messageDiv.textContent = 'Failed to initialize payment: ' + error.message;
                    messageDiv.style.display = 'block';
                });
        }
        
        // Handle cancel button click - updated to go to custform.html
        const cancelButton = document.getElementById('cancel-button');
        if (cancelButton) {
            cancelButton.addEventListener('click', function() {
                window.location.href = '/custform.html';
            });
        }
    });
    </script>
    
    <?php
    // Include the bundled JS - updated path for webpack
    $jsFiles = glob('assets/js/*.js');
    foreach ($jsFiles as $js) {
        echo '<script src="/' . $js . '">';
    }
    ?>
</body>
</html>