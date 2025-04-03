<?php
$orderId = $_GET['orderId'] ?? 'N/A';
$amount = isset($_GET['amount']) ? number_format($_GET['amount'] / 100, 2) : 'N/A';
$currency = $_GET['currency'] ?? 'GBP';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php
    $cssFiles = glob('dist/css/*.css');
    foreach ($cssFiles as $css) {
        echo '<link rel="stylesheet" href="' . $css . '">';
    }
    ?>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h1>Payment Successful!</h1>
        <p>Your payment has been processed successfully. Thank you for your purchase.</p>
        
        <div class="receipt-number">
            Receipt #: <?php echo substr(md5($orderId), 0, 8); ?>
        </div>
        
        <div class="order-details">
            <div class="detail-row">
                <span>Order ID:</span>
                <span><?php echo htmlspecialchars($orderId); ?></span>
            </div>
            <div class="detail-row">
                <span>Amount:</span>
                <span><?php echo htmlspecialchars($currency . ' ' . $amount); ?></span>
            </div>
            <div class="detail-row">
                <span>Date:</span>
                <span><?php echo date('Y-m-d H:i:s'); ?></span>
            </div>
        </div>
        
        <a href="index.php" class="button">Return to Dashboard</a>
    </div>
    
    <?php
    $jsFiles = glob('dist/js/*.js');
    foreach ($jsFiles as $js) {
        echo '<script src="' . $js . '"></script>';
    }
    ?>
</body>
</html>

