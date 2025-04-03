// Form handling logic
document.addEventListener("DOMContentLoaded", () => {
    // Check if we're on the form page
    const cardInput = document.getElementById("cardnop")
    if (!cardInput) return
  
    // Card number formatting
    cardInput.addEventListener("input", (e) => {
      // Remove non-digits
      let value = e.target.value.replace(/\D/g, "")
  
      // Limit to 16 digits
      if (value.length > 16) {
        value = value.slice(0, 16)
      }
  
      // Update the input value
      e.target.value = value
  
      // Detect card type and update icon
      const cardIcon = document.querySelector(".card-icon i")
      if (!cardIcon) return
  
      // Simple card type detection based on first digit
      const firstDigit = value.charAt(0)
      if (firstDigit === "4") {
        cardIcon.className = "fab fa-cc-visa"
      } else if (firstDigit === "5") {
        cardIcon.className = "fab fa-cc-mastercard"
      } else if (firstDigit === "3") {
        cardIcon.className = "fab fa-cc-amex"
      } else if (firstDigit === "6") {
        cardIcon.className = "fab fa-cc-discover"
      } else {
        cardIcon.className = "far fa-credit-card"
      }
    })
  
    // CVV validation
    const cvvInput = document.getElementById("cvv")
    if (cvvInput) {
      cvvInput.addEventListener("input", (e) => {
        // Remove non-digits
        let value = e.target.value.replace(/\D/g, "")
  
        // Limit to 4 digits (for Amex) or 3 digits (for others)
        if (value.length > 4) {
          value = value.slice(0, 4)
        }
  
        // Update the input value
        e.target.value = value
      })
    }
  })
  
  