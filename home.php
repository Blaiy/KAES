<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$user_avatar = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : null;

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/posts/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAES Home</title>
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

<body class="bg-gray-50">
    <?php include 'partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="lg:ml-64 min-h-screen relative">
        <!-- Top Navigation -->
        <nav class="fixed top-0 right-0 left-64 bg-white border-b border-gray-200 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-kabarak-maroon">KAES Dashboard</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button class="p-2 rounded-full hover:bg-gray-100 relative">
                            <i class="ri-notification-3-line text-xl text-gray-600"></i>
                            <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
                        </button>
                        <a href="create_post.php" class="bg-kabarak-maroon text-white px-4 py-2 rounded-lg hover:bg-kabarak-maroon/90">
                            Create Post
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Area with Fixed Right Sidebar -->
        <div class="pt-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="flex flex-col lg:flex-row lg:space-x-6">
                <!-- Main Feed -->
                <div class="lg:w-[calc(100%-320px)] space-y-6">
                    <!-- Create Post Card -->
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <form action="create_post.php" method="POST" enctype="multipart/form-data">
                            <div class="flex items-center space-x-4">
                                <?php if ($user_avatar && file_exists($user_avatar)): ?>
                                    <img src="<?php echo $user_avatar; ?>" alt="Profile" class="w-10 h-10 rounded-full">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-kabarak-maroon text-white flex items-center justify-center font-bold">
                                        <?php 
                                        $initials = explode(' ', $user_name);
                                        echo strtoupper(substr($initials[0], 0, 1) . (isset($initials[1]) ? substr($initials[1], 0, 1) : ''));
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <input type="text" name="content" placeholder="Share your thoughts..."
                                    class="flex-1 rounded-full bg-gray-100 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-kabarak-maroon"
                                    onclick="window.location.href='create_post.php';">
                            </div>
                            <div class="flex justify-between mt-4 pt-4 border-t">
                                <button type="button" onclick="window.location.href='create_post.php';"
                                    class="flex items-center space-x-2 text-gray-600 hover:text-kabarak-maroon">
                                    <i class="ri-image-line"></i>
                                    <span>Photo</span>
                                </button>
                                <button type="button" onclick="window.location.href='create_post.php';"
                                    class="flex items-center space-x-2 text-gray-600 hover:text-kabarak-maroon">
                                    <i class="ri-video-line"></i>
                                    <span>Video</span>
                                </button>
                                <button type="button" onclick="window.location.href='create_post.php';"
                                    class="flex items-center space-x-2 text-gray-600 hover:text-kabarak-maroon">
                                    <i class="ri-attachment-line"></i>
                                    <span>Attachment</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <?php
                    // Updated query to include tagged users
                    $stmt = $pdo->query("
        SELECT 
            p.*, 
            u.email,
            COALESCE(s.name, a.name) as author_name,
            COALESCE(s.avatar, a.avatar) as author_avatar,
            COUNT(DISTINCT l.id) as likes_count,
            COUNT(DISTINCT c.id) as comments_count,
            COUNT(DISTINCT ps.id) as shares_count,
            GROUP_CONCAT(
                DISTINCT CONCAT(
                    COALESCE(s2.name, a2.name)
                ) SEPARATOR '||'
            ) as tagged_users
        FROM posts p
        JOIN users u ON p.user_id = u.id
        LEFT JOIN students s ON u.id = s.user_id
        LEFT JOIN alumni a ON u.id = a.user_id
        LEFT JOIN likes l ON p.id = l.post_id
        LEFT JOIN comments c ON p.id = c.post_id
        LEFT JOIN post_shares ps ON p.id = ps.post_id
        LEFT JOIN post_tags pt ON p.id = pt.post_id
        LEFT JOIN users u2 ON pt.tagged_user_id = u2.id
        LEFT JOIN students s2 ON u2.id = s2.user_id
        LEFT JOIN alumni a2 ON u2.id = a2.user_id
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");

                    while ($post = $stmt->fetch(PDO::FETCH_ASSOC)):
                        $timeAgo = time_elapsed_string($post['created_at']);
                        $isLiked = checkIfLiked($pdo, $post['id'], $_SESSION['user_id']);
                        $tagged_users = $post['tagged_users'] ? explode('||', $post['tagged_users']) : [];
                    ?>
                        <article class="bg-white rounded-xl shadow-sm">
                            <div class="p-4">
                                <!-- Author Info -->
                                <div class="flex items-center space-x-3">
                                    <?php if (!empty($post['author_avatar']) && file_exists($post['author_avatar'])): ?>
                                        <img src="<?php echo htmlspecialchars($post['author_avatar']); ?>" 
                                             alt="<?php echo htmlspecialchars($post['author_name']); ?>" 
                                             class="w-10 h-10 rounded-full">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-kabarak-maroon text-white flex items-center justify-center font-bold">
                                            <?php 
                                            $name_parts = explode(' ', $post['author_name']);
                                            $initials = '';
                                            if (count($name_parts) >= 2) {
                                                $initials = strtoupper(substr($name_parts[0], 0, 1) . substr(end($name_parts), 0, 1));
                                            } else {
                                                $initials = strtoupper(substr($post['author_name'], 0, 2));
                                            }
                                            echo htmlspecialchars($initials);
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h3 class="font-semibold"><?php echo htmlspecialchars($post['author_name']); ?></h3>
                                        <div class="flex items-center text-sm text-gray-500 space-x-2">
                                            <span><?php echo $timeAgo; ?></span>
                                            <?php if (!empty($post['location'])): ?>
                                                <span>•</span>
                                                <div class="flex items-center space-x-1">
                                                    <i class="ri-map-pin-line"></i>
                                                    <span><?php echo htmlspecialchars($post['location']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Post Content -->
                                <div class="mt-4">
                                    <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                    
                                    <!-- Tagged Users -->
                                    <?php if (!empty($tagged_users)): ?>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="text-gray-600 text-sm">with</span>
                                            <?php foreach ($tagged_users as $tagged_user): ?>
                                                <span class="text-kabarak-maroon text-sm font-medium">
                                                    <?php echo htmlspecialchars($tagged_user); ?>
                                                </span>
                                                <?php if ($tagged_user !== end($tagged_users)): ?>
                                                    <span class="text-gray-600 text-sm">,</span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Post Image -->
                                    <?php if ($post['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($post['image_url']); ?>"
                                             alt="Post Image" class="mt-4 rounded-lg w-full">
                                    <?php endif; ?>
                                </div>

                                <!-- Post Actions -->
                                <div class="flex items-center justify-between mt-4 pt-4 border-t">
                                    <button onclick="likePost(<?php echo $post['id']; ?>)"
                                            class="flex items-center space-x-2 <?php echo $isLiked ? 'text-kabarak-maroon' : 'text-gray-600'; ?> hover:text-kabarak-maroon">
                                        <i class="<?php echo $isLiked ? 'ri-heart-fill' : 'ri-heart-line'; ?>"></i>
                                        <span id="likes-count-<?php echo $post['id']; ?>"><?php echo $post['likes_count']; ?> Likes</span>
                                    </button>
                                    <button onclick="toggleComments(<?php echo $post['id']; ?>)"
                                            class="flex items-center space-x-2 text-gray-600 hover:text-kabarak-maroon">
                                        <i class="ri-chat-1-line"></i>
                                        <span><?php echo $post['comments_count']; ?> Comments</span>
                                    </button>
                                    <div id="comments-section-<?php echo $post['id']; ?>" class="hidden mt-4">
                                        <div class="border-t pt-4">
                                            <form onsubmit="postComment(event, <?php echo $post['id']; ?>)" class="flex gap-2">
                                                <input type="text" 
                                                       id="comment-input-<?php echo $post['id']; ?>"
                                                       class="flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-kabarak-maroon"
                                                       placeholder="Write a comment...">
                                                <button type="submit" 
                                                        class="bg-kabarak-maroon text-white px-4 py-2 rounded-lg hover:bg-kabarak-maroon/90">
                                                    Post
                                                </button>
                                            </form>
                                            <div id="comments-list-<?php echo $post['id']; ?>" class="mt-4 space-y-4">
                                                <!-- Comments will be loaded here -->
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="showShareModal(<?php echo $post['id']; ?>)" 
                                            class="flex items-center space-x-2 text-gray-600 hover:text-kabarak-maroon">
                                        <i class="ri-share-line"></i>
                                        <span id="shares-count-<?php echo $post['id']; ?>">
                                            <?php echo isset($post['shares_count']) ? $post['shares_count'] . ' Shares' : '0 Shares'; ?>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <!-- Fixed Right Sidebar -->
                <div class="hidden lg:block w-80 flex-shrink-0">
                    <div class="fixed w-80 space-y-6 pr-4 overflow-y-auto h-[calc(100vh-5rem)] pb-6">
                        <?php include 'partials/right-sidebar.php'; ?>
                    </div>
                </div>

                <!-- Mobile Bottom Navigation for Right Sidebar Content -->
                <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t z-20">
                    <div class="flex justify-around p-2">
                        <button onclick="toggleMobileView('events')" class="p-2 text-center">
                            <i class="ri-calendar-event-line text-xl"></i>
                            <span class="text-xs block">Events</span>
                        </button>
                        <button onclick="toggleMobileView('jobs')" class="p-2 text-center">
                            <i class="ri-briefcase-line text-xl"></i>
                            <span class="text-xs block">Jobs</span>
                        </button>
                        <button onclick="toggleMobileView('forum')" class="p-2 text-center">
                            <i class="ri-discuss-line text-xl"></i>
                            <span class="text-xs block">Forum</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Slide-up Panel for Right Sidebar Content -->
        <div id="mobile-panel" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden">
            <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl max-h-[80vh] overflow-y-auto">
                <div class="p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="mobile-panel-title" class="text-lg font-semibold"></h3>
                        <button onclick="closeMobilePanel()" class="p-2">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                    <div id="mobile-panel-content"></div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add this JavaScript for mobile handling -->
    <script>
        function toggleMobileView(section) {
            const panel = document.getElementById('mobile-panel');
            const title = document.getElementById('mobile-panel-title');
            const content = document.getElementById('mobile-panel-content');
            
            // Set title and content based on section
            switch(section) {
                case 'events':
                    title.textContent = 'Upcoming Events';
                    content.innerHTML = document.querySelector('.events-content').innerHTML;
                    break;
                case 'jobs':
                    title.textContent = 'Latest Jobs';
                    content.innerHTML = document.querySelector('.jobs-content').innerHTML;
                    break;
                case 'forum':
                    title.textContent = 'Forum Highlights';
                    content.innerHTML = document.querySelector('.forum-content').innerHTML;
                    break;
            }
            
            panel.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeMobilePanel() {
            document.getElementById('mobile-panel').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Handle resize events
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) { // lg breakpoint
                closeMobilePanel();
            }
        });
    </script>

    <script>
        // Like Post Function
        async function likePost(postId) {
            try {
                const response = await fetch('like_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ post_id: postId })
                });

                const data = await response.json();
                
                if (data.success) {
                    const likeButton = document.querySelector(`button[onclick="likePost(${postId})"]`);
                    const likesCount = document.getElementById(`likes-count-${postId}`);
                    
                    if (data.liked) {
                        likeButton.querySelector('i').className = 'ri-heart-fill';
                        likeButton.classList.add('text-kabarak-maroon');
                    } else {
                        likeButton.querySelector('i').className = 'ri-heart-line';
                        likeButton.classList.remove('text-kabarak-maroon');
                    }
                    
                    likesCount.textContent = `${data.likes_count} Likes`;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Toggle Comments Section
        function toggleComments(postId) {
            const commentsSection = document.getElementById(`comments-section-${postId}`);
            const isHidden = commentsSection.classList.contains('hidden');
            
            if (isHidden) {
                commentsSection.classList.remove('hidden');
                loadComments(postId);
            } else {
                commentsSection.classList.add('hidden');
            }
        }

        // Load Comments
        async function loadComments(postId) {
            try {
                const response = await fetch(`get_comments.php?post_id=${postId}`);
                const data = await response.json();
                
                const commentsList = document.getElementById(`comments-list-${postId}`);
                
                if (data.comments && data.comments.length > 0) {
                    const commentsHTML = data.comments.map(comment => `
                        <div class="flex space-x-3 mb-4">
                            <div class="flex-shrink-0">
                                ${comment.author_avatar ? 
                                    `<img src="${comment.author_avatar}" class="w-8 h-8 rounded-full">` :
                                    `<div class="w-8 h-8 rounded-full bg-kabarak-maroon text-white flex items-center justify-center font-bold">
                                        ${getInitials(comment.author_name)}
                                    </div>`
                                }
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-100 rounded-lg p-3">
                                    <div class="font-medium">${comment.author_name}</div>
                                    <p class="text-gray-800">${comment.content}</p>
                                </div>
                                <div class="flex items-center space-x-4 mt-1">
                                    <button onclick="likeComment(${comment.id})" 
                                            class="text-sm ${comment.is_liked ? 'text-kabarak-maroon' : 'text-gray-500'} hover:text-kabarak-maroon">
                                        <i class="ri-heart-${comment.is_liked ? 'fill' : 'line'}"></i>
                                        <span id="comment-likes-${comment.id}">${comment.likes_count}</span>
                                    </button>
                                    <button onclick="showReplyForm(${comment.id})" class="text-sm text-gray-500 hover:text-kabarak-maroon">
                                        Reply
                                    </button>
                                </div>
                                <!-- Replies -->
                                ${comment.replies ? comment.replies.map(reply => `
                                    <div class="flex space-x-3 mt-3 ml-8">
                                        <div class="flex-shrink-0">
                                            ${reply.author_avatar ? 
                                                `<img src="${reply.author_avatar}" class="w-6 h-6 rounded-full">` :
                                                `<div class="w-6 h-6 rounded-full bg-kabarak-maroon text-white flex items-center justify-center font-bold text-xs">
                                                    ${getInitials(reply.author_name)}
                                                </div>`
                                            }
                                        </div>
                                        <div class="flex-1">
                                            <div class="bg-gray-100 rounded-lg p-2">
                                                <div class="font-medium text-sm">${reply.author_name}</div>
                                                <p class="text-gray-800 text-sm">${reply.content}</p>
                                            </div>
                                        </div>
                                    </div>
                                `).join('') : ''}
                            </div>
                        </div>
                    `).join('');
                    
                    commentsList.innerHTML = commentsHTML;
                } else {
                    commentsList.innerHTML = '<p class="text-gray-500 text-center">No comments yet</p>';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Post Comment
        async function postComment(event, postId) {
            event.preventDefault();
            
            const input = document.getElementById(`comment-input-${postId}`);
            const content = input.value.trim();
            
            if (!content) return;

            try {
                const response = await fetch('add_comment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        post_id: postId,
                        content: content
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    input.value = '';
                    loadComments(postId);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Share Post
        function showShareModal(postId) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white rounded-xl p-6 w-full max-w-lg mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Share Post</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <textarea id="share-text-${postId}" 
                                  class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent"
                                  placeholder="Add a comment to your share..."
                                  rows="3"></textarea>
                        
                        <div class="flex space-x-4">
                            <button onclick="sharePost(${postId})" 
                                    class="flex-1 bg-kabarak-maroon text-white py-2 px-4 rounded-lg hover:bg-kabarak-maroon/90">
                                Share
                            </button>
                            
                            <div class="flex space-x-2">
                                <button onclick="shareToExternal('whatsapp', ${postId})" 
                                        class="p-2 rounded-lg bg-green-500 text-white hover:bg-green-600">
                                    <i class="ri-whatsapp-line"></i>
                                </button>
                                <button onclick="shareToExternal('x', ${postId})" 
                                        class="p-2 rounded-lg bg-blue-400 text-white hover:bg-blue-500">
                                    <i class="ri-twitter-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        // Share Post Function
        async function sharePost(postId) {
            const shareText = document.getElementById(`share-text-${postId}`).value;
            
            try {
                const response = await fetch('share_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        post_id: postId,
                        share_text: shareText
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    document.querySelector('.fixed').remove();
                    const sharesCount = document.getElementById(`shares-count-${postId}`);
                    sharesCount.textContent = `${data.shares_count} Shares`;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Share to External Platforms
        function shareToExternal(platform, postId) {
            const postUrl = `${window.location.origin}/post.php?id=${postId}`;
            let shareUrl = '';
            
            switch (platform) {
                case 'whatsapp':
                    shareUrl = `https://wa.me/?text=${encodeURIComponent(`Check out this post: ${postUrl}`)}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(postUrl)}&text=${encodeURIComponent('Check out this post on KAES:')}`;
                    break;
            }
            
            window.open(shareUrl, '_blank');
        }
    </script>
</body>

</html>