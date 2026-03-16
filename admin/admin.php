<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - La Rose Noire Facilities</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php
    session_start();
    // Set timezone to ensure correct time display
    date_default_timezone_set('Asia/Manila'); // Adjust timezone as needed
    
    /**
     * Helper function to get employee profile photo URL
     * @param string $employee_number The employee ID/number
     * @return string The photo URL or empty string if not found
     */
    function getEmployeePhotoUrl($employee_number)
    {
        if (empty($employee_number)) {
            return '';
        }

        // Base URL for employee photos
        $base_url = 'http://10.2.0.8/lrnph/emp_photos/';

        // Try common image extensions
        $extensions = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'];

        // Return the first extension format (browser will handle 404 if not found)
        // We'll use JavaScript to handle fallback on client side
        return $base_url . htmlspecialchars($employee_number) . '.jpg';
    }

    /**
     * Generate an img tag with fallback for employee photo
     * @param string $employee_number The employee ID/number
     * @param string $classes CSS classes for the img tag
     * @param string $alt Alt text (defaults to employee number)
     * @return string HTML img tag with fallback icon
     */
    function getEmployeePhotoImg($employee_number, $classes = '', $alt = '')
    {
        if (empty($employee_number)) {
            return '<i class="fa-solid fa-user text-gray-400"></i>';
        }

        $employee_id = htmlspecialchars($employee_number);
        $alt_text = $alt ?: 'Employee ' . $employee_id;
        $classes_attr = $classes ? ' class="' . htmlspecialchars($classes) . '"' : '';

        // Base URL for employee photos
        $base_url = 'http://10.2.0.8/lrnph/emp_photos/';

        // Create img tag with JavaScript fallback that tries multiple extensions
        $js_function = "tryPhotoExtensions('$employee_id', this)";

        return '<img src="' . $base_url . $employee_id . '.jpg" alt="' . htmlspecialchars($alt_text) . '"' . $classes_attr . ' onerror="' . $js_function . '" /><i class="fa-solid fa-user text-gray-100" style="display:none;"></i>';
    }

    /**
     * Check if admin is authenticated via PHP session
     */
    if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
        header("Location: admin_login.php");
        exit();
    }

    /**
     * Check if user has admin privileges
     */
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: admin_login.php?error=unauthorized");
        exit();
    }

    /**
     * Check session timeout (24 hours)
     */
    if (isset($_SESSION['admin_login_time'])) {
        $login_time = $_SESSION['admin_login_time'];
        $current_time = time();
        $hours_diff = ($current_time - $login_time) / 3600;

        if ($hours_diff > 24) {
            session_destroy();
            header("Location: admin_login.php?error=expired");
            exit();
        }
    }
    ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
        }

        .admin-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 1);
            transition: all 0.3s ease;
        }

        .admin-card:hover {
            box-shadow: 0 10px 25px -5px rgba(236, 72, 153, 0.1);
        }

        /* Fullscreen intro animation */
        @keyframes screenSwipeUp {
            0% {
                transform: translateY(0);
                opacity: 1;
            }

            100% {
                transform: translateY(-100vh);
                opacity: 0;
            }
        }

        .screen-swipe-up {
            animation: screenSwipeUp 0.8s ease-out forwards;
        }

        @keyframes fade-in {
            0% {
                opacity: 0;
                transform: scale(0.95);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out;
        }

        .tab-active {
            background: #ec4899;
            color: white;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #fbcfe8;
            border-radius: 20px;
        }

        /* Responsive improvements */
        @media (max-width: 640px) {
            .tab-active {
                font-size: 0.875rem;
            }

            button {
                touch-action: manipulation;
                min-height: 44px;
            }

            input,
            select,
            textarea {
                font-size: 16px;
                /* Prevents zoom on iOS */
            }
        }
    </style>
</head>

<body class="text-gray-700">
    <!-- Full Screen Introduction -->
    <div id="fullscreenIntro" class="fixed inset-0 z-[300] bg-white flex items-center justify-center">
        <div class="text-center">
            <h1 id="introTitle"
                class="text-8xl md:text-9xl font-black text-gray-900 tracking-tighter leading-none transition-all duration-1000 ease-out">
                Admin <span class="text-pink-500">Panel</span>
            </h1>
            <p class="text-xl text-gray-600 mt-6 opacity-0 animate-fade-in"
                style="animation-delay: 0.5s; animation-fill-mode: both;">
                Manage your facilities portal
            </p>
        </div>
    </div>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50">
        <!-- Header -->
        <header class="bg-white/80 backdrop-blur-xl border-b border-slate-200 px-4 sm:px-6 md:px-8 py-4 md:py-6">
            <div class="grid grid-cols-2 lg:grid-cols-3 items-center gap-2 sm:gap-4">
                <!-- Logo and Title -->
                <div class="flex items-center gap-3 sm:gap-5">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center shadow-xl p-1 overflow-hidden border border-slate-100 rounded-xl">
                        <img src="../assets/logo.jpg" alt="Logo" class="w-full h-full">
                    </div>
                    <div>
                        <h1
                            class="font-extrabold text-gray-900 text-lg sm:text-xl md:text-2xl tracking-tight uppercase">
                            Admin Panel</h1>
                        <p class="text-slate-500 font-medium text-xs sm:text-sm hidden sm:block">La Rose Noire
                            Facilities Management</p>
                    </div>
                </div>

                <!-- Profile Information (Centered) -->
                <div class="hidden lg:flex justify-center">
                    <div
                        class="hidden lg:flex items-center gap-3 md:gap-6 px-3 md:px-6 py-2 md:py-3 bg-gradient-to-r from-pink-50/50 to-purple-50/50 rounded-xl md:rounded-2xl border border-pink-100/50">
                        <div class="flex items-center gap-2 md:gap-3">
                            <div
                                class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-pink-500 to-fuchsia-300 rounded-lg md:rounded-xl flex items-center justify-center shadow-sm overflow-hidden relative">
                                <?php echo getEmployeePhotoImg($_SESSION['employee_id'] ?? '', 'w-full h-full object-cover rounded-xl'); ?>
                            </div>
                            <div class="text-left">
                                <div class="text-xs md:text-sm font-semibold text-gray-900">
                                    <?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Unknown User'); ?>
                                </div>
                                <div class="text-[10px] md:text-xs text-gray-600">
                                    <?php echo htmlspecialchars($_SESSION['department'] ?? 'N/A'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="h-6 md:h-8 w-px bg-gradient-to-b from-pink-200 to-purple-200"></div>
                        <div class="flex items-center gap-2 md:gap-3">
                            <div
                                class="flex items-center gap-2 px-2 py-1 bg-green-100/80 rounded-lg border border-green-200/50">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-xs font-semibold text-green-700">Active</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                <i class="fa-solid fa-clock mr-1"></i>
                                <?php echo isset($_SESSION['admin_login_time']) ? date('M j, g:i A', $_SESSION['admin_login_time']) : 'Unknown'; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons (Right aligned) -->
                <div class="flex items-center justify-end gap-4">

                    <a href="admin_logout.php"
                        class="px-4 sm:px-6 py-2 sm:py-3 bg-pink-500 text-white rounded-xl hover:bg-pink-600 transition-colors font-semibold text-sm sm:text-base">
                        <i class="fa-solid fa-arrow-left mr-2"></i>Back to Portal
                    </a>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto p-4 sm:p-6 md:p-8">
            <!-- Tab Navigation -->
            <div class="mb-6 sm:mb-8">
                <div class="flex justify-center">
                    <div
                        class="flex space-x-1 bg-white/50 p-1 rounded-2xl backdrop-blur-sm w-full max-w-md sm:max-w-lg md:max-w-xl">
                        <button onclick="switchTab('announcements')" id="tab-announcements"
                            class="tab-active flex-1 px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-xl font-semibold transition-all text-sm sm:text-base">Announcements</button>
                        <button onclick="switchTab('left-panel')" id="tab-left-panel"
                            class="flex-1 px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-xl font-semibold transition-all text-sm sm:text-base">Left
                            Panel</button>
                        <button onclick="switchTab('apps')" id="tab-apps"
                            class="flex-1 px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-xl font-semibold transition-all text-sm sm:text-base">Apps</button>
                        <button onclick="switchTab('folders')" id="tab-folders"
                            class="flex-1 px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-xl font-semibold transition-all text-sm sm:text-base">Folders</button>
                    </div>
                </div>
            </div>


            <!-- Announcements Tab -->
            <div id="content-announcements" class="tab-content">
                <div class="admin-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Manage Announcements</h2>
                    <form id="announcementForm" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Active</label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" id="announcement-active"
                                        onchange="toggleAnnouncementActive()"
                                        class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                                    <span class="ml-2">Show announcement on portal</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
                            <input type="text" id="announcement-title"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="Announcement title">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                            <textarea id="announcement-message" rows="4"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="Announcement message"></textarea>
                        </div>
                        <button type="submit"
                            class="px-8 py-3 bg-pink-500 text-white rounded-xl hover:bg-pink-600 transition-colors font-semibold">
                            <i class="fa-solid fa-save mr-2"></i>Save Announcement
                        </button>
                    </form>
                </div>
            </div>

            <!-- Left Panel Tab -->
            <div id="content-left-panel" class="tab-content hidden">
                <div class="space-y-8">
                    <!-- General Settings -->
                    <div class="admin-card rounded-3xl p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">General Settings</h3>
                        <div class="space-y-4">
                            <label class="flex items-center">
                                <input type="checkbox" id="weather-enabled" onchange="toggleWeatherWidget()"
                                    class="rounded border-gray-300 text-pink-600 focus:ring-pink-500 w-5 h-5">
                                <span class="ml-3 font-medium text-gray-700">Enable weather widget</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" id="background-enabled" onchange="toggleBackground()"
                                    class="rounded border-gray-300 text-pink-600 focus:ring-pink-500 w-5 h-5">
                                <span class="ml-3 font-medium text-gray-700">Enable 2D floating background</span>
                            </label>
                        </div>
                    </div>

                    <!-- Announcements/Images -->
                    <div class="admin-card rounded-3xl p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Panel Announcements</h3>
                            <button onclick="addNewAnnouncement()"
                                class="px-6 py-2 bg-pink-500 text-white rounded-xl hover:bg-pink-600 transition-colors font-semibold">
                                <i class="fa-solid fa-plus mr-2"></i>Add Announcement
                            </button>
                        </div>
                        <div id="announcements-list" class="space-y-4">
                            <!-- Announcements will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apps Tab -->
            <div id="content-apps" class="tab-content hidden">
                <div class="admin-card rounded-3xl p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Manage Apps</h2>
                        <button onclick="addNewApp()"
                            class="px-6 py-2 bg-pink-500 text-white rounded-xl hover:bg-pink-600 transition-colors font-semibold">
                            <i class="fa-solid fa-plus mr-2"></i>Add New App
                        </button>
                    </div>

                    <div id="apps-list" class="space-y-4 custom-scrollbar max-h-96 overflow-y-auto">
                        <!-- Apps will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Folders Tab -->
            <div id="content-folders" class="tab-content hidden">
                <div class="admin-card rounded-3xl p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Manage Folders</h2>
                        <button onclick="addNewFolder()"
                            class="px-6 py-2 bg-pink-500 text-white rounded-xl hover:bg-pink-600 transition-colors font-semibold">
                            <i class="fa-solid fa-plus mr-2"></i>Create Folder
                        </button>
                    </div>

                    <div id="folders-list" class="space-y-4 custom-scrollbar max-h-96 overflow-y-auto">
                        <!-- Folders will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- App Modal -->
    <div id="appModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-2 sm:p-4 z-50">
        <div
            class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 max-w-sm sm:max-w-md md:max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900" id="appModalTitle">Add New App</h3>
                <button onclick="closeAppModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <form id="appForm" class="space-y-6">
                <input type="hidden" id="app-id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
                        <input type="text" id="app-title" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Folder</label>
                        <input type="text" id="app-folder" list="folder-datalist"
                            placeholder="Select or type folder name..."
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <datalist id="folder-datalist">
                            <!-- Populated via JS -->
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Icon</label>
                        <select id="app-icon" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="">Select an icon...</option>
                            <!-- Medical & Health -->
                            <option value="fa-heart-pulse">💓 Heart Pulse</option>
                            <option value="fa-user-doctor">👨‍⚕️ Doctor</option>
                            <option value="fa-hospital">🏥 Hospital</option>
                            <option value="fa-stethoscope">🩺 Stethoscope</option>
                            <option value="fa-pills">💊 Pills</option>
                            <!-- Business & Office -->
                            <option value="fa-briefcase">💼 Briefcase</option>
                            <option value="fa-building">🏢 Building</option>
                            <option value="fa-chart-line">📈 Chart Line</option>
                            <option value="fa-users">👥 Users</option>
                            <option value="fa-cog">⚙️ Cog</option>
                            <option value="fa-clipboard">📋 Clipboard</option>
                            <!-- Technical -->
                            <option value="fa-computer">🖥️ Computer</option>
                            <option value="fa-server">🖥️ Server</option>
                            <option value="fa-code">💻 Code</option>
                            <option value="fa-database">🗄️ Database</option>
                            <option value="fa-wifi">📶 WiFi</option>
                            <!-- Facilities & Maintenance -->
                            <option value="fa-tools">🔧 Tools</option>
                            <option value="fa-wrench">🔧 Wrench</option>
                            <option value="fa-screwdriver">🪛 Screwdriver</option>
                            <option value="fa-broom">🧹 Broom</option>
                            <option value="fa-dumpster">🚮 Dumpster</option>
                            <!-- Transportation -->
                            <option value="fa-truck">🚚 Truck</option>
                            <option value="fa-car">🚗 Car</option>
                            <option value="fa-bus">🚌 Bus</option>
                            <option value="fa-dolly">🤖 Dolly</option>
                            <!-- Food & Dining -->
                            <option value="fa-utensils">🍽️ Utensils</option>
                            <option value="fa-apple-whole">🍎 Apple</option>
                            <option value="fa-bread-slice">🍞 Bread</option>
                            <option value="fa-mug-hot">☕ Mug Hot</option>
                            <!-- Security & Safety -->
                            <option value="fa-shield">🛡️ Shield</option>
                            <option value="fa-lock">🔒 Lock</option>
                            <option value="fa-key">🔑 Key</option>
                            <option value="fa-eye">👁️ Eye</option>
                            <!-- Communication -->
                            <option value="fa-envelope">✉️ Envelope</option>
                            <option value="fa-phone">📞 Phone</option>
                            <option value="fa-comments">💬 Comments</option>
                            <option value="fa-bell">🔔 Bell</option>
                            <!-- Education -->
                            <option value="fa-graduation-cap">🎓 Graduation Cap</option>
                            <option value="fa-book">📚 Book</option>
                            <option value="fa-chalkboard">👩‍🏫 Chalkboard</option>
                            <!-- Shopping & Commerce -->
                            <option value="fa-shopping-cart">🛒 Shopping Cart</option>
                            <option value="fa-credit-card">💳 Credit Card</option>
                            <option value="fa-receipt">🧾 Receipt</option>
                            <!-- Time & Calendar -->
                            <option value="fa-calendar">📅 Calendar</option>
                            <option value="fa-clock">🕐 Clock</option>
                            <option value="fa-stopwatch">⏱️ Stopwatch</option>
                            <!-- Nature & Environment -->
                            <option value="fa-leaf">🍃 Leaf</option>
                            <option value="fa-tree">🌳 Tree</option>
                            <option value="fa-recycle">♻️ Recycle</option>
                            <!-- Other Common -->
                            <option value="fa-star">⭐ Star</option>
                            <option value="fa-home">🏠 Home</option>
                            <option value="fa-house-user">🏡 House User</option>
                            <option value="fa-list-check">✅ List Check</option>
                            <option value="fa-shirt">👕 Shirt</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Color</label>
                        <select id="app-color" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="pink">Pink</option>
                            <option value="rose">Rose</option>
                            <option value="indigo">Indigo</option>
                            <option value="blue">Blue</option>
                            <option value="amber">Amber</option>
                            <option value="green">Green</option>
                            <option value="purple">Purple</option>
                            <option value="red">Red</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Link URL</label>
                        <input type="text" id="app-link" required
                            placeholder="e.g., ../app/login.php or https://example.com"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="app-description" rows="3" required placeholder="Brief description of the app..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent"></textarea>
                </div>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="app-enabled" checked
                            class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                        <span class="ml-2">Enabled</span>
                    </label>
                    <!-- Order removed -->
                </div>
                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-pink-500 text-white rounded-xl hover:bg-pink-600 transition-colors font-semibold">
                        <i class="fa-solid fa-save mr-2"></i>Save App
                    </button>
                    <button type="button" onclick="closeAppModal()"
                        class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-colors font-semibold">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Announcement Modal -->
    <div id="announcementModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 max-w-sm sm:max-w-md md:max-w-2xl w-full">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900" id="announcementModalTitle">Add Announcement</h3>
                <button onclick="closeAnnouncementModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <form id="panelAnnouncementForm" class="space-y-6">
                <input type="hidden" id="panel-announcement-id">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                    <select id="panel-announcement-type" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <option value="image">Image</option>
                        <option value="text">Text</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
                    <input type="text" id="panel-announcement-title" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>
                <div id="image-fields">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Image File</label>

                    <!-- Current image preview (for editing) -->
                    <div id="current-image-preview" class="mb-4 p-4 bg-gray-50 rounded-xl hidden">
                        <p class="text-sm font-medium text-gray-700 mb-2">Current Image:</p>
                        <img id="current-image-src" src="" alt="Current image"
                            class="w-full h-32 object-cover rounded-lg">
                        <input type="hidden" id="existing-image-path" name="existing_image">
                    </div>

                    <!-- File input -->
                    <input type="file" id="panel-announcement-image" name="image" accept="image/*"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                    <p class="text-xs text-gray-500 mt-1">Supported formats: JPG, PNG, GIF. Max size: 5MB</p>
                    <p class="text-xs text-gray-400 mt-1">Leave empty to keep current image when editing</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Subtitle (optional)</label>
                    <input type="text" id="panel-announcement-subtitle"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="panel-announcement-enabled" checked
                            class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                        <span class="ml-2">Enabled</span>
                    </label>
                </div>
                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-pink-500 text-white rounded-xl hover:bg-pink-600 transition-colors font-semibold">
                        <i class="fa-solid fa-save mr-2"></i>Save Announcement
                    </button>
                    <button type="button" onclick="closeAnnouncementModal()"
                        class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-colors font-semibold">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Folder Modal -->
    <div id="folderModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 max-w-sm sm:max-w-md w-full">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900" id="folderModalTitle">Create Folder</h3>
                <button onclick="closeFolderModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <!-- Form content starts here -->
            <form id="folderForm" class="space-y-6">
                <input type="hidden" id="folder-original-name">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Folder Name</label>
                    <input type="text" id="folder-name" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="folder-enabled" checked
                            class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                        <span class="ml-2">Enabled</span>
                    </label>
                    </label>
                </div>

                <!-- Manage Apps Section -->
                <div id="folder-apps-section" class="border-t border-gray-100 pt-4 hidden">
                    <h4 class="font-bold text-gray-900 mb-4">Apps in this Folder</h4>

                    <!-- Add App to Folder -->
                    <div class="flex gap-2 mb-4">
                        <div class="relative flex-1">
                            <input type="text" id="folder-add-app-search" placeholder="Type to search existing apps..."
                                class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <div id="folder-app-search-results"
                                class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-100 rounded-xl shadow-lg z-50 max-h-48 overflow-y-auto">
                                <!-- Results populate here -->
                            </div>
                        </div>
                    </div>

                    <!-- List of Apps in Folder -->
                    <div id="folder-apps-list"
                        class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar bg-gray-50 rounded-xl p-2">
                        <!-- Apps will be loaded here -->
                        <p class="text-xs text-gray-400 text-center py-2">No apps in this folder.</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-pink-500 text-white rounded-xl hover:bg-pink-600 transition-colors font-semibold">
                        <i class="fa-solid fa-save mr-2"></i>Save Folder
                    </button>
                    <button type="button" onclick="closeFolderModal()"
                        class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-colors font-semibold">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fullscreen introduction animation
        function startIntroAnimation() {
            const introElement = document.getElementById('fullscreenIntro');

            // Main content is already visible underneath with lower z-index

            // Start the screen swipe animation after a brief delay
            setTimeout(() => {
                introElement.classList.add('screen-swipe-up');
            }, 800); // Show title for 0.8 seconds before swiping

            // After swipe animation completes, remove intro overlay
            setTimeout(() => {
                introElement.style.display = 'none';
            }, 1600); // 0.8s delay + 0.8s animation = 1.6s total
        }

        // Tab switching
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            document.querySelectorAll('[id^="tab-"]').forEach(tab => tab.classList.remove('tab-active'));

            // Show selected tab
            document.getElementById(`content-${tabName}`).classList.remove('hidden');
            document.getElementById(`tab-${tabName}`).classList.add('tab-active');
        }



        // Initialize everything on page load
        document.addEventListener('DOMContentLoaded', function () {
            // Load all data immediately while intro plays
            loadAnnouncements();
            loadLeftPanelData();
            loadApps();
            loadFolders();

            // Start intro animation after data starts loading
            setTimeout(() => {
                startIntroAnimation();
            }, 100); // Small delay to ensure DOM is ready
        });

        // Announcements
        async function loadAnnouncements() {
            try {
                // Add cache-busting parameter to ensure fresh data
                const response = await fetch('../data/announcements.json?v=' + Date.now(), {
                    cache: 'no-cache',
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                });
                const data = await response.json();

                document.getElementById('announcement-active').checked = data.active;
                document.getElementById('announcement-title').value = data.title;
                document.getElementById('announcement-message').value = data.message;
            } catch (error) {
                console.error('Error loading announcements:', error);
            }
        }

        document.getElementById('announcementForm').addEventListener('submit', function (e) {
            e.preventDefault();

            showConfirmationModal(
                'Save Announcement',
                'Are you sure you want to save this announcement?',
                async () => {
                    const data = {
                        active: document.getElementById('announcement-active').checked,
                        title: document.getElementById('announcement-title').value,
                        message: document.getElementById('announcement-message').value,
                        updated_at: new Date().toISOString()
                    };

                    try {
                        const response = await fetch('../api/save_announcement.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(data)
                        });

                        if (response.ok) {
                            showSuccessModal('Announcement Saved', 'The announcement has been successfully saved.');
                        } else {
                            alert('Error saving announcement');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error saving announcement');
                    }
                }
            );
        });

        // Left Panel
        async function loadLeftPanelData() {
            try {
                // Add cache-busting parameter to ensure fresh data
                const response = await fetch('../data/left_panel.json?v=' + Date.now(), {
                    cache: 'no-cache',
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                });
                const data = await response.json();

                document.getElementById('weather-enabled').checked = data.weather_enabled;
                if (document.getElementById('background-enabled')) {
                    document.getElementById('background-enabled').checked = data.background_enabled !== false; // Default to true if undefined
                }

                const announcementsList = document.getElementById('announcements-list');
                announcementsList.innerHTML = '';

                data.announcements.forEach(announcement => {
                    const announcementEl = document.createElement('div');
                    announcementEl.className = 'flex items-center justify-between p-4 bg-gray-50 rounded-xl';
                    announcementEl.innerHTML = `
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                                <i class="fa-solid ${announcement.type === 'image' ? 'fa-image' : 'fa-file-text'} text-pink-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">${announcement.title}</h4>
                                <p class="text-sm text-gray-600">${announcement.subtitle || ''}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" ${announcement.enabled ? 'checked' : ''} onchange="toggleAnnouncement('${announcement.id}', this.checked)" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                                <span class="ml-2 text-sm">Enabled</span>
                            </label>
                            <button onclick="editAnnouncement('${announcement.id}')" class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
                                <i class="fa-solid fa-edit"></i>
                            </button>
                            <button onclick="deleteAnnouncement('${announcement.id}')" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `;
                    announcementsList.appendChild(announcementEl);
                });
            } catch (error) {
                console.error('Error loading left panel data:', error);
            }
        }

        async function toggleAnnouncementActive() {
            const active = document.getElementById('announcement-active').checked;
            const data = {
                active: active,
                title: document.getElementById('announcement-title').value,
                message: document.getElementById('announcement-message').value,
                updated_at: new Date().toISOString()
            };

            try {
                const response = await fetch('../api/save_announcement.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Reload data to verify it was saved correctly
                    setTimeout(() => loadAnnouncements(), 500);
                } else {
                    console.error('Error saving announcement toggle:', result.error);
                    alert('Error toggling announcement: ' + (result.error || 'Unknown error'));
                    // Reload current data to revert checkbox if save failed
                    loadAnnouncements();
                }
            } catch (error) {
                console.error('Network error toggling announcement:', error);
                alert('Error toggling announcement: ' + error.message);
                // Reload current data to revert checkbox if save failed
                loadAnnouncements();
            }
        }

        async function toggleAppEnabled(appId, checkbox) {
            const enabled = checkbox.checked;

            try {
                const response = await fetch('../api/save_app.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: appId,
                        enabled: enabled,
                        isToggle: true // Flag to indicate this is just an enabled toggle
                    })
                });

                if (response.ok) {
                    // Reload apps to verify it was saved correctly
                    setTimeout(() => loadApps(), 500);
                } else {
                    alert('Error toggling app');
                    loadApps();
                }
            } catch (error) {
                alert('Error toggling app: ' + error.message);
                loadApps();
            }
        }

        async function toggleWeatherWidget() {
            const enabled = document.getElementById('weather-enabled').checked;

            try {
                const response = await fetch('../api/save_weather.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ weather_enabled: enabled })
                });

                if (response.ok) {
                    // Reload data to verify it was saved correctly
                    setTimeout(() => loadLeftPanelData(), 500);
                } else {
                    const result = await response.json();
                    alert('Error toggling weather widget: ' + (result.error || 'Unknown error'));
                    // Reload current data to revert checkbox if save failed
                    loadLeftPanelData();
                }
            } catch (error) {
                alert('Error toggling weather widget: ' + error.message);
                // Reload current data to revert checkbox if save failed
                loadLeftPanelData();
            }
        }

        async function toggleBackground() {
            const enabled = document.getElementById('background-enabled').checked;

            try {
                const response = await fetch('../api/save_weather.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ background_enabled: enabled })
                });

                if (response.ok) {
                    // Reload data to verify it was saved correctly
                    setTimeout(() => loadLeftPanelData(), 500);
                } else {
                    const result = await response.json();
                    alert('Error toggling background: ' + (result.error || 'Unknown error'));
                    loadLeftPanelData(); // Revert
                }
            } catch (error) {
                alert('Error toggling background: ' + error.message);
                loadLeftPanelData(); // Revert
            }
        }

        async function saveWeatherSettings() {
            const enabled = document.getElementById('weather-enabled').checked;

            try {
                const response = await fetch('../api/save_weather.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ weather_enabled: enabled })
                });

                if (response.ok) {
                    alert('Weather settings saved!');
                } else {
                    alert('Error saving weather settings');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error saving weather settings');
            }
        }

        function addNewAnnouncement() {
            document.getElementById('panel-announcement-id').value = '';
            document.getElementById('panel-announcement-type').value = 'image';
            document.getElementById('panel-announcement-title').value = '';
            document.getElementById('panel-announcement-image').value = '';
            document.getElementById('panel-announcement-subtitle').value = '';
            document.getElementById('panel-announcement-enabled').checked = true;
            document.getElementById('current-image-preview').style.display = 'none';
            document.getElementById('announcementModalTitle').textContent = 'Add Announcement';
            document.getElementById('announcementModal').classList.remove('hidden');
            toggleImageFields('image');
        }

        async function editAnnouncement(id) {
            try {
                const response = await fetch('../data/left_panel.json');
                const data = await response.json();

                const announcement = data.announcements.find(a => a.id === id);
                if (!announcement) {
                    alert('Announcement not found');
                    return;
                }

                document.getElementById('panel-announcement-id').value = announcement.id;
                document.getElementById('panel-announcement-type').value = announcement.type;
                document.getElementById('panel-announcement-title').value = announcement.title;
                document.getElementById('panel-announcement-subtitle').value = announcement.subtitle || '';
                document.getElementById('panel-announcement-enabled').checked = announcement.enabled;

                // Handle image preview
                const imageInput = document.getElementById('panel-announcement-image');
                const imagePreview = document.getElementById('current-image-preview');

                if (announcement.image) {
                    imageInput.required = false;
                    imagePreview.style.display = 'block';
                    const isUploaded = announcement.image.startsWith('announcement_');
                    const imagePath = isUploaded ? '../uploads/' + announcement.image : '../' + announcement.image;
                    document.getElementById('current-image-src').src = imagePath;
                    document.getElementById('existing-image-path').value = announcement.image;
                } else {
                    imageInput.required = true;
                    imagePreview.style.display = 'none';
                    document.getElementById('existing-image-path').value = '';
                }

                document.getElementById('announcementModalTitle').textContent = 'Edit Announcement';
                document.getElementById('announcementModal').classList.remove('hidden');
                toggleImageFields(announcement.type);

            } catch (error) {
                console.error('Error loading announcement:', error);
                alert('Error loading announcement for editing');
            }
        }

        function closeAnnouncementModal() {
            document.getElementById('announcementModal').classList.add('hidden');
        }

        document.getElementById('panel-announcement-type').addEventListener('change', function () {
            toggleImageFields(this.value);
        });

        function toggleImageFields(type) {
            const imageFields = document.getElementById('image-fields');
            if (type === 'image') {
                imageFields.style.display = 'block';
            } else {
                imageFields.style.display = 'none';
            }
        }

        // Apps
        async function loadApps() {
            try {
                const url = '../data/apps.json?v=' + Date.now();
                console.log('Loading apps from:', url);

                // Add cache-busting parameter to ensure fresh data
                const response = await fetch(url, {
                    cache: 'no-cache',
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status} url: ${response.url}`);
                }

                const apps = await response.json();

                const appsList = document.getElementById('apps-list');
                const folderDatalist = document.getElementById('folder-datalist');
                appsList.innerHTML = '';
                folderDatalist.innerHTML = '';

                // Populate Folder Datalist
                const folders = [...new Set(apps.map(app => app.folder).filter(f => f))];
                folders.sort().forEach(folder => {
                    const option = document.createElement('option');
                    option.value = folder;
                    folderDatalist.appendChild(option);
                });

                apps.sort((a, b) => a.order - b.order).forEach(app => {
                    const appEl = document.createElement('div');
                    appEl.className = 'flex items-center justify-between p-4 sm:p-6 bg-gray-50 rounded-xl';
                    appEl.innerHTML = `
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-${app.color}-100 text-${app.color}-600 rounded-2xl sm:rounded-3xl flex items-center justify-center shadow-inner">
                                <i class="fa-solid ${app.icon} text-lg sm:text-2xl"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-gray-900 text-sm sm:text-base truncate">${app.title}</h4>
                                <p class="text-xs text-pink-500 font-semibold uppercase tracking-wider mb-1">${app.folder ? app.folder : 'No Folder'}</p>
                                <p class="text-xs text-gray-500 truncate">${app.link}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 sm:gap-2 flex-wrap">
                            <label class="inline-flex items-center">
                                <input type="checkbox" ${app.enabled ? 'checked' : ''} onchange="toggleAppEnabled('${app.id}', this)" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500 w-4 h-4 sm:w-5 sm:h-5">
                                <span class="ml-1 text-xs sm:text-sm text-gray-600">Enabled</span>
                            </label>
                            <div class="flex gap-1 ml-auto">
                                <button onclick="editApp('${app.id}')" class="px-2 sm:px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-xs sm:text-sm">
                                    <i class="fa-solid fa-edit ${window.innerWidth < 640 ? '' : 'mr-1'}"></i><span class="hidden sm:inline">Edit</span>
                                </button>
                                <button onclick="deleteApp('${app.id}')" class="px-2 sm:px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 text-xs sm:text-sm">
                                    <i class="fa-solid fa-trash ${window.innerWidth < 640 ? '' : 'mr-1'}"></i><span class="hidden sm:inline">Delete</span>
                                </button>
                            </div>
                        </div>
                    `;
                    appsList.appendChild(appEl);
                });
            } catch (error) {
                console.error('Error loading apps:', error);
            }
        }

        function addNewApp() {
            document.getElementById('app-id').value = '';
            document.getElementById('app-title').value = '';
            document.getElementById('app-folder').value = '';
            document.getElementById('app-description').value = '';
            document.getElementById('app-icon').value = '';
            document.getElementById('app-color').value = 'pink';
            document.getElementById('app-link').value = '';
            document.getElementById('app-enabled').checked = true;
            document.getElementById('appModalTitle').textContent = 'Add New App';
            document.getElementById('appModal').classList.remove('hidden');
            // Clear edit ID for new apps
            document.getElementById('appForm').removeAttribute('data-edit-id');
        }

        function closeAppModal() {
            document.getElementById('appModal').classList.add('hidden');
            // Clear edit ID when closing modal
            document.getElementById('appForm').removeAttribute('data-edit-id');
        }

        document.getElementById('appForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Check if this is an edit operation
            const editId = document.getElementById('appForm').getAttribute('data-edit-id');
            const isEdit = !!editId;

            showConfirmationModal(
                isEdit ? 'Update App' : 'Save App',
                `Are you sure you want to ${isEdit ? 'update' : 'save'} this app?`,
                async () => {
                    const appData = {
                        id: isEdit ? editId : (document.getElementById('app-id').value || generateId()),
                        title: document.getElementById('app-title').value,
                        folder: document.getElementById('app-folder').value,
                        description: document.getElementById('app-description').value,
                        icon: document.getElementById('app-icon').value,
                        color: document.getElementById('app-color').value,
                        link: document.getElementById('app-link').value,
                        enabled: document.getElementById('app-enabled').checked,
                        order: 999,
                        isEdit: isEdit // Flag to indicate edit operation
                    };

                    try {
                        const response = await fetch('../api/save_app.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(appData)
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            closeAppModal();
                            loadApps(); // Refresh the apps list
                            showSuccessModal(
                                isEdit ? 'App Updated' : 'App Saved',
                                `The app has been successfully ${isEdit ? 'updated' : 'saved'}.`
                            );
                        } else {
                            alert('Error saving app: ' + (result.error || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error saving app: ' + error.message);
                    }
                }
            );
        });

        function generateId() {
            return 'app_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        // Form submission for announcements
        document.getElementById('panelAnnouncementForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('id', document.getElementById('panel-announcement-id').value);
            formData.append('type', document.getElementById('panel-announcement-type').value);
            formData.append('title', document.getElementById('panel-announcement-title').value);
            formData.append('subtitle', document.getElementById('panel-announcement-subtitle').value);
            formData.append('enabled', document.getElementById('panel-announcement-enabled').checked ? '1' : '0');

            // Handle image upload
            const imageFile = document.getElementById('panel-announcement-image').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            } else if (document.getElementById('existing-image-path').value) {
                formData.append('existing_image', document.getElementById('existing-image-path').value);
            }

            try {
                const response = await fetch('../api/upload_announcement.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    alert('Announcement saved successfully!');
                    closeAnnouncementModal();
                    loadLeftPanelData();
                } else {
                    alert('Error saving announcement: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error saving announcement');
            }
        });

        // Announcement toggle functionality
        async function toggleAnnouncement(id, enabled) {
            try {
                const response = await fetch('../api/toggle_announcement.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, enabled: enabled })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Refresh the left panel data to show changes
                    loadLeftPanelData();
                } else {
                    alert('Error toggling announcement: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error toggling announcement');
            }
        }



        // Announcement delete functionality
        async function deleteAnnouncement(id) {
            showConfirmationModal(
                'Delete Announcement',
                'Are you sure you want to delete this announcement? This action cannot be undone.',
                async () => {
                    try {
                        const response = await fetch('../api/delete_announcement.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: id })
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            // Refresh the left panel data to show changes
                            loadLeftPanelData();
                            showSuccessModal('Announcement Deleted', 'The announcement has been successfully deleted.');
                        } else {
                            alert('Error deleting announcement: ' + (result.error || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error deleting announcement');
                    }
                }
            );
        }

        // Placeholder functions for edit/delete (to be implemented)
        async function editApp(id) {
            try {
                const response = await fetch('../data/apps.json?v=' + Date.now());
                const apps = await response.json();

                const app = apps.find(a => a.id === id);
                if (!app) {
                    alert('App not found');
                    return;
                }

                // Populate the modal with existing data
                document.getElementById('appModalTitle').textContent = 'Edit App';
                document.getElementById('app-title').value = app.title;
                document.getElementById('app-folder').value = app.folder || '';
                document.getElementById('app-description').value = app.description || '';
                document.getElementById('app-icon').value = app.icon;
                document.getElementById('app-color').value = app.color;
                document.getElementById('app-link').value = app.link;

                // Store the app ID for updating
                document.getElementById('appForm').setAttribute('data-edit-id', id);

                document.getElementById('appModal').classList.remove('hidden');
            } catch (error) {
                console.error('Error loading app for editing:', error);
                alert('Error loading app for editing');
            }
        }
        async function deleteApp(id) {
            showConfirmationModal(
                'Delete App',
                'Are you sure you want to delete this app? This action cannot be undone.',
                async () => {
                    try {
                        const response = await fetch('../api/delete_app.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: id })
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            loadApps(); // Refresh the apps list
                            showSuccessModal('App Deleted', 'The app has been successfully deleted.');
                        } else {
                            alert('Error deleting app: ' + (result.error || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error deleting app');
                    }
                }
            );
        }

        // Employee photo fallback function
        function tryPhotoExtensions(employeeId, imgElement) {
            const extensions = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'];
            const baseUrl = 'http://10.2.0.8/lrnph/emp_photos/';
            let currentIndex = 0;

            function tryNextExtension() {
                if (currentIndex >= extensions.length) {
                    // All extensions failed, show fallback icon
                    const fallbackIcon = imgElement.nextElementSibling;
                    if (fallbackIcon && fallbackIcon.classList.contains('fa-user')) {
                        fallbackIcon.style.display = 'block';
                        imgElement.style.display = 'none';
                    }
                    return;
                }

                const extension = extensions[currentIndex];
                const newSrc = baseUrl + employeeId + '.' + extension;

                // Skip if this is the same as current src
                if (newSrc === imgElement.src) {
                    currentIndex++;
                    tryNextExtension();
                    return;
                }

                const testImg = new Image();
                testImg.onload = function () {
                    // Success! Use this extension
                    imgElement.src = newSrc;
                };
                testImg.onerror = function () {
                    // Try next extension
                    currentIndex++;
                    tryNextExtension();
                };
                testImg.src = newSrc;
            }

            tryNextExtension();
        }
    </script>

    <!-- Confirmation Modal -->
    <div id="confirmationModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 max-w-xs sm:max-w-sm md:max-w-md w-full">
            <div class="text-center">
                <div
                    class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2" id="confirmationTitle">Confirm Action</h3>
                <p class="text-gray-600 mb-6" id="confirmationMessage">Are you sure you want to proceed?</p>
                <div class="flex gap-4">
                    <button onclick="closeConfirmationModal()"
                        class="flex-1 px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-colors font-semibold">
                        Cancel
                    </button>
                    <button id="confirmActionBtn" onclick="executeConfirmedAction()"
                        class="flex-1 px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-colors font-semibold">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-2 sm:p-4 z-50">
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 max-w-xs sm:max-w-sm md:max-w-md w-full">
            <div class="text-center">
                <div
                    class="w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-check-circle text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2" id="successTitle">Success!</h3>
                <p class="text-gray-600 mb-6" id="successMessage">Operation completed successfully.</p>
                <button onclick="closeSuccessModal()"
                    class="px-8 py-3 bg-pink-500 text-white rounded-xl hover:bg-pink-600 transition-colors font-semibold">
                    OK
                </button>
            </div>
        </div>
    </div>



    <script>
        // Folders Management

        async function loadFolders() {
            try {
                // Fetch both apps (for counts) and folders (for metadata)
                const [appsRes, foldersRes] = await Promise.all([
                    fetch('../data/apps.json?v=' + Date.now(), { cache: 'no-cache' }),
                    fetch('../data/folders.json?v=' + Date.now(), { cache: 'no-cache' })
                ]);

                const apps = await appsRes.json();
                const foldersConfig = await foldersRes.json(); // Array of {name, enabled}

                // Get all unique folder names from apps
                const appFolders = [...new Set(apps.map(app => app.folder).filter(f => f))];

                // Merge with foldersConfig to get complete list (some config folders might be empty)
                const allFolderNames = new Set([...appFolders, ...foldersConfig.map(f => f.name)]);

                const combinedFolders = Array.from(allFolderNames).map(name => {
                    const config = foldersConfig.find(f => strCompare(f.name, name));
                    const appCount = apps.filter(a => strCompare(a.folder, name)).length;
                    return {
                        name: name,
                        enabled: config ? config.enabled : true, // Default to true if not in config
                        count: appCount
                    };
                });

                const list = document.getElementById('folders-list');
                list.innerHTML = '';

                combinedFolders.sort((a, b) => a.name.localeCompare(b.name)).forEach(folder => {
                    const el = document.createElement('div');
                    el.className = 'flex items-center justify-between p-4 sm:p-6 bg-gray-50 rounded-xl';
                    el.innerHTML = `
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-pink-100 text-pink-600 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-folder text-2xl"></i>
        </div>
        <div>
            <h4 class="font-bold text-gray-900">${folder.name}</h4>
            <p class="text-sm text-gray-500">${folder.count} Apps</p>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <label class="inline-flex items-center">
            <input type="checkbox" ${folder.enabled ? 'checked' : ''}
                onchange="toggleFolderEnabled('${folder.name}', this.checked)"
                class="rounded border-gray-300 text-pink-600 focus:ring-pink-500 w-5 h-5">
            <span class="ml-2 text-sm text-gray-600">Enabled</span>
        </label>
        <button onclick="editFolder('${folder.name}')"
            class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            <i class="fa-solid fa-edit"></i>
        </button>
        <button onclick="deleteFolder('${folder.name}')"
            class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
            <i class="fa-solid fa-trash"></i>
        </button>
    </div>
    `;
                    list.appendChild(el);
                });

            } catch (error) {
                console.error('Error loading folders:', error);
            }
        }

        function strCompare(a, b) {
            return (a || '').toLowerCase() === (b || '').toLowerCase();
        }

        function addNewFolder() {
            document.getElementById('folderModalTitle').textContent = 'Create Folder';
            document.getElementById('folder-name').value = '';
            document.getElementById('folder-original-name').value = '';
            document.getElementById('folder-enabled').checked = true;
            document.getElementById('folder-apps-section').classList.add('hidden'); // Hide for new folders (must save first)
            document.getElementById('folderModal').classList.remove('hidden');
        }

        async function editFolder(name) {
            try {
                // Fetch current state
                const response = await fetch('../data/folders.json?v=' + Date.now());
                const folders = await response.json();
                const folder = folders.find(f => strCompare(f.name, name)) || { name: name, enabled: true };

                document.getElementById('folderModalTitle').textContent = 'Edit Folder';
                document.getElementById('folder-name').value = folder.name;
                document.getElementById('folder-original-name').value = folder.name;
                document.getElementById('folder-enabled').checked = folder.enabled;

                // Show Apps Section
                document.getElementById('folder-apps-section').classList.remove('hidden');

                // Load Apps for this folder
                renderFolderApps(folder.name);
                setupFolderAppSearch();

                document.getElementById('folderModal').classList.remove('hidden');
            } catch (e) {
                console.error(e);
                // Fallback
                document.getElementById('folderModalTitle').textContent = 'Edit Folder';
                document.getElementById('folder-name').value = name;
                document.getElementById('folder-original-name').value = name;
                document.getElementById('folder-enabled').checked = true;
                document.getElementById('folder-apps-section').classList.remove('hidden');
                renderFolderApps(name);
                setupFolderAppSearch();
                document.getElementById('folderModal').classList.remove('hidden');
            }
        }

        async function renderFolderApps(folderName) {
            const list = document.getElementById('folder-apps-list');
            try {
                const response = await fetch('../data/apps.json?v=' + Date.now());
                const apps = await response.json();
                const folderApps = apps.filter(a => strCompare(a.folder, folderName));

                if (folderApps.length === 0) {
                    list.innerHTML = '<p class="text-xs text-gray-400 text-center py-2">No apps in this folder.</p>';
                    return;
                }

                list.innerHTML = '';
                folderApps.forEach(app => {
                    const el = document.createElement('div');
                    el.className = 'flex items-center justify-between p-2 bg-white rounded-lg border border-gray-100';
                    el.innerHTML = `
                        <div class="flex items-center gap-2 overflow-hidden">
                            <div class="w-6 h-6 bg-${app.color}-100 text-${app.color}-600 rounded flex items-center justify-center text-xs">
                                <i class="fa-solid ${app.icon}"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700 truncate">${app.title}</span>
                        </div>
                        <button type="button" onclick="removeAppFromFolder('${app.id}', '${folderName}')" class="text-red-400 hover:text-red-600 px-2" title="Remove from folder">
                             <i class="fa-solid fa-times"></i>
                        </button>
                    `;
                    list.appendChild(el);
                });
            } catch (e) {
                console.error(e);
                list.innerHTML = '<p class="text-xs text-red-400 text-center">Error loading apps.</p>';
            }
        }

        async function removeAppFromFolder(appId, currentFolderName) {
            if (!confirm('Remove this app from the folder?')) return;
            await updateAppFolder(appId, '');
            renderFolderApps(currentFolderName);
            loadFolders(); // Refresh counts
            loadApps(); // Refresh main list
        }

        async function addAppToFolder(appId, folderName) {
            await updateAppFolder(appId, folderName);
            document.getElementById('folder-add-app-search').value = '';
            document.getElementById('folder-app-search-results').classList.add('hidden');
            renderFolderApps(folderName);
            loadFolders(); // Refresh counts
            loadApps(); // Refresh main list
        }

        async function updateAppFolder(appId, newFolderName) {
            try {
                // Fetch current app state
                const response = await fetch('../data/apps.json?v=' + Date.now());
                const apps = await response.json();
                const app = apps.find(a => a.id === appId);

                if (!app) return;

                app.folder = newFolderName;
                // Add isEdit flag for save_app.php
                app.isEdit = true;

                await fetch('../api/save_app.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(app)
                });
            } catch (e) {
                console.error(e);
                alert('Failed to update app folder.');
            }
        }

        function setupFolderAppSearch() {
            const input = document.getElementById('folder-add-app-search');
            const results = document.getElementById('folder-app-search-results');

            input.oninput = async function () {
                const query = this.value.toLowerCase();
                if (query.length < 1) {
                    results.classList.add('hidden');
                    return;
                }

                const response = await fetch('../data/apps.json?v=' + Date.now());
                const apps = await response.json();

                // Filter apps that match query AND are not already in this folder
                // AND not 'implicit' folder matches (exact match only)
                const currentFolderName = document.getElementById('folder-original-name').value;
                const matches = apps.filter(a =>
                    a.title.toLowerCase().includes(query) &&
                    !strCompare(a.folder, currentFolderName)
                );

                results.innerHTML = '';
                if (matches.length > 0) {
                    matches.slice(0, 5).forEach(app => { // Limit to 5
                        const div = document.createElement('div');
                        div.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-0 flex items-center gap-2';
                        div.innerHTML = `
                            <div class="w-6 h-6 bg-${app.color}-100 text-${app.color}-600 rounded flex items-center justify-center text-xs">
                                <i class="fa-solid ${app.icon}"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">${app.title}</p>
                                <p class="text-xs text-gray-400">${app.folder ? 'In: ' + app.folder : 'No Folder'}</p>
                            </div>
                            <i class="fa-solid fa-plus text-pink-500"></i>
                        `;
                        div.onclick = () => addAppToFolder(app.id, currentFolderName);
                        results.appendChild(div);
                    });
                    results.classList.remove('hidden');
                } else {
                    results.innerHTML = '<div class="p-3 text-xs text-gray-400 text-center">No matching apps found.</div>';
                    results.classList.remove('hidden');
                }
            };

            // Hide on click outside
            document.addEventListener('click', function (e) {
                if (!input.contains(e.target) && !results.contains(e.target)) {
                    results.classList.add('hidden');
                }
            });
        }
        function closeFolderModal() {
            document.getElementById('folderModal').classList.add('hidden');
            document.getElementById('folder-app-search-results').classList.add('hidden'); // Hide search results
            document.getElementById('folder-add-app-search').value = ''; // Clear search input
        }

        document.getElementById('folderForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const newName = document.getElementById('folder-name').value;
            const originalName = document.getElementById('folder-original-name').value;
            const enabled = document.getElementById('folder-enabled').checked;

            const endpoint = originalName && originalName !== newName ? '../api/rename_folder.php' : '../api/save_folder.php';

            const payload = {
                name: newName,
                enabled: enabled
            };

            if (originalName && originalName !== newName) {
                payload.original_name = originalName;
            }

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    closeFolderModal();
                    loadFolders();
                    loadApps(); // Apps might have been updated (renamed folder)
                    showSuccessModal(originalName ? 'Folder Updated' : 'Folder Created', 'Changes saved successfully.');
                } else {
                    alert('Error saving folder');
                }
            } catch (e) {
                console.error(e);
                alert('Error saving folder');
            }
        });

        async function toggleFolderEnabled(name, enabled) {
            try {
                await fetch('../api/save_folder.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, enabled })
                });
                // No need to reload, visual state reflects change
            } catch (e) {
                console.error(e);
                alert('Error updating folder state');
                loadFolders(); // Revert
            }
        }

        async function deleteFolder(name) {
            showConfirmationModal(
                'Delete Folder',
                `Are you sure you want to delete folder "${name}"? This will remove the folder grouping from all apps inside it.`,
                async () => {
                    try {
                        const response = await fetch('../api/delete_folder.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ name })
                        });

                        if (response.ok) {
                            loadFolders();
                            loadApps(); // Apps will update to "No Folder"
                            showSuccessModal('Folder Deleted', 'Folder and app associations removed.');
                        } else {
                            alert('Error deleting folder');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Error deleting folder');
                    }
                }
            );
        }

        // Confirmation modal functions
        let pendingAction = null;

        function showConfirmationModal(title, message, actionCallback) {
            document.getElementById('confirmationTitle').textContent = title;
            document.getElementById('confirmationMessage').textContent = message;
            pendingAction = actionCallback;
            document.getElementById('confirmationModal').classList.remove('hidden');
        }

        function closeConfirmationModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
            pendingAction = null;
        }

        function executeConfirmedAction() {
            if (pendingAction) {
                pendingAction();
                closeConfirmationModal();
            }
        }

        // Success modal functions
        function showSuccessModal(title, message) {
            document.getElementById('successTitle').textContent = title;
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.remove('hidden');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }

    </script>
</body>

</html>