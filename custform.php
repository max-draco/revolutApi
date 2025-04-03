<?php
// This file serves as a PHP wrapper around the HTML template
// It will include the bundled assets from webpack
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Information</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php
    // Include the bundled CSS
    $cssFiles = glob('dist/css/*.css');
    foreach ($cssFiles as $css) {
        echo '<link rel="stylesheet" href="' . $css . '">';
    }
    ?>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Information</h1>
            <p>Enter your details to complete the payment</p>
        </div>
        
        <form action="payment.php" method="post">
            <!-- Reservation and Item Information -->
            <div class="form-group">
                <label for="resnumber">Reservation Number</label>
                <input type="text" id="resnumber" name="resnumber" value="RC87687IW" required>
            </div>
            
            <div class="form-group">
                <label for="itemnumber">Item Number</label>
                <input type="text" id="itemnumber" name="itemnumber" value="CDAR7869879" required>
            </div>
            
            <!-- Personal Information -->
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" value="Richard" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" value="Little" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="customer@example.com" required>
            </div>
            
            <!-- Payment Information -->
            <div class="form-group">
                <label for="cardnop">Card Number</label>
                <div class="input-wrapper">
                    <input type="text" id="cardnop" name="cardnop" value="1212121212121212" maxlength="16" required>
                    <div class="card-icon">
                        <i class="fab fa-cc-visa"></i>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="expmonth">Expiry Month</label>
                        <select id="expmonth" name="expmonth" required>
                            <?php for($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo sprintf('%02d', $i); ?>" <?php echo $i == 6 ? 'selected' : ''; ?>>
                                    <?php echo sprintf('%02d', $i); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="expyear">Expiry Year</label>
                        <select id="expyear" name="expyear" required>
                            <?php 
                                $currentYear = date('Y');
                                for($i = $currentYear; $i <= $currentYear + 10; $i++): 
                            ?>
                                <option value="<?php echo $i; ?>" <?php echo $i == 2026 ? 'selected' : ''; ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" value="123" maxlength="4" required>
                    </div>
                </div>
            </div>
            
            <!-- Amount Information -->
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" id="amount" name="amount" value="10.00" min="0.01" step="0.01" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <select id="currency" name="currency" required>
                            <option value="GBP" selected>GBP</option>
                            <option value="EUR">EUR</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="button">Continue to Payment</button>
            
            <div class="secure-badge">
                <i class="fas fa-lock"></i> Your payment information is secure
            </div>
        </form>
    </div>
    
    <?php
    // Include the bundled JS
    $jsFiles = glob('dist/js/*.js');
    foreach ($jsFiles as $js) {
        echo '<script src="' . $js . '"></script>';
    }
    ?>
</body>
</html>

