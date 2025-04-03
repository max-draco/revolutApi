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
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --background: #f9fafb;
            --card-bg: #ffffff;
            --text: #1f2937;
            --text-light: #6b7280;
            --border: #e5e7eb;
            --success: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--background);
            color: var(--text);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }

        .success-container {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 480px;
            padding: 2rem;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        .success-icon {
            color: var(--success);
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        h1 {
            color: var(--success);
            margin-bottom: 1rem;
        }

        p {
            color: var(--text-light);
            margin-bottom: 1.5rem;
        }

        .order-details {
            background-color: var(--background);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        .detail-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: background-color 0.15s ease;
        }

        .button:hover {
            background-color: var(--primary-hover);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1>Payment Successful!</h1>
        <p>Your payment has been processed successfully. Thank you for your purchase.</p>
        
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
</body>
</html>

