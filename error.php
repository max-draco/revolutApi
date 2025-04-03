<?php
// Get error details from URL parameters
$message = $_GET['message'] ?? 'Your payment could not be processed.';
$code = $_GET['code'] ?? 'UNKNOWN_ERROR';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php
    $cssFiles = glob('dist/css/*.css');
    foreach ($cssFiles as $css) {
        echo '<link rel="stylesheet" href="' . $css . '">';
    }
    ?>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-times"></i>
        </div>
        <h1>Payment Failed</h1>
        <p>Your payment could not be processed. Please check your payment details and try again.</p>
        
        <div class="error-details">
            <h3>Error Details:</h3>
            <p><?php echo htmlspecialchars($message); ?></p>
            <p class="error-code">Error code: <?php echo htmlspecialchars($code); ?></p>
        </div>
        
        <div class="buttons">
            <a href="index.php" class="button button-secondary">Return to Dashboard</a>
            <a href="custform.php" class="button">Try Again</a>
        </div>
    </div>
    
    <?php
    $jsFiles = glob('dist/js/*.js');
    foreach ($jsFiles as $js) {
        echo '<script src="' . $js . '"></script>';
    }
    ?>
</body>
</html>

