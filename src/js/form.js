
document.addEventListener("DOMContentLoaded", () => {
    const cardInput = document.getElementById("cardnop")
    if (!cardInput) return

    cardInput.addEventListener("input", (e) => {
      let value = e.target.value.replace(/\D/g, "")
  
      if (value.length > 16) {
        value = value.slice(0, 16)
      }
  
      e.target.value = value
  
      const cardIcon = document.querySelector(".card-icon i")
      if (!cardIcon) return
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
  
    const cvvInput = document.getElementById("cvv")
    if (cvvInput) {
      cvvInput.addEventListener("input", (e) => {
        let value = e.target.value.replace(/\D/g, "")
  
        if (value.length > 4) {
          value = value.slice(0, 4)
        }
        e.target.value = value
      })
    }
  })
  
  