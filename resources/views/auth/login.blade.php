<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Media pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#8B5CF6',
                        dark: '#0F172A',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-dark via-primary/20 to-secondary/20 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-2">
                Media pro
            </h1>
            <p class="text-gray-300">Admin Dashboard Login</p>
        </div>

        <div class="bg-gradient-to-r from-primary to-secondary rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold">Admin Credentials</h3>
            </div>
            <div class="space-y-2 bg-white/10 backdrop-blur-sm rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Email:</span>
                    <code class="bg-dark/30 px-3 py-1 rounded text-sm font-mono">admin@admin.com</code>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Password:</span>
                    <code class="bg-dark/30 px-3 py-1 rounded text-sm font-mono">admin123</code>
                </div>
            </div>
            <p class="text-xs mt-3 text-white/80">
                Use these credentials to access the admin dashboard. Additional users can be created from admin panel.
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Welcome Back</h2>

            <form method="POST" action="/login">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="admin@admin.com" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" required autofocus>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Enter password" required>
                </div>

                <div class="mb-6 flex items-center">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary text-white font-semibold py-3 rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                    Sign In to Dashboard
                </button>
            </form>

            <div class="mt-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>Note:</strong> User registration is disabled. New users must be created by administrators through the admin panel.
                </p>
            </div>
        </div>

        <div class="mt-6 text-center space-y-2">
            <a href="/" class="block text-gray-300 hover:text-white transition-colors">Back to Home</a>
        </div>
    </div>
</body>
</html>
