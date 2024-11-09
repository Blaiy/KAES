<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];
$user_avatar = $_SESSION['avatar'] ?? null;

// Fetch all users except current user
$stmt = $pdo->prepare("
    SELECT DISTINCT
        m.sender_id,
        m.receiver_id,
        m.content as last_message,
        m.created_at as last_message_time,
        CASE 
            WHEN m.sender_id = ? THEN m.receiver_id
            ELSE m.sender_id
        END as other_user_id,
        COALESCE(s.name, a.name) as other_user_name,
        COALESCE(s.avatar, a.avatar) as other_user_avatar,
        u.user_type,
        CASE 
            WHEN u.user_type = 'student' THEN s.reg_number
            WHEN u.user_type = 'alumni' THEN CONCAT('Class of ', a.year_of_graduation)
            ELSE ''
        END as additional_info,
        COUNT(CASE WHEN m.is_read = 0 AND m.receiver_id = ? THEN 1 END) as unread_count
    FROM messages m
    JOIN users u ON (
        CASE 
            WHEN m.sender_id = ? THEN m.receiver_id
            ELSE m.sender_id
        END = u.id
    )
    LEFT JOIN students s ON u.id = s.user_id
    LEFT JOIN alumni a ON u.id = a.user_id
    WHERE m.sender_id = ? OR m.receiver_id = ?
    GROUP BY other_user_id
    ORDER BY MAX(m.created_at) DESC
");

$stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - KAES</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        @media (max-width: 768px) {
            .chat-container {
                position: relative;
                height: 100vh;
            }
            
            .users-list {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                z-index: 20;
                transition: transform 0.3s ease;
            }
            
            .users-list.hidden-mobile {
                transform: translateX(-100%);
            }
            
            .chat-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                z-index: 10;
            }

            .back-to-users {
                display: block !important;
            }
        }

        .messages-container::-webkit-scrollbar {
            width: 4px;
        }
        .messages-container::-webkit-scrollbar-track {
            background: transparent;
        }
        .messages-container::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 2px;
        }
        .chat-bubble {
            max-width: 80%;
            word-wrap: break-word;
        }
        .chat-bubble.sent {
            background-color: #E8EBF8;
            border-radius: 8px 8px 0 8px;
        }
        .chat-bubble.received {
            background-color: #F3F4F6;
            border-radius: 8px 8px 8px 0;
        }
    </style>
</head>
<body class="bg-[#F5F5F5]">
    <div class="flex h-screen chat-container">
        <!-- Left Sidebar - Users List -->
        <div class="w-full md:w-80 bg-white border-r flex flex-col users-list">
            <!-- Header with back button for mobile -->
            <div class="p-4 border-b flex items-center justify-between">
                <h2 class="text-xl font-semibold">Chat</h2>
                <a href="home.php" class="md:hidden text-gray-600 hover:text-gray-900">
                    <i class="ri-arrow-left-line text-xl"></i>
                </a>
            </div>

            <!-- Search -->
            <div class="p-4">
                <div class="relative">
                    <input type="text" 
                           placeholder="Search users..."
                           class="w-full pl-10 pr-4 py-2 bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Users List -->
            <div class="flex-1 overflow-y-auto messages-container">
                <?php if (empty($conversations)): ?>
                    <div class="p-4 text-center text-gray-500">
                        No conversations yet
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): ?>
                        <div class="hover:bg-gray-100 cursor-pointer transition-colors"
                             onclick="loadChat('<?php echo $conv['other_user_id']; ?>', '<?php echo htmlspecialchars($conv['other_user_name']); ?>')">
                            <div class="p-3 flex items-center space-x-3">
                                <?php if (!empty($conv['other_user_avatar']) && file_exists($conv['other_user_avatar'])): ?>
                                    <img src="<?php echo htmlspecialchars($conv['other_user_avatar']); ?>" 
                                         class="w-10 h-10 rounded-full">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-[#464775] text-white flex items-center justify-center font-semibold">
                                        <?php echo getInitials($conv['other_user_name']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-baseline">
                                        <h3 class="font-medium truncate"><?php echo htmlspecialchars($conv['other_user_name']); ?></h3>
                                        <span class="text-xs text-gray-400">
                                            <?php echo time_elapsed_string($conv['last_message_time']); ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate">
                                        <?php 
                                        if ($conv['sender_id'] == $user_id) {
                                            echo 'You: ';
                                        }
                                        echo htmlspecialchars($conv['last_message']); 
                                        ?>
                                    </p>
                                    <?php if ($conv['unread_count'] > 0): ?>
                                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-kabarak-maroon rounded-full">
                                            <?php echo $conv['unread_count']; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="hidden md:flex flex-1 flex-col bg-white chat-area">
            <!-- Chat Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b bg-white">
                <div class="flex items-center space-x-3">
                    <button class="md:hidden back-to-users" onclick="showUsersList()">
                        <i class="ri-arrow-left-line text-xl"></i>
                    </button>
                    <div id="chat-user-avatar" class="w-10 h-10 rounded-full bg-[#464775] text-white flex items-center justify-center font-semibold"></div>
                    <div>
                        <h3 id="chat-user-name" class="font-semibold"></h3>
                        <p class="text-xs text-green-500">Active now</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button class="p-2 hover:bg-gray-100 rounded-full">
                        <i class="ri-video-add-line text-gray-600 text-xl"></i>
                    </button>
                    <button class="p-2 hover:bg-gray-100 rounded-full">
                        <i class="ri-phone-line text-gray-600 text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Messages Area -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-4 messages-container bg-white">
                <!-- Messages will be loaded here -->
            </div>

            <!-- Message Input -->
            <div class="p-4 border-t bg-white">
                <form id="message-form" class="flex items-center space-x-2">
                    <button type="button" class="p-2 hover:bg-gray-100 rounded-full">
                        <i class="ri-emotion-line text-gray-600 text-xl"></i>
                    </button>
                    <button type="button" class="p-2 hover:bg-gray-100 rounded-full">
                        <i class="ri-attachment-2 text-gray-600 text-xl"></i>
                    </button>
                    <input type="text" 
                           id="message-input"
                           placeholder="Type a message..."
                           class="flex-1 py-2 px-4 bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" 
                            class="p-2 bg-[#464775] text-white rounded-md hover:bg-[#464775]/90">
                        <i class="ri-send-plane-fill"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentReceiverId = null;

        function loadChat(userId, userName) {
            console.log('Loading chat for:', userId, userName);
            currentReceiverId = userId;
            
            // Get chat area and default state elements
            const chatArea = document.querySelector('.chat-area');
            const usersList = document.querySelector('.users-list');
            
            if (chatArea && usersList) {
                // Show chat area
                chatArea.classList.remove('hidden');
                
                // Handle mobile view
                if (window.innerWidth < 768) {
                    usersList.classList.add('hidden-mobile');
                }
                
                // Update chat header
                const userNameElement = document.getElementById('chat-user-name');
                const avatarElement = document.getElementById('chat-user-avatar');
                
                if (userNameElement) userNameElement.textContent = userName;
                if (avatarElement) avatarElement.textContent = getInitials(userName);

                // Load messages
                loadMessages(userId);
            } else {
                console.error('Chat area or users list elements not found');
            }
        }

        async function loadMessages(userId) {
            try {
                console.log('Fetching messages for user:', userId); // Debug log
                const response = await fetch(`get_messages.php?receiver_id=${userId}`);
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);

                const container = document.getElementById('messages-container');
                if (!data.messages || !Array.isArray(data.messages)) {
                    container.innerHTML = '<p class="text-center text-gray-500 py-4">No messages yet</p>';
                    return;
                }

                const messagesHTML = data.messages.map(message => createMessageHTML(message)).join('');
                container.innerHTML = messagesHTML;
                container.scrollTop = container.scrollHeight;
                
            } catch (error) {
                console.error('Error:', error);
                const container = document.getElementById('messages-container');
                container.innerHTML = '<p class="text-center text-red-500 py-4">Error loading messages</p>';
            }
        }

        // Add this function to help with debugging
        function handleUserClick(userId, userName) {
            console.log('User clicked:', userId, userName);
            loadChat(userId, userName);
        }

        // Your existing JavaScript with updated styling for messages
        function createMessageHTML(message) {
            const isOwn = parseInt(message.sender_id) === <?php echo $user_id; ?>;
            const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            return `
                <div class="flex ${isOwn ? 'justify-end' : 'justify-start'} mb-4">
                    <div class="flex items-start space-x-2 max-w-[70%]">
                        ${!isOwn ? `
                            <div class="flex-shrink-0">
                                ${message.sender_avatar ? 
                                    `<img src="${message.sender_avatar}" alt="" class="w-8 h-8 rounded-full">` :
                                    `<div class="w-8 h-8 rounded-full bg-[#464775] text-white flex items-center justify-center font-semibold">
                                        ${getInitials(message.sender_name)}
                                    </div>`
                                }
                            </div>
                        ` : ''}
                        <div>
                            ${!isOwn ? `
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-sm font-medium">${message.sender_name}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full ${message.user_type === 'student' ? 
                                        'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'}">
                                        ${message.user_type === 'student' ? 'Student' : 'Alumni'}
                                    </span>
                                </div>
                            ` : ''}
                            <div class="chat-bubble ${isOwn ? 'sent' : 'received'} p-3">
                                <p class="text-gray-800">${message.content}</p>
                                <p class="text-xs ${isOwn ? 'text-gray-500' : 'text-gray-500'} mt-1">${time}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        document.getElementById('message-form').addEventListener('submit', async (e) => {
            e.preventDefault(); // Prevent form submission
            
            const input = document.getElementById('message-input');
            const content = input.value.trim();
            
            if (!content || !currentReceiverId) return;

            try {
                const response = await fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        receiver_id: currentReceiverId,
                        content: content
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    input.value = ''; // Clear input after successful send
                    await loadMessages(currentReceiverId); // Reload messages
                    
                    // Scroll to bottom
                    const container = document.getElementById('messages-container');
                    container.scrollTop = container.scrollHeight;
                } else {
                    throw new Error(data.error || 'Failed to send message');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to send message. Please try again.');
            }
        });

        // Update the loadMessages function
        async function loadMessages(userId) {
            try {
                const response = await fetch(`get_messages.php?receiver_id=${userId}`);
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);

                const container = document.getElementById('messages-container');
                if (!data.messages || data.messages.length === 0) {
                    container.innerHTML = `
                        <div class="flex items-center justify-center h-full">
                            <p class="text-gray-500">No messages yet. Start a conversation!</p>
                        </div>
                    `;
                    return;
                }

                const currentUserId = <?php echo $_SESSION['user_id']; ?>;
                const messagesHTML = data.messages.map(message => {
                    const isOwn = parseInt(message.sender_id) === currentUserId;
                    const time = new Date(message.created_at).toLocaleTimeString([], { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                    
                    return `
                        <div class="flex ${isOwn ? 'justify-end' : 'justify-start'} mb-4">
                            <div class="max-w-[70%] ${isOwn ? 'bg-[#E8EBF8]' : 'bg-gray-100'} 
                                        rounded-lg p-3 ${isOwn ? 'rounded-br-none' : 'rounded-bl-none'}">
                                <p class="text-gray-800">${message.content}</p>
                                <p class="text-xs text-gray-500 mt-1">${time}</p>
                            </div>
                        </div>
                    `;
                }).join('');

                container.innerHTML = messagesHTML;
                container.scrollTop = container.scrollHeight;
                
            } catch (error) {
                console.error('Error:', error);
                const container = document.getElementById('messages-container');
                container.innerHTML = `
                    <div class="flex items-center justify-center h-full">
                        <p class="text-red-500">Error loading messages: ${error.message}</p>
                    </div>
                `;
            }
        }

        // Add auto-refresh for messages
        setInterval(() => {
            if (currentReceiverId) {
                loadMessages(currentReceiverId);
            }
        }, 3000);

        function showUsersList() {
            document.querySelector('.users-list').classList.remove('hidden-mobile');
            document.querySelector('.chat-area').classList.add('hidden');
        }

        // Handle resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                document.querySelector('.users-list').classList.remove('hidden-mobile');
                document.querySelector('.chat-area').classList.remove('hidden');
            }
        });

    </script>
</body>
</html>
