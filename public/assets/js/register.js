document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.querySelector('form');
    
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(registerForm);
        const data = Object.fromEntries(formData.entries());
        
        fetch('http://localhost:3000/api/auth/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.token) {
                // Store token in localStorage or sessionStorage
                localStorage.setItem('auth_token', result.token);
                window.location.href = '/dashboard';
            } else {
                // Handle registration error
                alert(result.message || 'Registration failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during registration');
        });
    });
});