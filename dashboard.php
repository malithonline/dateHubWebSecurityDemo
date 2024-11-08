<?php
session_start();
require_once 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Get user information
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle admin actions
if ($user['role'] == 'admin') {
    // Handle user deletion
    if (isset($_POST['delete_user'])) {
        $delete_id = $_POST['delete_user'];
        if ($delete_id != $_SESSION['user_id']) {  // Prevent admin from deleting themselves
            // Delete user's photos
            $stmt = $conn->prepare("SELECT profile_photo FROM users WHERE id = ?");
            $stmt->execute([$delete_id]);
            $user_photo = $stmt->fetch();
            if ($user_photo && $user_photo['profile_photo']) {
                @unlink($user_photo['profile_photo']);
            }
            
            // Delete from matches
            $stmt = $conn->prepare("DELETE FROM matches WHERE user_id = ? OR liked_user_id = ?");
            $stmt->execute([$delete_id, $delete_id]);
            
            // Delete from activity_logs
            $stmt = $conn->prepare("DELETE FROM activity_logs WHERE user_id = ?");
            $stmt->execute([$delete_id]);
            
            // Delete user
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$delete_id]);
        }
    }

    // Handle role change
    if (isset($_POST['change_role'])) {
        $user_id = $_POST['user_id'];
        $new_role = $_POST['new_role'];
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$new_role, $user_id]);
    }

    // Get all users for admin panel
    $stmt = $conn->prepare("SELECT * FROM users WHERE id != ?");
    $stmt->execute([$_SESSION['user_id']]);
    $all_users = $stmt->fetchAll();
}

// Handle like/match action
if (isset($_POST['like_user'])) {
    $liked_user_id = $_POST['like_user'];
    
    // Check if already liked
    $stmt = $conn->prepare("SELECT * FROM matches WHERE user_id = ? AND liked_user_id = ?");
    $stmt->execute([$_SESSION['user_id'], $liked_user_id]);
    
    if ($stmt->rowCount() == 0) {
        // Add new like
        $stmt = $conn->prepare("INSERT INTO matches (user_id, liked_user_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $liked_user_id]);
        
        // Check if it's a mutual match
        $stmt = $conn->prepare("SELECT * FROM matches WHERE user_id = ? AND liked_user_id = ?");
        $stmt->execute([$liked_user_id, $_SESSION['user_id']]);
        if ($stmt->rowCount() > 0) {
            // It's a match!
            // You could add notification logic here
        }
    }
}

// Get potential matches based on preferences
$stmt = $conn->prepare("
    SELECT * FROM users 
    WHERE id != ? 
    AND (
        (? = 'both' AND gender IN ('male', 'female'))
        OR (? = gender)
    )
    AND (
        (looking_for = 'both')
        OR (looking_for = ?)
        OR (? = 'other')
    )
    LIMIT 10
");
$stmt->execute([
    $_SESSION['user_id'],
    $user['looking_for'],
    $user['looking_for'],
    $user['gender'],
    $user['gender']
]);
$potential_matches = $stmt->fetchAll();

// Get mutual matches
$stmt = $conn->prepare("
    SELECT u.*, 
           m1.created_at as matched_at 
    FROM matches m1 
    INNER JOIN matches m2 ON m1.user_id = m2.liked_user_id 
                        AND m1.liked_user_id = m2.user_id 
    INNER JOIN users u ON u.id = m1.liked_user_id 
    WHERE m1.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$mutual_matches = $stmt->fetchAll();

// Get user activity logs
$stmt = $conn->prepare("SELECT * FROM activity_logs WHERE user_id = ? ORDER BY timestamp DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$user_logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dating App - Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-rose-500">DateHub</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <button class="flex items-center gap-2 text-gray-700 hover:text-gray-900">
                            <?php if ($user['profile_photo']): ?>
                                <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" class="w-8 h-8 rounded-full object-cover">
                            <?php else: ?>
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500 text-sm"><?php echo substr($user['username'], 0, 1); ?></span>
                                </div>
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                        </button>
                    </div>
                    <a href="logout.php" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if ($user['role'] != 'admin'): ?>
        <!-- User Dashboard -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="text-center">
                        <?php if ($user['profile_photo']): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" class="w-32 h-32 rounded-full mx-auto object-cover">
                        <?php else: ?>
                            <div class="w-32 h-32 rounded-full bg-gray-200 mx-auto flex items-center justify-center">
                                <span class="text-gray-500 text-4xl"><?php echo substr($user['username'], 0, 1); ?></span>
                            </div>
                        <?php endif; ?>
                        <h2 class="mt-4 text-xl font-semibold"><?php echo htmlspecialchars($user['username']); ?></h2>
                        <p class="text-gray-500"><?php echo htmlspecialchars($user['location']); ?></p>
                    </div>
                    <div class="mt-6 space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Bio</h3>
                            <p class="mt-1"><?php echo htmlspecialchars($user['bio']); ?></p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Interests</h3>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <?php foreach (explode(',', $user['interests']) as $interest): ?>
                                    <span class="px-3 py-1 rounded-full text-sm bg-rose-100 text-rose-600">
                                        <?php echo htmlspecialchars(trim($interest)); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Potential Matches -->
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-bold mb-6">Potential Matches</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($potential_matches as $match): ?>
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
                        <div class="aspect-w-16 aspect-h-9">
                            <?php if ($match['profile_photo']): ?>
                                <img src="<?php echo htmlspecialchars($match['profile_photo']); ?>" 
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500 text-4xl"><?php echo substr($match['username'], 0, 1); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($match['username']); ?></h3>
                            <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($match['location']); ?></p>
                            <p class="mt-2 text-sm line-clamp-2"><?php echo htmlspecialchars($match['bio']); ?></p>
                            <form method="POST" class="mt-4">
                                <input type="hidden" name="like_user" value="<?php echo $match['id']; ?>">
                                <button type="submit" class="w-full bg-rose-500 text-white rounded-lg py-2 px-4 hover:bg-rose-600 transition-colors duration-200">
                                    Like Profile
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Matches Section -->
                <h2 class="text-2xl font-bold mt-12 mb-6">Your Matches</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($mutual_matches as $match): ?>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center gap-4">
                            <?php if ($match['profile_photo']): ?>
                                <img src="<?php echo htmlspecialchars($match['profile_photo']); ?>" class="w-16 h-16 rounded-full object-cover">
                            <?php else: ?>
                                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500 text-2xl"><?php echo substr($match['username'], 0, 1); ?></span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h3 class="font-semibold"><?php echo htmlspecialchars($match['username']); ?></h3>
                                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($match['location']); ?></p>
                                <p class="text-sm text-gray-400 mt-1">Matched <?php echo date('M j, Y', strtotime($match['matched_at'])); ?></p>
                            </div>
                        </div>
                        <button class="w-full mt-4 border border-rose-500 text-rose-500 rounded-lg py-2 px-4 hover:bg-rose-50 transition-colors duration-200">
                            Send Message
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- Admin Dashboard -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-bold">Admin Panel - Manage Users</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profile Info</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($all_users as $other_user): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <?php if ($other_user['profile_photo']): ?>
                                        <img src="<?php echo htmlspecialchars($other_user['profile_photo']); ?>" class="w-8 h-8 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500 text-sm"><?php echo substr($other_user['username'], 0, 1); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($other_user['username']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?php echo htmlspecialchars($other_user['email']); ?></td>
                            <td class="px-6 py-4">
                                <form method="POST">
                                    <select name="new_role" onchange="this.form.submit()" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm">
                                        <option value="user" <?php echo $other_user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo $other_user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <input type="hidden" name="user_id" value="<?php echo $other_user['id']; ?>">
                                    <input type="hidden" name="change_role" value="1">
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">
                                    <p>Gender: <?php echo htmlspecialchars($other_user['gender']); ?></p>
                                    <p>Looking for: <?php echo htmlspecialchars($other_user['looking_for']); ?></p>
                                    <p>Location: <?php echo htmlspecialchars($other_user['location']); ?></p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="delete_user" value="<?php echo $other_user['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>