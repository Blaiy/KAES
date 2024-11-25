<?php
session_start(); 
require_once 'db.php'; 
require_once 'functions.php';
require_once 'get_database_info.php';

// Security check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Initialize DatabaseInfo class
$dbInfo = new DatabaseInfo($pdo);

// Get initial dashboard stats
$dashboardStats = $dbInfo->getDashboardStats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KAES Advanced Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'kabarak-maroon': '#800000',
                        'kabarak-gold': '#FFD700'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Main Navigation -->
    <nav class="bg-kabarak-maroon text-white">
        <div class="container mx-auto px-6 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-xl font-bold">KAES Admin</span>
                </div>
                <div class="flex items-center gap-4">
                    <span><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                    <a href="logout.php" class="bg-white text-kabarak-maroon px-4 py-2 rounded hover:bg-gray-100">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Dashboard Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Users Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-gray-500 text-sm">Total Users</h3>
                    <i class="fas fa-users text-kabarak-maroon"></i>
                </div>
                <p class="text-3xl font-bold"><?php echo $dashboardStats['total_users']; ?></p>
                <div class="text-sm text-gray-500 mt-2">
                    Active members in the system
                </div>
            </div>

            <!-- Total Events Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-gray-500 text-sm">Total Events</h3>
                    <i class="fas fa-calendar text-kabarak-maroon"></i>
                </div>
                <p class="text-3xl font-bold"><?php echo $dashboardStats['total_events']; ?></p>
                <div class="text-sm text-gray-500 mt-2">
                    Events created in the system
                </div>
            </div>

            <!-- Total Registrations Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-gray-500 text-sm">Total Registrations</h3>
                    <i class="fas fa-ticket-alt text-kabarak-maroon"></i>
                </div>
                <p class="text-3xl font-bold"><?php echo $dashboardStats['total_registrations']; ?></p>
                <div class="text-sm text-gray-500 mt-2">
                    Event registrations recorded
                </div>
            </div>
        </div>

        <!-- Main Content Tabs -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex">
                    <button onclick="showTab('events')" class="tab-button w-1/4 py-4 px-6 text-center border-b-2 border-kabarak-maroon font-medium text-sm">
                        Events Management
                    </button>
                    <button onclick="showTab('users')" class="tab-button w-1/4 py-4 px-6 text-center border-b-2 border-transparent font-medium text-sm">
                        User Management
                    </button>
                    <button onclick="showTab('registrations')" class="tab-button w-1/4 py-4 px-6 text-center border-b-2 border-transparent font-medium text-sm">
                        Registrations
                    </button>
                    <button onclick="showTab('analytics')" class="tab-button w-1/4 py-4 px-6 text-center border-b-2 border-transparent font-medium text-sm">
                        Analytics
                    </button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div class="p-6">
                <!-- Events Tab -->
                <div id="events-tab" class="tab-content">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">Events Management</h2>
                        <button onclick="showAddEventModal()" class="bg-kabarak-maroon text-white px-4 py-2 rounded hover:bg-kabarak-maroon/90">
                            Add New Event
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full" id="events-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Events will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Users Tab -->
                <div id="users-tab" class="tab-content hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">User Management</h2>
                        <div class="flex gap-4">
                            <input type="text" id="user-search" placeholder="Search users..." 
                                   class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-kabarak-maroon">
                            <button onclick="exportUsers()" class="bg-kabarak-maroon text-white px-4 py-2 rounded hover:bg-kabarak-maroon/90">
                                Export Users
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full" id="users-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Users will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Registrations Tab -->
                <div id="registrations-tab" class="tab-content hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">Event Registrations</h2>
                        <select id="event-filter" class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-kabarak-maroon">
                            <option value="">All Events</option>
                            <!-- Events will be loaded here -->
                        </select>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full" id="registrations-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Registrations will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Analytics Tab -->
                <div id="analytics-tab" class="tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Events by Category Chart -->
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-4">Events by Category</h3>
                            <canvas id="eventsByCategoryChart"></canvas>
                        </div>
                        
                        <!-- Registration Trends Chart -->
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-4">Registration Trends</h3>
                            <canvas id="registrationTrendsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div id="add-event-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <!-- Modal content here -->
    </div>

    <script>
        // Tab switching functionality
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-kabarak-maroon', 'text-kabarak-maroon');
                button.classList.add('border-transparent');
            });
            
            document.getElementById(`${tabName}-tab`).classList.remove('hidden');
            event.currentTarget.classList.add('border-kabarak-maroon', 'text-kabarak-maroon');
            
            // Load data based on tab
            switch(tabName) {
                case 'events':
                    loadEvents();
                    break;
                case 'users':
                    loadUsers();
                    break;
                case 'registrations':
                    loadRegistrations();
                    break;
                case 'analytics':
                    loadAnalytics();
                    break;
            }
        }

        // Data loading functions
        function loadEvents() {
            fetch('get_database_info.php?type=event')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#events-table tbody');
                    tbody.innerHTML = data.map(event => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">${event.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${event.event_date}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${event.category}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${event.location}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="editEvent(${event.id})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                <button onclick="deleteEvent(${event.id})" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    `).join('');
                });
        }

        function loadUsers() {
            fetch('get_database_info.php?type=user')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#users-table tbody');
                    tbody.innerHTML = data.map(user => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">${user.full_name}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${user.email}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${user.created_at}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${user.is_admin ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                                    ${user.is_admin ? 'Admin' : 'User'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="editUser(${user.id})" class="text-blue
                                // Continuing the script tag content...
                                <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    `).join('');
                });
        }

        function loadRegistrations() {
            // Load events for the filter dropdown first
            fetch('get_database_info.php?type=event')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('event-filter');
                    select.innerHTML = '<option value="">All Events</option>' + 
                        data.map(event => `<option value="${event.id}">${event.name}</option>`).join('');
                });

            // Load registrations
            fetch('get_database_info.php?type=registration')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#registrations-table tbody');
                    tbody.innerHTML = data.map(reg => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">${reg.user_name}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${reg.event_name}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${reg.registration_date}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    ${reg.status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                    ${reg.status}
                                </span>
                            </td>
                        </tr>
                    `).join('');
                });
        }

        function loadAnalytics() {
            // Load events by category data
            fetch('get_database_info.php?type=analytics&metric=events_by_category')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('eventsByCategoryChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: data.map(item => item.category),
                            datasets: [{
                                data: data.map(item => item.count),
                                backgroundColor: [
                                    '#800000', '#FFD700', '#4B0082', '#2E8B57',
                                    '#CD853F', '#4682B4', '#800080', '#556B2F'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                });

            // Load registration trends data
            fetch('get_database_info.php?type=analytics&metric=registration_trends')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('registrationTrendsChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.map(item => item.date),
                            datasets: [{
                                label: 'Registrations',
                                data: data.map(item => item.count),
                                borderColor: '#800000',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                });
        }

        // CRUD Operations
        function showAddEventModal() {
            const modal = document.getElementById('add-event-modal');
            modal.classList.remove('hidden');
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-lg w-full">
                    <h2 class="text-xl font-bold mb-4">Add New Event</h2>
                    <form id="add-event-form" onsubmit="submitEvent(event)">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="event-name">
                                Event Name
                            </label>
                            <input type="text" id="event-name" name="name" required
                                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-kabarak-maroon">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="event-date">
                                Date
                            </label>
                            <input type="date" id="event-date" name="date" required
                                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-kabarak-maroon">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="event-category">
                                Category
                            </label>
                            <select id="event-category" name="category" required
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-kabarak-maroon">
                                <option value="">Select Category</option>
                                <option value="academic">Academic</option>
                                <option value="social">Social</option>
                                <option value="sports">Sports</option>
                                <option value="cultural">Cultural</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="event-location">
                                Location
                            </label>
                            <input type="text" id="event-location" name="location" required
                                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-kabarak-maroon">
                        </div>
                        <div class="flex justify-end gap-4">
                            <button type="button" onclick="closeModal('add-event-modal')"
                                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="bg-kabarak-maroon text-white px-4 py-2 rounded hover:bg-kabarak-maroon/90">
                                Add Event
                            </button>
                        </div>
                    </form>
                </div>
            `;
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function submitEvent(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            fetch('manage_events.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal('add-event-modal');
                    loadEvents();
                } else {
                    alert('Error adding event: ' + data.message);
                }
            });
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', () => {
            showTab('events');
            
            // Set up event filter listener
            document.getElementById('event-filter').addEventListener('change', (e) => {
                const eventId = e.target.value;
                loadRegistrations(eventId);
            });

            // Set up user search listener
            document.getElementById('user-search').addEventListener('input', (e) => {
                const searchTerm = e.target.value;
                loadUsers(searchTerm);
            });
        });

        // Export functionality
        function exportUsers() {
            window.location.href = 'export_users.php';
        }

        // Event handlers for edit/delete
        function editEvent(id) {
            // Implementation for editing an event
            console.log('Editing event:', id);
        }

        function deleteEvent(id) {
            if (confirm('Are you sure you want to delete this event?')) {
                fetch(`manage_events.php?action=delete&id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadEvents();
                    } else {
                        alert('Error deleting event: ' + data.message);
                    }
                });
            }
        }

        function editUser(id) {
            // Implementation for editing a user
            console.log('Editing user:', id);
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                fetch(`manage_users.php?action=delete&id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadUsers();
                    } else {
                        alert('Error deleting user: ' + data.message);
                    }
                });
            }
        }
    </script>
</body>
</html>