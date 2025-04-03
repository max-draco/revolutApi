
document.addEventListener("DOMContentLoaded", () => {
    const payButton = document.getElementById("pay-button")
    if (!payButton) return
  
    payButton.addEventListener("click", () => {
      const buttonText = document.getElementById("button-text")
      const messageDiv = document.getElementById("message")
      buttonText.innerHTML = '<span class="loader"></span> Processing...'
      payButton.disabled = true

      const publicId = payButton.getAttribute("data-public-id")
  
      window
        .RevolutCheckout(publicId, "sandbox")
        .then((instance) => {
          instance.payWithPopup({
            card: {
              cardNumber: payButton.getAttribute("data-card-number") || "",
              expiryMonth: payButton.getAttribute("data-expiry-month") || "",
              expiryYear: payButton.getAttribute("data-expiry-year") || "",
              cvv: payButton.getAttribute("data-cvv") || "",
            },
            savePaymentMethodFor: "merchant",
            onSuccess() {
              console.log("Payment successful")
              messageDiv.className = "success"
              messageDiv.textContent = "Payment successful! Redirecting..."
              messageDiv.style.display = "block"
              setTimeout(() => {
                window.location.href = "success.php" + window.location.search
              }, 2000)
            },
            onError(error) {
              console.error("Payment error:", error)
              buttonText.textContent = "Complete Payment"
              payButton.disabled = false
  
              messageDiv.className = "error"
              messageDiv.textContent = "Payment error: " + error.message
              messageDiv.style.display = "block"
            },
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
    const cancelButton = document.getElementById("cancel-button")
    if (cancelButton) {
      cancelButton.addEventListener("click", () => {
        window.history.back()
      })
    }
  })
  
  