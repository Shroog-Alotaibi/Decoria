function saveChanges(userID) {

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();

    const errorBox = document.getElementById("errorMessage");
    const errorText = document.getElementById("errorText");

    // إخفاء الرسالة أول
    errorBox.style.display = "none";

    // ============================
    //     VALIDATION
    // ============================

    if (name === "" || email === "" || phone === "") {
        return showError("⚠️ All fields are required");
    }

    const emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
    if (!emailPattern.test(email)) {
        return showError("📧 Email must be a valid Gmail address (example@gmail.com)");
    }

    const phonePattern = /^[0-9]{10}$/;
    if (!phonePattern.test(phone)) {
        return showError("📱 Phone number must be exactly 10 digits");
    }

    // ============================
    //        AJAX REQUEST
    // ============================

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "update-user.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {

        if (xhr.readyState === 4 && xhr.status === 200) {

            // إذا نجح التحديث
            if (xhr.responseText.trim() === "SUCCESS") {
                alert("Saved successfully ✔️");
            } 
            else {
                showError("❌ Error: " + xhr.responseText);
            }
        }
    };

    xhr.send(
        "userID=" + userID +
        "&name=" + encodeURIComponent(name) +
        "&email=" + encodeURIComponent(email) +
        "&phone=" + encodeURIComponent(phone)
    );
}


// ============================
//    SHOW ERROR FUNCTION
// ============================

function showError(msg) {
    const box = document.getElementById("errorMessage");
    const text = document.getElementById("errorText");

    text.textContent = msg;
    box.style.display = "block";

    setTimeout(() => {
        box.style.display = "none";
    }, 3000);
}
