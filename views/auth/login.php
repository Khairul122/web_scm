<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SCM System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #5a67d8;
            --secondary: #764ba2;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
            --white: #ffffff;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="white" opacity="0.1"/><circle cx="80" cy="40" r="1" fill="white" opacity="0.1"/><circle cx="40" cy="80" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .login-container {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 420px;
            position: relative;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: var(--shadow);
        }

        .logo i {
            color: var(--white);
            font-size: 1.5rem;
        }

        .login-header h1 {
            color: var(--gray-900);
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        .login-header p {
            color: var(--gray-600);
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
            font-weight: 500;
            font-size: 0.875rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            padding-left: 2.75rem;
            border: 2px solid var(--gray-200);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
            font-size: 1rem;
            pointer-events: none;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .btn {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }

        .btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-content {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .loading {
            display: none;
            align-items: center;
            gap: 0.75rem;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid var(--white);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert {
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .alert-success {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-warning {
            background-color: #fffbeb;
            border: 1px solid #fed7aa;
            color: #d97706;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remember-me input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            accent-color: var(--primary);
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--primary-dark);
        }

        .divider {
            position: relative;
            margin: 2rem 0;
            text-align: center;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--gray-200);
        }

        .divider span {
            background: var(--white);
            padding: 0 1rem;
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .social-login {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .social-btn {
            padding: 0.75rem;
            border: 2px solid var(--gray-200);
            border-radius: 0.75rem;
            background: var(--white);
            color: var(--gray-700);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .social-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-1px);
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-100);
        }

        .login-footer p {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .register-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .register-link:hover {
            color: var(--primary-dark);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
                border-radius: 1.25rem;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }

            .social-login {
                grid-template-columns: 1fr;
            }

            .remember-forgot {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }

        @media (max-width: 360px) {
            body {
                padding: 0.5rem;
            }
            
            .login-container {
                padding: 1.5rem 1rem;
            }
        }

        .strength-meter {
            height: 4px;
            background: var(--gray-200);
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: var(--error); width: 25%; }
        .strength-fair { background: var(--warning); width: 50%; }
        .strength-good { background: var(--primary); width: 75%; }
        .strength-strong { background: var(--success); width: 100%; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-boxes"></i>
            </div>
            <h1>SCM System</h1>
            <p>Masuk ke dashboard supply chain management Anda</p>
        </div>

        <div id="alerts"></div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" novalidate>
            <div class="form-group">
                <label for="identifier">Email atau No. Telepon</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" 
                           id="identifier" 
                           name="identifier" 
                           class="form-control" 
                           placeholder="Masukkan email atau nomor telepon"
                           autocomplete="username"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="Masukkan password"
                           autocomplete="current-password"
                           required>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>
            </div>

            <div class="remember-forgot">
                <label class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <span>Ingat saya</span>
                </label>
                <a href="?controller=Auth&action=forgotPassword" class="forgot-link">
                    Lupa Password?
                </a>
            </div>

            <button type="submit" class="btn" id="loginBtn">
                <div class="btn-content">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Masuk ke Dashboard</span>
                </div>
                <div class="loading">
                    <div class="spinner"></div>
                    <span>Memproses...</span>
                </div>
            </button>
        </form>

        <div class="divider">
            <span>atau</span>
        </div>

        <div class="social-login">
            <a href="#" class="social-btn" onclick="alert('Fitur Google login akan segera tersedia')">
                <i class="fab fa-google"></i>
                <span>Google</span>
            </a>
            <a href="#" class="social-btn" onclick="alert('Fitur Microsoft login akan segera tersedia')">
                <i class="fab fa-microsoft"></i>
                <span>Microsoft</span>
            </a>
        </div>

        <div class="login-footer">
            <p>Belum memiliki akun?</p>
            <a href="?controller=Auth&action=register" class="register-link">
                Daftar Sekarang
            </a>
        </div>
    </div>

    <script>
        class LoginManager {
            constructor() {
                this.form = document.getElementById('loginForm');
                this.btn = document.getElementById('loginBtn');
                this.btnContent = this.btn.querySelector('.btn-content');
                this.loading = this.btn.querySelector('.loading');
                this.togglePassword = document.getElementById('togglePassword');
                this.passwordInput = document.getElementById('password');
                this.identifierInput = document.getElementById('identifier');
                
                this.init();
            }

            init() {
                this.form.addEventListener('submit', this.handleSubmit.bind(this));
                this.togglePassword.addEventListener('click', this.togglePasswordVisibility.bind(this));
                this.identifierInput.addEventListener('input', this.validateIdentifier.bind(this));
                this.passwordInput.addEventListener('input', this.validatePassword.bind(this));
                
                this.loadRememberedUser();
            }

            async handleSubmit(e) {
                e.preventDefault();
                
                if (!this.validateForm()) {
                    return;
                }

                this.setLoading(true);
                
                const formData = new FormData(this.form);
                const loginData = {
                    identifier: formData.get('identifier'),
                    password: formData.get('password')
                };

                try {
                    const response = await this.loginAPI(loginData);
                    
                    if (response.success) {
                        if (formData.get('remember')) {
                            localStorage.setItem('remembered_user', loginData.identifier);
                        }
                        
                        this.showAlert('Login berhasil! Mengalihkan...', 'success');
                        
                        setTimeout(() => {
                            window.location.href = '?controller=Dashboard&action=index';
                        }, 1000);
                    } else {
                        this.showAlert(response.error || 'Login gagal', 'error');
                    }
                } catch (error) {
                    this.showAlert('Terjadi kesalahan jaringan. Silakan coba lagi.', 'error');
                    console.error('Login error:', error);
                } finally {
                    this.setLoading(false);
                }
            }

            async loginAPI(data) {
                const response = await fetch('?api=auth&path=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                return {
                    success: response.ok && result.user,
                    error: result.error,
                    data: result
                };
            }

            validateForm() {
                const identifier = this.identifierInput.value.trim();
                const password = this.passwordInput.value;

                if (!identifier) {
                    this.showAlert('Email atau nomor telepon harus diisi', 'error');
                    this.identifierInput.focus();
                    return false;
                }

                if (!password) {
                    this.showAlert('Password harus diisi', 'error');
                    this.passwordInput.focus();
                    return false;
                }

                if (password.length < 6) {
                    this.showAlert('Password minimal 6 karakter', 'error');
                    this.passwordInput.focus();
                    return false;
                }

                return true;
            }

            validateIdentifier() {
                const value = this.identifierInput.value.trim();
                const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                const isPhone = /^[\+]?[0-9]{10,15}$/.test(value);
                
                if (value && !isEmail && !isPhone) {
                    this.identifierInput.style.borderColor = 'var(--error)';
                } else {
                    this.identifierInput.style.borderColor = 'var(--gray-200)';
                }
            }

            validatePassword() {
                const value = this.passwordInput.value;
                
                if (value.length > 0 && value.length < 6) {
                    this.passwordInput.style.borderColor = 'var(--error)';
                } else {
                    this.passwordInput.style.borderColor = 'var(--gray-200)';
                }
            }

            togglePasswordVisibility() {
                const type = this.passwordInput.type === 'password' ? 'text' : 'password';
                this.passwordInput.type = type;
                
                this.togglePassword.classList.toggle('fa-eye');
                this.togglePassword.classList.toggle('fa-eye-slash');
            }

            setLoading(loading) {
                this.btn.disabled = loading;
                
                if (loading) {
                    this.btnContent.style.display = 'none';
                    this.loading.style.display = 'flex';
                } else {
                    this.btnContent.style.display = 'flex';
                    this.loading.style.display = 'none';
                }
            }

            showAlert(message, type = 'error') {
                const alertsContainer = document.getElementById('alerts');
                
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type}`;
                
                const icon = type === 'success' ? 'check-circle' : 
                           type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle';
                
                alertDiv.innerHTML = `
                    <i class="fas fa-${icon}"></i>
                    <span>${message}</span>
                `;
                
                alertsContainer.innerHTML = '';
                alertsContainer.appendChild(alertDiv);
                
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }

            loadRememberedUser() {
                const rememberedUser = localStorage.getItem('remembered_user');
                if (rememberedUser) {
                    this.identifierInput.value = rememberedUser;
                    document.getElementById('remember').checked = true;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            new LoginManager();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
                e.preventDefault();
                document.getElementById('loginForm').requestSubmit();
            }
        });
    </script>
</body>
</html>