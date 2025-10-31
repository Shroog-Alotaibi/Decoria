const newPasswordInput = document.getElementById('new-password');
const confirmPasswordInput = document.getElementById('confirm-password');
const currentPasswordInput = document.getElementById('current-password');
const saveBtn = document.getElementById('saveBtn');
const passwordCriteria = document.getElementById('password-criteria');
const messageElement = document.getElementById('message');

const defaultPassword = "Ahmed@123872";

passwordCriteria.innerHTML = `
  <ul>
    <li id="upperCase" class="criteria-item">Must contain both uppercase and lowercase letters</li>
    <li id="numbers" class="criteria-item">Must contain numbers</li>
    <li id="symbols" class="criteria-item">Must contain special characters</li>
    <li id="length" class="criteria-item">Must be at least 12 characters long</li>
    <li id="unDecipherable" class="criteria-item">Must not be easily guessable</li>
  </ul>
`;

function validatePassword() {
  const password = newPasswordInput.value;
  const confirmPassword = confirmPasswordInput.value;
  let validationMessage = '';
  let allCriteriaMet = true;  

  const hasUpperCase = /[A-Z]/.test(password);
  const hasLowerCase = /[a-z]/.test(password);
  if (hasUpperCase && hasLowerCase) {
    document.getElementById('upperCase').classList.add('valid');
    document.getElementById('upperCase').classList.remove('invalid');
  } else {
    document.getElementById('upperCase').classList.add('invalid');
    document.getElementById('upperCase').classList.remove('valid');
    validationMessage += "Must contain both uppercase and lowercase letters.\n";
    allCriteriaMet = false;
  }

  const hasNumbers = /\d/.test(password);
  if (hasNumbers) {
    document.getElementById('numbers').classList.add('valid');
    document.getElementById('numbers').classList.remove('invalid');
  } else {
    document.getElementById('numbers').classList.add('invalid');
    document.getElementById('numbers').classList.remove('valid');
    validationMessage += "Must contain numbers.\n";
    allCriteriaMet = false;
  }

  const hasSymbols = /[!@#$%^&*(),.?":{}|<>]/.test(password);
  if (hasSymbols) {
    document.getElementById('symbols').classList.add('valid');
    document.getElementById('symbols').classList.remove('invalid');
  } else {
    document.getElementById('symbols').classList.add('invalid');
    document.getElementById('symbols').classList.remove('valid');
    validationMessage += "Must contain special characters.\n";
    allCriteriaMet = false;
  }

  const isLongEnough = password.length >= 12;
  if (isLongEnough) {
    document.getElementById('length').classList.add('valid');
    document.getElementById('length').classList.remove('invalid');
  } else {
    document.getElementById('length').classList.add('invalid');
    document.getElementById('length').classList.remove('valid');
    validationMessage += "Must be at least 12 characters long.\n";
    allCriteriaMet = false;
  }

  const commonPasswords = /password|12345|qwerty|abc123|welcome|letmein|password123|123123|iloveyou|monkey/i;
  const isDecipherable = commonPasswords.test(password); 
  if (isDecipherable) {
    document.getElementById('unDecipherable').classList.add('invalid');
    document.getElementById('unDecipherable').classList.remove('valid');
    validationMessage += "Must not be easily guessable.\n";
    allCriteriaMet = false;
  } else {
    document.getElementById('unDecipherable').classList.add('valid');
    document.getElementById('unDecipherable').classList.remove('invalid');
  }

  if (validationMessage) {
    messageElement.style.display = 'block';
    messageElement.textContent = validationMessage;
    messageElement.style.display = 'none';
  } else {
    messageElement.style.display = 'none'; 
  }

  return allCriteriaMet;
}

function saveChanges(event) {
  event.preventDefault(); 

  const currentPassword = currentPasswordInput.value;
  const newPassword = newPasswordInput.value;
  const confirmPassword = confirmPasswordInput.value;

  if (currentPassword !== defaultPassword) {
    messageElement.textContent = "The current password is incorrect";
    messageElement.style.backgroundColor = "#f8d7daa1"; // red color
    messageElement.style.color = "#721c24";
    messageElement.style.display = 'block';
    return;
  }

  if (!validatePassword()) {
    messageElement.style.display = 'none';  
    return;
  }

  if (newPassword !== confirmPassword) {
    messageElement.textContent = "The confirmation password does not match the input";
    messageElement.style.backgroundColor = "#f8d7daa1"; 
    messageElement.style.color = "#721c24";
    messageElement.style.display = 'block';
    return;
  }

  messageElement.textContent = "Password updated successfully";
  messageElement.style.backgroundColor = "#d4edda9c"; 
  messageElement.style.color = "#155724";
  messageElement.style.display = 'block';
}

newPasswordInput.addEventListener('input', validatePassword);
confirmPasswordInput.addEventListener('input', validatePassword);
saveBtn.addEventListener('click', saveChanges);
