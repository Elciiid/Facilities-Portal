<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - La Rose Noire Facilities</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
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
            background: radial-gradient(circle at 20% 80%, rgba(236, 72, 153, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(236, 72, 153, 0.06) 0%, transparent 50%),
                        radial-gradient(circle at 40% 40%, rgba(236, 72, 153, 0.04) 0%, transparent 50%);
            pointer-events: none;
        }

        .login-card {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.08), rgba(236, 72, 153, 0.06));
            border-radius: 50%;
            transform: translate(50px, -50px);
        }

        .login-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.06), rgba(236, 72, 153, 0.08));
            border-radius: 50%;
            transform: translate(-40px, 40px);
        }

        .input-field {
            transition: all 0.3s ease;
            border: 2px solid #e2e8f0;
            background: rgba(255, 255, 255, 0.8);
        }

        .input-field:focus {
            border-color: #ec4899;
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
            background: rgba(255, 255, 255, 0.95);
        }

        .login-btn {
            background: linear-gradient(135deg,rgb(240, 116, 178),rgb(228, 184, 195));
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(192, 38, 211, 0.3);
        }

        .logo-glow {
            position: relative;
        }

        .logo-glow::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.2) 0%, rgba(236, 72, 153, 0.1) 50%, transparent 70%);
            border-radius: 50%;
            animation: logoPulse 2s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% {
                opacity: 0.3;
                transform: scale(1);
            }
            50% {
                opacity: 0.6;
                transform: scale(1.05);
            }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md relative">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="w-24 h-24 bg-gradient-to-br from-pink-500 to-fucshia-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-2xl logo-glow">
                <i class="fa-solid fa-shield-alt text-4xl text-white relative z-10"></i>
            </div>
            <h1 class="text-4xl font-black text-gray-900 mb-3 tracking-tight">Admin Access</h1>
            <p class="text-slate-500 font-medium mb-2">La Rose Noire Facilities Management</p>
        </div>

        <!-- Login Form -->
        <div class="login-card rounded-3xl p-8 shadow-2xl relative z-10">
            <form id="loginForm" action="admin_auth.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fa-solid fa-user-shield mr-2 text-pink-500"></i>Administrator Username
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        required
                        class="input-field w-full px-5 py-4 rounded-2xl text-gray-900 placeholder-gray-500 font-medium"
                        placeholder="Enter your username"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fa-solid fa-key mr-2 text-pink-500"></i>Access Password
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="input-field w-full px-5 py-4 rounded-2xl text-gray-900 placeholder-gray-500 font-medium"
                        placeholder="Enter your password"
                    >
                </div>

                <button
                    type="submit"
                    class="login-btn w-full py-4 px-6 rounded-2xl text-white font-bold text-lg shadow-lg relative z-10"
                >
                    <i class="fa-solid fa-shield-alt mr-2"></i>Access Admin Panel
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="../portal.php" class="inline-flex items-center px-4 py-2 bg-white/50 hover:bg-white/70 rounded-xl text-pink-600 hover:text-pink-700 transition-all duration-300 backdrop-blur-sm border border-white/20">
                    <i class="fa-solid fa-arrow-left mr-2"></i>Return to Portal
                </a>
            </div>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="mt-6 p-4 bg-red-50/90 backdrop-blur-sm border border-red-200/50 rounded-2xl text-red-700 text-sm hidden shadow-lg">
            <div class="flex items-center">
                <i class="fa-solid fa-triangle-exclamation mr-3 text-red-500"></i>
                <span id="errorText">Access denied. Invalid administrator credentials.</span>
            </div>
        </div>
    </div>

    <script>
        // Handle server-side authentication errors
        const urlParams = new URLSearchParams(window.location.search);
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');

        if (urlParams.get('error') === 'invalid') {
            errorText.textContent = 'Invalid administrator credentials.';
            errorMessage.classList.remove('hidden');
            errorMessage.classList.add('animate-pulse');

            // Hide error after 5 seconds
            setTimeout(() => {
                errorMessage.classList.add('hidden');
                errorMessage.classList.remove('animate-pulse');
            }, 5000);
        } else if (urlParams.get('error') === 'unauthorized') {
            errorText.textContent = 'Access denied. You are not authorized for admin access.';
            errorMessage.classList.remove('hidden');
            errorMessage.classList.add('animate-pulse');

            // Hide error after 5 seconds
            setTimeout(() => {
                errorMessage.classList.add('hidden');
                errorMessage.classList.remove('animate-pulse');
            }, 5000);
        } else if (urlParams.get('error') === 'expired') {
            errorText.textContent = 'Your session has expired. Please login again.';
            errorMessage.classList.remove('hidden');
            errorMessage.classList.add('animate-pulse');

            // Hide error after 5 seconds
            setTimeout(() => {
                errorMessage.classList.add('hidden');
                errorMessage.classList.remove('animate-pulse');
            }, 5000);
        }
    </script>
</body>
</html>