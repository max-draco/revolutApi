<?php
function generateRandomString($length = 10) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$defaultOrderNumber = 'ORD-' . time() . '-' . rand(100, 999);
$defaultProductCode = 'PROD-' . generateRandomString(6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revolut Payment</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        !function(e,o,t){var n={sandbox:"https://sandbox-merchant.revolut.com/embed.js",prod:"https://merchant.revolut.com/embed.js"},r={sandbox:"https://sandbox-merchant.revolut.com/upsell/embed.js",prod:"https://merchant.revolut.com/upsell/embed.js"},l=function(e){var n=function(e){var t=o.createElement("script");return t.id="revolut-checkout",t.src=e,t.async=!0,o.head.appendChild(t),t}(e);return new Promise((function(e,r){n.onload=()=> e(),n.onerror=()=> {o.head.removeChild(n),r(new Error(t+" failed to load"))}}))},u=function(){if(window.RevolutCheckout===i||!window.RevolutCheckout)throw new Error(t+" failed to load")},c={},d={},i=function o(r,d){return c[d=d||"prod"]?Promise.resolve(c[d](r)):l(n[d]).then((function(){return u(),c[d]=window.RevolutCheckout,e[t]=o,c[d](r)}))};i.payments=function(o){var r=o.mode||"prod",d={locale:o.locale||"auto",publicToken:o.publicToken||null};return c[r]?Promise.resolve(c[r].payments(d)):l(n[r]).then((function(){return u(),c[r]=window.RevolutCheckout,e[t]=i,c[r].payments(d)}))},i.upsell=function(e){var o=e.mode||"prod",n={locale:e.locale||"auto",publicToken:e.publicToken||null};return d[o]?Promise.resolve(d[o](n)):l(r[o]).then((function(){if(!window.RevolutUpsell)throw new Error(t+" failed to load");return d[o]=window.RevolutUpsell,delete window.RevolutUpsell,d[o](n)}))},e[t]=i}(window,document,"RevolutCheckout");
    </script>
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <h1>Revolut Payment API Testing</h1>
        </div>
        
        <div class="api-cards">
            <div class="api-card" data-api="createOrder">
                <div class="api-card-header">
                    <i class="fas fa-plus-circle"></i>
                    <h3>Create Order</h3>
                </div>
                <p>Create a new payment order</p>
                <button class="btn-primary">Test Payment</button>
            </div>

            <div class="api-card" data-api="preauth">
                <div class="api-card-header">
                    <i class="fas fa-credit-card"></i>
                    <h3>Pre-authorize Payment</h3>
                </div>
                <p>Reserve funds without capturing</p>
                <button class="btn-primary">Test Payment</button>
            </div>

            <div class="api-card" data-api="chargePreauth">
                <div class="api-card-header">
                    <i class="fas fa-money-check"></i>
                    <h3>Charge Pre-auth</h3>
                </div>
                <p>Capture funds from pre-authorization</p>
                <button class="btn-primary">Test Payment</button>
            </div>

            <div class="api-card" data-api="refundOrder">
                <div class="api-card-header">
                    <i class="fas fa-undo"></i>
                    <h3>Refund Payment</h3>
                </div>
                <p>Process a refund for an order</p>
                <button class="btn-primary">Test Payment</button>
            </div>

            <div class="api-card" data-api="preauthRelease">
                <div class="api-card-header">
                    <i class="fas fa-unlock"></i>
                    <h3>Release Pre-auth</h3>
                </div>
                <p>Release reserved funds</p>
                <button class="btn-primary">Test Payment</button>
            </div>

            <div class="api-card" data-api="capturePayment">
                <div class="api-card-header">
                    <i class="fas fa-hand-holding-usd"></i>
                    <h3>Capture Payment</h3>
                </div>
                <p>Capture authorized payment</p>
                <button class="btn-primary">Test Payment</button>
            </div>
        </div>

        <div class="response-container">
            <div class="response-header">
                <h3>API Response</h3>
                <div class="response-actions">
                    <button id="copyResponse" class="btn-icon" title="Copy to clipboard">
                        <i class="fas fa-copy"></i>
                    </button>
                    <button id="clearResponse" class="btn-icon" title="Clear response">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <pre id="responseOutput" class="response-output">No response yet. Test a payment to see results here.</pre>
        </div>
    </div>

    <div id="payment-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-title">Make a Payment</h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <form id="payment-form" method="post" action="process_payment.php">
                    <input type="hidden" id="api_type" name="api_type" value="">
                    
                    <div class="form-group">
                        <label for="orderNumber">Order Number:</label>
                        <div class="input-with-icon">
                            <input type="text" id="orderNumber" name="orderNumber" required value="<?php echo $defaultOrderNumber; ?>" readonly>
                            <div class="refresh-icon" onclick="generateOrderNumber()">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="productCode">Product Code:</label>
                        <div class="input-with-icon">
                            <input type="text" id="productCode" name="productCode" required value="<?php echo $defaultProductCode; ?>" readonly>
                            <div class="refresh-icon" onclick="generateProductCode()">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount (£):</label>
                        <input type="number" id="amount" name="amount" min="0.01" step="0.01" required value="10.00">
                    </div>

                    <div class="form-group">
                        <label for="currency">Currency:</label>
                        <select id="currency" name="currency" class="form-control" required>
                            <option value="GBP">GBP</option>
                            <option value="EUR">EUR</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="captureMode">Capture Mode:</label>
                        <select id="captureMode" name="capture_mode" class="form-control" required>
                            <option value="AUTOMATIC">Automatic</option>
                            <option value="MANUAL">Manual</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="customerEmail">Customer Email:</label>
                        <input type="email" id="customerEmail" name="customerEmail" required value="customer@example.com">
                    </div>

                    <div class="form-group">
                        <label for="firstName">First Name:</label>
                        <input type="text" id="firstName" name="firstName" required value="Richard">
                    </div>

                    <div class="form-group">
                        <label for="lastName">Last Name:</label>
                        <input type="text" id="lastName" name="lastName" required value="Little">
                    </div>

                    <button type="submit" id="submit-button" class="btn-primary">Process Payment</button>
                </form>
            </div>
        </div>
    </div>

    <div id="processing-overlay" class="processing-overlay">
        <div class="loader"></div>
        <div>Processing your payment...</div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const apiCards = document.querySelectorAll('.api-card');
        const paymentModal = document.getElementById('payment-modal');
        const modalTitle = document.getElementById('modal-title');
        
        apiCards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'BUTTON') return;
                
                const apiType = this.getAttribute('data-api');
                modalTitle.textContent = this.querySelector('h3').textContent;
                
                document.getElementById('payment-form').setAttribute('data-api', apiType);
                document.getElementById('api_type').value = apiType;
                
                paymentModal.classList.add('active');
            });
        });
        
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                paymentModal.classList.remove('active');
            });
        });
        
        window.addEventListener('click', function(e) {
            if (e.target === paymentModal) {
                paymentModal.classList.remove('active');
            }
        });
        
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            document.getElementById('processing-overlay').style.display = 'flex';
            
            const formData = new FormData(this);
            
            fetch('process_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('processing-overlay').style.display = 'none';
                
                document.getElementById('responseOutput').textContent = JSON.stringify(data, null, 2);
                
                paymentModal.classList.remove('active');
                
                if (data.success && data.public_id) {
                    openRevolutCheckout(data.public_id);
                }
            })
            .catch(error => {
                document.getElementById('processing-overlay').style.display = 'none';
                
                document.getElementById('responseOutput').textContent = 'Error: ' + error.message;
                
                paymentModal.classList.remove('active');
            });
        });
        
        document.getElementById('copyResponse').addEventListener('click', function() {
            const responseOutput = document.getElementById('responseOutput');
            navigator.clipboard.writeText(responseOutput.textContent)
                .then(() => {
                    this.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-copy"></i>';
                    }, 2000);
                })
                .catch(err => {
                    console.error('Failed to copy: ', err);
                });
        });
        
        document.getElementById('clearResponse').addEventListener('click', function() {
            document.getElementById('responseOutput').textContent = 'No response yet. Test a payment to see results here.';
        });
    });

    function generateOrderNumber() {
        const prefix = "ORD";
        const timestamp = new Date().getTime().toString().slice(-6);
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        const orderNumber = `${prefix}-${timestamp}-${random}`;
        document.getElementById('orderNumber').value = orderNumber;
    }

    function generateProductCode() {
        const prefixes = ["PROD", "ITEM", "SKU"];
        const prefix = prefixes[Math.floor(Math.random() * prefixes.length)];
        const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const randomLetters = letters.charAt(Math.floor(Math.random() * letters.length)) + 
                             letters.charAt(Math.floor(Math.random() * letters.length));
        const numbers = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        const productCode = `${prefix}-${randomLetters}${numbers}`;
        document.getElementById('productCode').value = productCode;
    }

    function openRevolutCheckout(publicId) {
        RevolutCheckout(publicId, 'sandbox')
            .then(function(instance) {
                console.log('Revolut Checkout instance created');
                
                instance.payWithPopup({
                    savePaymentMethodFor: 'merchant',
                    onSuccess() {
                        console.log('Payment successful');
                        document.getElementById('responseOutput').textContent += '\n\nPayment completed successfully!';
                    },
                    onError(error) {
                        console.error('Payment error:', error);
                        document.getElementById('responseOutput').textContent += '\n\nPayment error: ' + error.message;
                    },
                    onCancel() {
                        console.log('Payment cancelled');
                        document.getElementById('responseOutput').textContent += '\n\nPayment was cancelled by user.';
                    }
                });
            })
            .catch(function(error) {
                console.error('Failed to create Revolut Checkout instance:', error);
                document.getElementById('responseOutput').textContent += '\n\nFailed to create checkout: ' + error.message;
            });
    }
    </script>
</body>
</html>

