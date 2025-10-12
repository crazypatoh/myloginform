document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById('registerForm');
    const signInForm = document.getElementById('signInForm');
    const switchToSignIn = document.getElementById('switchToSignIn');
    const switchToRegister = document.getElementById('switchToRegister');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm-password');

    // Toggle between forms
    switchToSignIn.addEventListener('click', function () {
        registerForm.style.display = 'none';
        signInForm.style.display = 'block';
    }); 

    switchToRegister.addEventListener('click', function () {
        signInForm.style.display = 'none';
        registerForm.style.display = 'block';
    });

    // Password validation and matching
    registerForm.addEventListener('submit', function (event) {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;

        // Regular expression for password validation
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!passwordRegex.test(password)) {
            event.preventDefault(); // Prevent form submission
            alert(
                'Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.'
            );
            return;
        }

        if (password !== confirmPassword) {
            event.preventDefault(); // Prevent form submission
            alert('Passwords do not match. Please try again.');
        }
    });

    const addUserForm = document.getElementById('addUserForm');
    const updateUserForm = document.getElementById('updateUserForm');
    const deleteUserForm = document.getElementById('deleteUserForm');
    const userTable = document.getElementById('userTable').querySelector('tbody');

    // Fetch and display users
    function fetchUsers() {
        fetch('user_crud.php')
            .then(response => response.json())
            .then(users => {
                userTable.innerHTML = '';
                users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.fullname}</td>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td>
                            <button onclick="deleteUser(${user.id})">Delete</button>
                        </td>
                    `;
                    userTable.appendChild(row);
                });
            });
    }

    // Add user
    addUserForm.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(addUserForm);
        formData.append('action', 'add');
        fetch('user_crud.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            fetchUsers();
            addUserForm.reset();
        });
    });

    // Update user
    updateUserForm.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(updateUserForm);
        formData.append('action', 'update');
        fetch('user_crud.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            fetchUsers();
            updateUserForm.reset();
        });
    });

    // Delete user
    deleteUserForm.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(deleteUserForm);
        formData.append('action', 'delete');
        fetch('user_crud.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            fetchUsers();
            deleteUserForm.reset();
        });
    });

    // Initial fetch
    fetchUsers();
});
