function validateForm() {
    const userId = document.getElementById('userId').value;
    const password = document.getElementById('password').value;
    const role = document.getElementById('role').value;

    if (userId === '' || password === '' || role === '') {
        alert('Please fill in all fields.');
        return false;
    }

    return true;
}
