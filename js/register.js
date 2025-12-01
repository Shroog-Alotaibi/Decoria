    function validateForm(event) {
      event.preventDefault();

      const email = document.getElementById('email').value;
      const phone = document.getElementById('phone').value;
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const terms = document.getElementById('terms').checked;
      const message = document.getElementById('message');

      
      document.getElementById('emailError').textContent = '';
      document.getElementById('phoneError').textContent = '';
      document.getElementById('passwordError').textContent = '';
      document.getElementById('confirmPasswordError').textContent = '';
      document.getElementById('termsError').textContent = '';
      message.textContent = '';

      let valid = true;

      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(email)) {
        document.getElementById('emailError').textContent = 'Please enter a valid email address.';
        valid = false;
      }

      const phonePattern = /^05\d{8}$/;
      if (!phonePattern.test(phone)) {
        document.getElementById('phoneError').textContent = 'Phone number must start with 05 and be 10 digits long.';
        valid = false;
      }

      const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
      if (!passwordPattern.test(password)) {
        document.getElementById('passwordError').textContent = 'Password must be at least 8 characters long and include uppercase, lowercase letters, and a number.';
        valid = false;
      }

      if (password !== confirmPassword) {
        document.getElementById('confirmPasswordError').textContent = 'Passwords do not match.';
        valid = false;
      }

      if (!terms) {
        document.getElementById('termsError').textContent = 'You must accept the Terms of Service to register.';
        valid = false;
      }

      if (!valid) {
        message.textContent = '';
        return false;
      }

      message.textContent = 'Registration successful!';
      message.style.color = 'green';
      return true;
    }
