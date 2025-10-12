<?php
// File: views/profile/change_password.php

/**
 * Change Password View
 * Allows users to change their password
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

$user = [
    'name' => $_SESSION['name'] ?? $_SESSION['username'],
    'email' => $_SESSION['email'] ?? ''
];

// Get success/error messages from session
$success = $_SESSION['password_success'] ?? null;
$error = $_SESSION['password_error'] ?? null;
unset($_SESSION['password_success'], $_SESSION['password_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Mental Health Companion</title>
    <link rel="stylesheet" href="../../dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .edit-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .header h1 {
            color: #2d3748;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header h1 i {
            color: #667eea;
        }

        .btn-back {
            padding: 0.75rem 1.5rem;
            background: #e9ecef;
            color: #495057;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-back:hover {
            background: #dee2e6;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert i {
            font-size: 1.25rem;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .info-box i {
            color: #2196f3;
            font-size: 1.25rem;
            margin-top: 0.1rem;
        }

        .info-box p {
            color: #1565c0;
            line-height: 1.6;
            margin: 0;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: #2d3748;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-group label i {
            color: #667eea;
            margin-right: 0.5rem;
        }

        .form-group label .required {
            color: #e53e3e;
        }

        .password-input-wrapper {
            position: relative;
        }

        .form-group input[type="password"],
        .form-group input[type="text"] {
            width: 100%;
            padding: 0.75rem 3rem 0.75rem 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .toggle-password {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #718096;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 0.5rem;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: #667eea;
        }

        .password-requirements {
            margin-top: 0.75rem;
            padding: 0.75rem;
            background: #f7fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .password-requirements strong {
            color: #2d3748;
            font-size: 0.9rem;
            display: block;
            margin-bottom: 0.5rem;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-requirements li {
            color: #718096;
            font-size: 0.875rem;
            padding: 0.25rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .password-requirements li i {
            font-size: 0.5rem;
            color: #cbd5e0;
        }

        .password-requirements li.valid {
            color: #28a745;
        }

        .password-requirements li.valid i {
            color: #28a745;
            font-size: 0.875rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #e9ecef;
        }

        .btn {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #e9ecef;
            color: #495057;
        }

        .btn-secondary:hover {
            background: #dee2e6;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .edit-container {
                padding: 1.5rem;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .btn-back {
                width: 100%;
                justify-content: center;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="header">
            <h1>
                <i class="fas fa-key"></i> Change Password
            </h1>
            <a href="profile.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <i class="fas fa-shield-alt"></i>
            <p>
                <strong>Security Notice:</strong> Make sure your new password is strong and unique. Don't use the same password across multiple sites.
            </p>
        </div>

        <form action="../../public/change_password.php" method="POST" id="changePasswordForm">
            <div class="form-group">
                <label>
                    <i class="fas fa-lock"></i>
                    Current Password <span class="required">*</span>
                </label>
                <div class="password-input-wrapper">
                    <input type="password" name="current_password" id="current_password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-key"></i>
                    New Password <span class="required">*</span>
                </label>
                <div class="password-input-wrapper">
                    <input type="password" name="new_password" id="new_password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-requirements">
                    <strong>Password Requirements:</strong>
                    <ul id="requirements">
                        <li id="req-length"><i class="fas fa-circle"></i> At least 6 characters</li>
                    </ul>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-check-double"></i>
                    Confirm New Password <span class="required">*</span>
                </label>
                <div class="password-input-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-actions">
                <a href="profile.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password validation
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const reqLength = document.getElementById('req-length');

        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Length check
            if (password.length >= 6) {
                reqLength.classList.add('valid');
            } else {
                reqLength.classList.remove('valid');
            }
        });

        // Form validation
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (newPassword.length < 6) {
                e.preventDefault();
                alert('New password must be at least 6 characters long.');
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New password and confirm password do not match.');
                return false;
            }
        });
    </script>
</body>
</html>
