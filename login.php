<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAES Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'kabarak-maroon': '#800000',
                        'kabarak-gold': '#FFD700',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-kabarak-maroon/90 via-kabarak-maroon to-red-900">
    <div class="min-h-screen flex items-center justify-center p-4">
        <!-- Login Card -->
        <div class="w-full max-w-md">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <img src="images/kabarak logo.png" alt="KAES Logo" class="mx-auto h-24 mb-2">
                <h2 class="text-3xl font-bold text-white mb-2">Welcome Back</h2>
                <p class="text-gray-200">Sign in to your KAES account</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl p-8">
                <form action="login_handler.php" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Email Address</label>
                        <div class="relative">
                            <i class="ri-mail-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-300"></i>
                            <input type="email" name="email" required
                                   class="w-full pl-10 pr-4 py-3 bg-white/10 border border-gray-200/20 rounded-lg 
                                          focus:ring-2 focus:ring-kabarak-gold focus:border-transparent
                                          text-white placeholder-gray-300"
                                   placeholder="Enter your email">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Password</label>
                        <div class="relative">
                            <i class="ri-lock-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-300"></i>
                            <input type="password" name="password" required
                                   class="w-full pl-10 pr-4 py-3 bg-white/10 border border-gray-200/20 rounded-lg 
                                          focus:ring-2 focus:ring-kabarak-gold focus:border-transparent
                                          text-white placeholder-gray-300"
                                   placeholder="Enter your password">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center text-gray-200">
                            <input type="checkbox" class="rounded border-gray-300 text-kabarak-gold 
                                                        focus:ring-kabarak-gold mr-2">
                            Remember me
                        </label>
                        <a href="forgot_password.php" class="text-kabarak-gold hover:text-kabarak-gold/80">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" 
                            class="w-full bg-kabarak-gold text-kabarak-maroon font-semibold py-3 px-4 rounded-lg
                                   hover:bg-kabarak-gold/90 transition-colors">
                        Sign In
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-200">
                        Don't have an account? 
                        <a href="register.php" class="text-kabarak-gold hover:text-kabarak-gold/80 font-semibold">
                            Create Account
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
