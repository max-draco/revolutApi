document.addEventListener("DOMContentLoaded", () => {
    generateOrderNumber()
    generateProductCode()
  
    const apiCards = document.querySelectorAll(".api-card")
    const paymentModal = document.getElementById("payment-modal")
    const modalTitle = document.getElementById("modal-title")
  
    apiCards.forEach((card) => {
      card.addEventListener("click", function (e) {
        if (e.target.tagName !== "BUTTON") return
  
        const apiType = this.getAttribute("data-api")
        modalTitle.textContent = this.querySelector("h3").textContent
  
        document.getElementById("payment-form").setAttribute("data-api", apiType)
  
        paymentModal.classList.add("active")
      })
    })
  
    document.querySelectorAll(".close-modal").forEach((btn) => {
      btn.addEventListener("click", () => {
        paymentModal.classList.remove("active")
      })
    })
  
    window.addEventListener("click", (e) => {
      if (e.target === paymentModal) {
        paymentModal.classList.remove("active")
      }
    })
  
    document.getElementById("payment-form").addEventListener("submit", function (e) {
      e.preventDefault()
      processPayment(this.getAttribute("data-api"))
    })
  
    document.getElementById("copyResponse").addEventListener("click", function () {
      const responseOutput = document.getElementById("responseOutput")
      navigator.clipboard
        .writeText(responseOutput.textContent)
        .then(() => {
          this.innerHTML = '<i class="fas fa-check"></i>'
          setTimeout(() => {
            this.innerHTML = '<i class="fas fa-copy"></i>'
          }, 2000)
        })
        .catch((err) => {
          console.error("Failed to copy: ", err)
        })
    })
  
    document.getElementById("clearResponse").addEventListener("click", () => {
      document.getElementById("responseOutput").textContent = "No response yet. Test a payment to see results here."
    })
  })
  
  function generateOrderNumber() {
    const prefix = "ORD"
    const timestamp = new Date().getTime().toString().slice(-6)
    const random = Math.floor(Math.random() * 1000)
      .toString()
      .padStart(3, "0")
    const orderNumber = `${prefix}-${timestamp}-${random}`
    document.getElementById("orderNumber").value = orderNumber
  }
  
  function generateProductCode() {
    const prefixes = ["PROD", "ITEM", "SKU"]
    const prefix = prefixes[Math.floor(Math.random() * prefixes.length)]
    const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
    const randomLetters =
      letters.charAt(Math.floor(Math.random() * letters.length)) +
      letters.charAt(Math.floor(Math.random() * letters.length))
    const numbers = Math.floor(Math.random() * 10000)
      .toString()
      .padStart(4, "0")
    const productCode = `${prefix}-${randomLetters}${numbers}`
    document.getElementById("productCode").value = productCode
  }
  
  function validateForm() {
    let isValid = true
  
    const amount = document.getElementById("amount")
    if (!amount.value || Number.parseFloat(amount.value) <= 0) {
      amount.classList.add("error")
      isValid = false
    } else {
      amount.classList.remove("error")
    }
  
    return isValid
  }
  
  async function processPayment(apiType) {
    if (!validateForm()) {
      return
    }
  
    const orderNumber = document.getElementById("orderNumber").value
    const productCode = document.getElementById("productCode").value
    const amount = document.getElementById("amount").value
    const currency = document.getElementById("currency").value
    const captureMode = document.getElementById("captureMode").value
    const customerEmail = document.getElementById("customerEmail").value
  
    const amountInPennies = Math.round(Number.parseFloat(amount) * 100)
  
    const submitButton = document.getElementById("submit-button")
    submitButton.disabled = true
  
    const processingOverlay = document.getElementById("processing-overlay")
    processingOverlay.style.display = "flex"
  
    const responseOutput = document.getElementById("responseOutput")
  
    const endpoints = {
      createOrder: "/api/orders",
      preauth: "/api/orders/preauth",
      chargePreauth: "/api/orders/charge-preauth",
      refundOrder: "/api/orders/refund",
      preauthRelease: "/api/orders/preauthRelease",
      capturePayment: "/api/orders/capture",
    }
  
    try {
      let requestPayload = {}
  
      switch (apiType) {
        case "createOrder":
          requestPayload = {
            amount: amountInPennies,
            currency: currency,
            customer: {
              email: customerEmail,
              firstName: "Richard",
              lastName: "Little",
            },
            capture_mode: captureMode,
            order_id: orderNumber,
            description: `Payment for ${productCode}`,
          }
          break
  
        case "preauth":
          requestPayload = {
            amount: amountInPennies,
            currency: currency,
            customer: {
              email: customerEmail,
              firstName: "Richard",
              lastName: "Little",
            },
            description: `Pre-auth for ${productCode}`,
            redirect_url: window.location.origin + "/success.html",
          }
          break
  
        case "chargePreauth":
          requestPayload = {
            order_id: orderNumber,
            amount: amountInPennies,
          }
          break
  
        case "refundOrder":
          requestPayload = {
            order_id: orderNumber,
            amount: amountInPennies,
            description: "Customer request",
          }
          break
  
        case "preauthRelease":
          requestPayload = {
            order_id: orderNumber,
          }
          break
  
        case "capturePayment":
          requestPayload = {
            order_id: orderNumber,
            amount: amountInPennies,
          }
          break
  
        default:
          throw new Error("Unknown API type")
      }
  
      const response = await fetch(`https://pay.muslih.tech${endpoints[apiType]}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(requestPayload),
      })
  
      const data = await response.json()
  
      processingOverlay.style.display = "none"
  
      responseOutput.textContent = JSON.stringify(data, null, 2)
  
      document.getElementById("payment-modal").classList.remove("active")
  
      if (response.ok && (apiType === "createOrder" || apiType === "preauth") && data.public_id) {
        openRevolutCheckout(data.public_id)
      }
    } catch (error) {
      console.error("Error processing payment:", error)
  
      processingOverlay.style.display = "none"
  
      responseOutput.textContent = `Error: ${error.message || "An unexpected error occurred"}`
  
      document.getElementById("payment-modal").classList.remove("active")
    } finally {
      submitButton.disabled = false
    }
  }
  

  let RevolutCheckout
  
  function openRevolutCheckout(publicId) {
    try {
      RevolutCheckout(publicId, "sandbox")
        .then((instance) => {
          instance.payWithPopup({
            onSuccess() {
              console.log("Payment completed successfully")
              document.getElementById("responseOutput").textContent += "\n\nPayment completed successfully!"
            },
            onError(error) {
              console.error("Payment error:", error)
              document.getElementById("responseOutput").textContent +=
                `\n\nPayment error: ${error.message || "Unknown error"}`
            },
            onCancel() {
              console.log("Payment was cancelled by user")
              document.getElementById("responseOutput").textContent += "\n\nPayment was cancelled by user."
            },
          })
        })
        .catch((error) => {
          console.error("Revolut Checkout initialization error:", error)
          document.getElementById("responseOutput").textContent +=
            `\n\nCheckout initialization error: ${error.message || "Unknown error"}`
        })
    } catch (error) {
      console.error("Error initializing Revolut Checkout:", error)
      document.getElementById("responseOutput").textContent +=
        `\n\nError initializing checkout: ${error.message || "Unknown error"}`
    }
  }
  
  