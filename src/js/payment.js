// Payment processing logic
document.addEventListener("DOMContentLoaded", () => {
    // Check if we're on the payment page
    const payButton = document.getElementById("pay-button")
    if (!payButton) return
  
    payButton.addEventListener("click", () => {
      const buttonText = document.getElementById("button-text")
      const messageDiv = document.getElementById("message")
  
      // Show loading state
      buttonText.innerHTML = '<span class="loader"></span> Processing...'
      payButton.disabled = true
  
      // Get the public ID from the data attribute
      const publicId = payButton.getAttribute("data-public-id")
  
      // Initialize Revolut Checkout
      window
        .RevolutCheckout(publicId, "sandbox")
        .then((instance) => {
          // Open the payment popup
          instance.payWithPopup({
            // Try to pass card details to Revolut
            card: {
              cardNumber: payButton.getAttribute("data-card-number") || "",
              expiryMonth: payButton.getAttribute("data-expiry-month") || "",
              expiryYear: payButton.getAttribute("data-expiry-year") || "",
              cvv: payButton.getAttribute("data-cvv") || "",
            },
            // Save payment method for future
            savePaymentMethodFor: "merchant",
            // Handle successful payment
            onSuccess() {
              console.log("Payment successful")
              messageDiv.className = "success"
              messageDiv.textContent = "Payment successful! Redirecting..."
              messageDiv.style.display = "block"
  
              // Redirect to success page
              setTimeout(() => {
                window.location.href = "success.php" + window.location.search
              }, 2000)
            },
            // Handle payment errors
            onError(error) {
              console.error("Payment error:", error)
              buttonText.textContent = "Complete Payment"
              payButton.disabled = false
  
              messageDiv.className = "error"
              messageDiv.textContent = "Payment error: " + error.message
              messageDiv.style.display = "block"
            },
            // Handle user cancellation
            onCancel() {
              console.log("Payment cancelled")
              buttonText.textContent = "Complete Payment"
              payButton.disabled = false
  
              messageDiv.className = "error"
              messageDiv.textContent = "Payment was cancelled."
              messageDiv.style.display = "block"
            },
          })
        })
        .catch((error) => {
          console.error("Failed to create Revolut Checkout instance:", error)
          buttonText.textContent = "Complete Payment"
          payButton.disabled = false
  
          messageDiv.className = "error"
          messageDiv.textContent = "Failed to initialize payment: " + error.message
          messageDiv.style.display = "block"
        })
    })
  
    // Handle cancel button click
    const cancelButton = document.getElementById("cancel-button")
    if (cancelButton) {
      cancelButton.addEventListener("click", () => {
        window.history.back()
      })
    }
  })
  
  