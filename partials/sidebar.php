<!-- Sidebar -->
<div class="lg:hidden fixed inset-0 bg-black/50 z-40 transition-opacity duration-300" 
     id="sidebar-overlay"
     onclick="toggleSidebar(false)"
     style="display: none;">
</div>

<aside class="fixed top-0 left-0 h-screen w-64 bg-kabarak-maroon text-white z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300"
       id="sidebar">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="p-4 text-center">
            <img src="images/kabarak logo.png" alt="KAES Logo" class="w-24 mx-auto">
        </div>

        <!-- Profile Section -->
        <div class="p-4 border-b border-kabarak-gold/30">
            <div class="flex items-center space-x-3">
                <?php if ($user_avatar && file_exists($user_avatar)): ?>
                    <img src="<?php echo $user_avatar; ?>" alt="Profile" class="w-12 h-12 rounded-full">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-full bg-kabarak-gold flex items-center justify-center text-kabarak-maroon font-bold text-xl">
                        <?php
                            $initials = explode(' ', $user_name);
                            $initials = count($initials) > 1 ?
                                strtoupper(substr($initials[0], 0, 1) . substr(end($initials), 0, 1)) :
                                strtoupper(substr($user_name, 0, 1));
                            echo $initials;
                        ?>
                    </div>
                <?php endif; ?>
                <div class="flex-1">
                    <h3 class="font-semibold"><?php echo $user_name; ?></h3>
                    <a href="edit_profile.php" class="text-sm text-kabarak-gold hover:text-kabarak-gold/80">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-4">
            <div class="space-y-2">
                <a href="home.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-kabarak-gold/10 <?php echo basename($_SERVER['PHP_SELF']) === 'home.php' ? 'bg-kabarak-gold/10' : ''; ?>">
                    <i class="ri-home-line text-xl"></i>
                    <span>Home</span>
                </a>
                <a href="search.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-kabarak-gold/10 <?php echo basename($_SERVER['PHP_SELF']) === 'search.php' ? 'bg-kabarak-gold/10' : ''; ?>">
                    <i class="ri-search-line text-xl"></i>
                    <span>Search</span>
                </a>
                <a href="connect.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-kabarak-gold/10 <?php echo basename($_SERVER['PHP_SELF']) === 'connect.php' ? 'bg-kabarak-gold/10' : ''; ?>">
                    <i class="ri-group-line text-xl"></i>
                    <span>Connect</span>
                </a>
                <a href="messages.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-kabarak-gold/10 <?php echo basename($_SERVER['PHP_SELF']) === 'messages.php' ? 'bg-kabarak-gold/10' : ''; ?>">
                    <i class="ri-message-3-line text-xl"></i>
                    <span>Messages</span>
                </a>
                <a href="events.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-kabarak-gold/10 <?php echo basename($_SERVER['PHP_SELF']) === 'events.php' ? 'bg-kabarak-gold/10' : ''; ?>">
                    <i class="ri-calendar-event-line text-xl"></i>
                    <span>Events</span>
                </a>
                <a href="forum.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-kabarak-gold/10 <?php echo basename($_SERVER['PHP_SELF']) === 'forum.php' ? 'bg-kabarak-gold/10' : ''; ?>">
                    <i class="ri-discuss-line text-xl"></i>
                    <span>Forum</span>
                </a>
            </div>
        </nav>

        <!-- Logout -->
        <div class="p-4">
            <a href="logout.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-kabarak-gold/10">
                <i class="ri-logout-box-line text-xl"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</aside>

<!-- Mobile Menu Toggle Button -->
<button class="lg:hidden fixed bottom-4 right-4 bg-kabarak-maroon text-white p-3 rounded-full shadow-lg z-50"
        onclick="toggleSidebar(true)">
    <i class="ri-menu-line text-xl"></i>
</button>

<!-- Add this script at the bottom of sidebar.php -->
<script>
    function toggleSidebar(show) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (show) {
            sidebar.classList.remove('-translate-x-full');
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.querySelector('button[onclick="toggleSidebar(true)"]');
        
        if (window.innerWidth < 1024 && // Only on mobile
            !sidebar.contains(e.target) && // Click not on sidebar
            !toggleButton.contains(e.target) && // Click not on toggle button
            !sidebar.classList.contains('-translate-x-full')) { // Sidebar is open
            toggleSidebar(false);
        }
    });

    // Handle resize events
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebar-overlay').style.display = 'none';
            document.body.style.overflow = '';
        } else {
            document.getElementById('sidebar').classList.add('-translate-x-full');
        }
    });
</script>