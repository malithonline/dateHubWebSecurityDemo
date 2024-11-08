<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DateHub - Find Your Perfect Match</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white border-b fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-rose-500">DateHub</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="login.html" class="text-gray-500 hover:text-gray-700 font-medium">Login</a>
                    <a href="register.html" class="bg-rose-500 text-white rounded-lg px-4 py-2 hover:bg-rose-600 transition-colors">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="pt-16">
        <div class="relative bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                    <div class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                        <div class="sm:text-center lg:text-left">
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span class="block">Find your perfect</span>
                                <span class="block text-rose-500">match today</span>
                            </h1>
                            <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Join thousands of singles who have found meaningful connections on DateHub. Start your journey today!
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                                <div class="rounded-md shadow">
                                    <a href="register.html" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-rose-500 hover:bg-rose-600 md:py-4 md:text-lg md:px-10">
                                        Get Started
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Random Profiles Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Meet Our Singles</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                require_once 'database.php';
                
                // Get random profiles
                $stmt = $conn->prepare("
                    SELECT id, username, profile_photo, location, bio 
                    FROM users 
                    WHERE profile_photo IS NOT NULL 
                    ORDER BY RAND() 
                    LIMIT 8
                ");
                $stmt->execute();
                $random_profiles = $stmt->fetchAll();

                foreach ($random_profiles as $profile):
                ?>
                <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
                    <div class="aspect-w-1 aspect-h-1">
                        <?php if ($profile['profile_photo']): ?>
                            <img src="<?php echo htmlspecialchars($profile['profile_photo']); ?>" 
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500 text-4xl"><?php echo substr($profile['username'], 0, 1); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($profile['username']); ?></h3>
                        <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($profile['location']); ?></p>
                        <p class="mt-2 text-sm line-clamp-2"><?php echo htmlspecialchars($profile['bio']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Features Section -->
        <div class="bg-gray-100 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="bg-rose-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Smart Matching</h3>
                        <p class="mt-2 text-gray-500">Our algorithm finds your perfect match based on your preferences</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-rose-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Verified Profiles</h3>
                        <p class="mt-2 text-gray-500">All profiles are verified for your safety and security</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-rose-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Instant Messaging</h3>
                        <p class="mt-2 text-gray-500">Connect instantly with your matches through our chat system</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="mt-8 border-t border-gray-200 pt-8 text-center">
                <p class="text-base text-gray-400">&copy; 2024 DateHub. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>