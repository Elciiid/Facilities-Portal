<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facilities Portal - La Rose Noire</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            height: 100vh;
            margin: 0;
            display: flex;
            overflow: hidden;
        }


        .portal-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 1);
            cursor: pointer;
            z-index: 10;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .portal-card:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            border-color: #f472b6;
        }

        /* Custom Scrollbar - Hidden by default, visible on hover */
        #cardGrid {
            scrollbar-width: thin;
            scrollbar-color: transparent transparent;
            transition: scrollbar-color 0.3s ease;
        }

        #cardGrid:hover {
            scrollbar-color: rgba(255, 211, 233, 0.57) transparent;
        }

        /* Webkit browsers (Chrome, Safari, Edge) */
        #cardGrid::-webkit-scrollbar {
            width: 8px;
        }

        #cardGrid::-webkit-scrollbar-track {
            background: transparent;
        }

        #cardGrid::-webkit-scrollbar-thumb {
            background: transparent;
            border-radius: 10px;
            transition: background 0.3s ease;
        }

        #cardGrid:hover::-webkit-scrollbar-thumb {
            background: rgba(244, 114, 182, 0.5);
        }

        #cardGrid:hover::-webkit-scrollbar-thumb:hover {
            background: rgba(244, 114, 182, 0.8);
        }



        #animationCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        /* Folder Expansion Overlay */
        .folder-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .folder-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .folder-expanded {
            background: #f5f5f7;
            border-radius: 3rem;
            padding: 3rem;
            width: fit-content;
            min-width: 620px;
            max-width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .folder-overlay.active .folder-expanded {
            transform: scale(1);
            opacity: 1;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #fbcfe8;
            border-radius: 20px;
        }

        .today {
            background: #ec4899;
            color: white !important;
            border-radius: 12px;
        }

        .nav-btn:hover {
            color: #ec4899;
            transform: scale(1.2);
        }

        /* Weather section transition for consistent layout */
        #weather-section {
            transition: all 0.4s ease-in-out;
        }

        /* Responsive improvements */
        @media (max-width: 640px) {
            .portal-card {
                touch-action: manipulation;
            }

            .nav-btn {
                min-width: 44px;
                min-height: 44px;
            }
        }

        /* Sidebar positioning for all screen sizes */
        aside:first-of-type {
            position: fixed;
            left: 0;
            top: 0;
            z-index: 50;
        }

        aside:last-of-type {
            position: fixed;
            right: 0;
            top: 0;
            z-index: 50;
        }

        /* Responsive sidebar behavior for tablets and smaller screens */
        @media (max-width: 1200px) {

            /* Left sidebar slides in/out on tablets */
            aside:first-of-type {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }

            aside:first-of-type.show-sidebar {
                transform: translateX(0);
            }

            /* Right sidebar slides in/out on tablets */
            aside:last-of-type {
                transform: translateX(100%);
                transition: transform 0.3s ease;
                box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            }

            aside:last-of-type.show-sidebar {
                transform: translateX(0);
            }

            /* Main content takes full width on tablets */
            main {
                margin-left: 0;
                margin-right: 0;
            }

            /* Sidebar toggle buttons */
            .sidebar-toggle {
                position: fixed;
                top: 50%;
                transform: translateY(-50%);
                z-index: 60;
                width: 48px;
                height: 88px;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border: 2px solid rgba(236, 72, 153, 0.3);
                border-radius: 0 24px 24px 0;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                outline: none;
                opacity: 1;
                visibility: visible;
            }

            .sidebar-toggle:hover {
                background: rgba(255, 255, 255, 1);
                transform: translateY(-50%) scale(1.05);
            }

            .sidebar-toggle.right {
                right: 0;
                border-radius: 24px 0 0 24px;
                left: auto;
            }


            .sidebar-toggle i {
                color: #ec4899;
                font-size: 1.2rem;
            }


            /* Ensure buttons stay visible in all states */
            .sidebar-toggle:active,
            .sidebar-toggle:focus {
                transform: translateY(-50%) scale(0.95);
                opacity: 1;
                visibility: visible;
            }
        }

        /* Hide toggle buttons on larger screens (desktop) */
        @media (min-width: 1201px) {
            .sidebar-toggle {
                display: none !important;
            }
        }

        /* Overlay when sidebar is open */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 45;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        @media (min-width: 1025px) {

            /* On larger screens, ensure sidebars are always visible and properly positioned */
            aside:first-of-type,
            aside:last-of-type {
                position: fixed;
            }

            /* Main content adjusts for fixed sidebars */
            main {
                margin-left: 16rem;
                /* w-64 = 16rem */
                margin-right: 16rem;
                /* w-64 = 16rem */
            }

            @media (min-width: 1280px) {
                main {
                    margin-left: 18rem;
                    /* w-72 = 18rem */
                    margin-right: 18rem;
                    /* w-72 = 18rem */
                }
            }

            @media (min-width: 1536px) {
                main {
                    margin-left: 20rem;
                    /* w-80 = 20rem */
                    margin-right: 20rem;
                    /* w-80 = 20rem */
                }
            }
        }

        /* Border trace effect for admin button */
        .border-trace {
            position: relative;
            transition: all 0.3s ease;
        }

        .border-trace::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 2rem;
            padding: 2px;
            background: linear-gradient(90deg, #ec4899, rgba(103, 202, 206, 0.48), #ec4899);
            background-size: 200% 200%;
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .border-trace:hover::before {
            opacity: 1;
            animation: border-trace 2s linear infinite;
        }

        .border-trace:hover {
            /* Subtle hover effect with border tracing only */
        }

        @keyframes border-trace {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Announcement carousel styles */
        .announcement-slide {
            width: 100%;
        }

        .announcement-dot {
            cursor: pointer;
        }

        .announcement-dot:hover {
            background-color: #ec4899 !important;
        }

        /* Custom scrollbar for apps grid */
        .scrollbar-hide {
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE and Edge */
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        .scrollbar-show:hover,
        .scrollbar-show:focus {
            scrollbar-width: thin;
            /* Firefox */
            -ms-overflow-style: auto;
            /* IE and Edge */
        }

        .scrollbar-show:hover::-webkit-scrollbar,
        .scrollbar-show:focus::-webkit-scrollbar {
            display: block;
            /* Chrome, Safari, Opera */
            width: 6px;
        }

        .scrollbar-show:hover::-webkit-scrollbar-track,
        .scrollbar-show:focus::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .scrollbar-show:hover::-webkit-scrollbar-thumb,
        .scrollbar-show:focus::-webkit-scrollbar-thumb {
            background: rgba(236, 72, 153, 0.6);
            border-radius: 10px;
        }

        .scrollbar-show:hover::-webkit-scrollbar-thumb:hover,
        .scrollbar-show:focus::-webkit-scrollbar-thumb:hover {
            background: rgba(236, 72, 153, 0.8);
        }

        /* Custom animations for announcement banner */
        @keyframes fade-in-up {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes progress-bar {
            0% {
                width: 0%;
            }

            50% {
                width: 70%;
            }

            100% {
                width: 100%;
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
        }

        .animate-progress-bar {
            animation: progress-bar 2s ease-in-out infinite;
        }

        @keyframes fade-in {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out;
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

        /* Default Grid (3 Columns) */

        #cardGrid.grid-default {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        /* Compact Grid (5 Columns) */
        #cardGrid.grid-compact {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        #cardGrid.grid-compact .portal-card {
            min-height: 200px;
            padding: 1.5rem;
            border-radius: 2rem;
        }

        #cardGrid.grid-compact .app-icon-container {
            width: 3.5rem;
            height: 3.5rem;
        }

        /* Micro Grid (8 Columns) */
        #cardGrid.grid-micro {
            grid-template-columns: repeat(8, minmax(0, 1fr));
            gap: 0.75rem;
        }

        #cardGrid.grid-micro .portal-card {
            min-height: 140px;
            padding: 1rem;
            border-radius: 1.5rem;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        #cardGrid.grid-micro .app-icon-container {
            width: 3rem;
            height: 3rem;
            margin-bottom: 0.5rem;
        }

        #cardGrid.grid-micro h3 {
            font-size: 0.75rem !important;
            line-height: 1rem !important;
            margin-bottom: 0 !important
        }

        #cardGrid.grid-compact h3 {
            font-size: 1.1rem !important;
            line-height: 1.3rem !important;
            margin-top: auto;
            margin-bottom: 0 !important;
        }

        #cardGrid.grid-micro p {
            display: none;
            /* Remove descriptions */
        }

        #cardGrid.grid-micro .icon-preview-area {
            margin-bottom: 1rem;
            gap: 0.25rem;
        }

        #cardGrid.grid-micro .icon-preview-area div {
            width: 1.5rem;
            height: 1.5rem;
        }

        #cardGrid.grid-compact p {
            display: none !important;
        }

        /* Ensure mobile responsiveness overrides these when screen is small */
        @media (max-width: 1024px) {

            #cardGrid.grid-compact,
            #cardGrid.grid-micro {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>

<body class="text-gray-700 relative">

    <!-- Animated Announcement Banner -->
    <div id="announcement-banner"
        class="fixed top-0 left-0 right-0 z-40 transform -translate-y-full transition-all duration-700 ease-out">
        <div id="announcement-banner-container"
            class="bg-gradient-to-r from-pink-500 via-rose-500 to-pink-600 text-white py-2 sm:py-3 px-2 sm:px-4 shadow-2xl relative overflow-hidden">
            <!-- Animated background elements -->
            <div
                class="absolute inset-0 bg-gradient-to-r from-pink-400/30 via-rose-400/30 to-pink-400/30 animate-pulse">
            </div>

            <!-- Floating particles -->
            <div class="absolute top-2 right-10 w-3 h-3 bg-white/20 rounded-full animate-ping"></div>
            <div class="absolute top-4 right-20 w-2 h-2 bg-white/30 rounded-full animate-ping"
                style="animation-delay: 0.5s;"></div>
            <div class="absolute top-1 right-32 w-1.5 h-1.5 bg-white/40 rounded-full animate-ping"
                style="animation-delay: 1s;"></div>
            <div class="absolute bottom-2 left-10 w-2.5 h-2.5 bg-white/25 rounded-full animate-bounce"></div>
            <div class="absolute bottom-1 left-20 w-1 h-1 bg-white/35 rounded-full animate-bounce"
                style="animation-delay: 0.7s;"></div>

            <!-- Gradient border animation -->
            <div
                class="absolute inset-0 rounded-b-lg bg-gradient-to-r from-transparent via-white/10 to-transparent animate-pulse">
            </div>

            <div class="relative flex items-center justify-center gap-2 sm:gap-4 max-w-6xl mx-auto">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-white/20 rounded-full flex items-center justify-center animate-spin"
                        style="animation-duration: 3s;">
                        <i id="announcement-icon" class="fa-solid fa-bullhorn text-lg sm:text-xl"></i>
                    </div>
                </div>
                <div class="flex-1 text-center min-w-0">
                    <h3 id="announcement-title"
                        class="text-sm sm:text-base md:text-lg font-bold mb-1 animate-fade-in-up truncate"
                        style="animation-delay: 0.2s;">Official Portal of LRNPH</h3>
                    <p id="announcement-message"
                        class="text-pink-50 text-xs sm:text-sm animate-fade-in-up hidden sm:block"
                        style="animation-delay: 0.4s;">Hello everyone! This is the portal for LRNPH</p>
                </div>
                <button onclick="closeAnnouncement()"
                    class="flex-shrink-0 text-pink-200 hover:text-white transition-all duration-300 hover:scale-110 animate-fade-in-up ml-2"
                    style="animation-delay: 0.6s;">
                    <i class="fa-solid fa-times text-base sm:text-lg"></i>
                </button>
            </div>

            <!-- Progress bar animation -->
            <div class="absolute bottom-0 left-0 h-1 bg-white/30 animate-pulse">
                <div class="h-full bg-white animate-progress-bar"></div>
            </div>
        </div>
    </div>


    <aside
        class="w-64 sm:w-72 md:w-80 bg-white/80 backdrop-blur-xl border-r border-slate-200 flex flex-col h-screen shrink-0 relative z-50">
        <div class="flex-1 flex flex-col pt-12 px-8">
            <div class="flex items-center gap-5 mb-8 ml-2">
                <div
                    class="w-16 h-16 flex items-center justify-center shadow-xl p-1 overflow-hidden border border-slate-100">
                    <img src="assets/logo.jpg" alt="Logo" class="w-full h-full">
                </div>
                <div>
                    <h1 class="font-extrabold text-gray-900 leading-none text-xl tracking-tight uppercase">La Rose Noire
                    </h1>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="h-1 w-4 bg-pink-500 rounded-full"></span>
                        <p class="text-[9px] uppercase tracking-[0.4em] text-pink-500 font-black">Facilities</p>
                    </div>
                </div>
            </div>

            <div id="weather-section" class="mb-6 bg-slate-50 p-6 rounded-[2.5rem] border border-slate-200 text-center">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Mabalacat City Weather
                </p>
                <div id="weather-container" class="flex flex-col items-center">
                    <div class="flex items-center gap-4">
                        <i id="weather-icon" class="fa-solid fa-cloud-sun text-4xl text-pink-500"></i>
                        <span id="weather-temp" class="text-4xl font-black text-gray-900">--°C</span>
                    </div>
                    <p class="text-[11px] font-bold text-pink-500 uppercase mt-2">Mabalacat City</p>
                </div>
            </div>

            <nav class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pb-6">
                <a href="admin/admin_login.php" target="_blank"
                    class="group flex items-center gap-4 px-7 py-4 rounded-[2rem] bg-white/90 backdrop-blur-sm border border-pink-400/30 text-pink-600 font-bold text-sm relative overflow-hidden border-trace">
                    <!-- Animated background effect -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out">
                    </div>

                    <!-- Icon with glow effect -->
                    <div class="relative w-8 flex justify-center">
                        <i
                            class="fa-solid fa-cog text-xl drop-shadow-sm group-hover:rotate-90 transition-transform duration-300"></i>
                    </div>

                    <!-- Text with subtle glow -->
                    <span class="relative tracking-tight font-semibold drop-shadow-sm">Admin Login</span>

                    <!-- Subtle sparkle effect -->
                    <div class="absolute top-2 right-3 w-1 h-1 bg-white/60 rounded-full animate-pulse"></div>
                    <div class="absolute bottom-3 left-4 w-0.5 h-0.5 bg-white/40 rounded-full animate-pulse"
                        style="animation-delay: 0.5s;"></div>
                </a>

                <!-- Dynamic announcements will be loaded here -->
                <div id="left-panel-announcements" class="space-y-4">
                    <!-- Announcements loaded from left_panel.json -->
                </div>

                <!-- Dynamic logos will be loaded here -->
                <div id="left-panel-logos" class="flex justify-center pt-4">
                    <!-- Logos loaded from left_panel.json -->
                </div>
            </nav>
        </div>
    </aside>

    <canvas id="animationCanvas"></canvas>

    <!-- Folder Expansion Overlay -->
    <div id="folderOverlay" class="folder-overlay" onclick="closeFolder()">
        <div id="expandedFolder" class="folder-expanded" onclick="event.stopPropagation()">

            <div class="relative pt-6">
                <!-- Close Button -->
                <button onclick="closeFolder()"
                    class="absolute top-0 right-0 w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-500 transition-colors">
                    <i class="fa-solid fa-times text-sm"></i>
                </button>

                <h3 id="folderTitle" class="text-4xl font-black text-gray-800 text-center mb-10">Folder Name</h3>

                <div id="folderAppsGrid" class="flex flex-wrap justify-center gap-12 pb-8">
                    <!-- Apps inside folder -->
                </div>
            </div>
        </div>
    </div>

    <!-- Full Screen Introduction -->
    <div id="fullscreenIntro" class="fixed inset-0 z-[300] bg-white flex items-center justify-center">
        <div class="text-center">
            <h1 id="introTitle"
                class="text-8xl md:text-9xl font-black text-gray-900 tracking-tighter leading-none transition-all duration-1000 ease-out">
                Facilities <span class="text-pink-500">Pro</span>
            </h1>
            <p class="text-xl text-gray-600 mt-6 opacity-0 animate-fade-in"
                style="animation-delay: 0.5s; animation-fill-mode: both;">
                Centralized workspace for compliance and reporting
            </p>
        </div>
    </div>

    <main
        class="flex-1 h-screen flex flex-col items-center justify-center p-4 sm:p-6 md:p-8 lg:p-12 overflow-hidden z-10">
        <div class="w-full max-w-[1100px]">
            <header class="mb-8 sm:mb-10 md:mb-14 flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div class="text-center lg:text-left">
                    <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-gray-900 tracking-tighter leading-none">
                        Facilities <span class="text-pink-500">Pro</span></h2>
                    <p class="text-slate-500 font-medium text-lg sm:text-xl mt-4">Centralized workspace for compliance
                        and reporting</p>
                </div>

                <div
                    class="bg-white/80 backdrop-blur-md p-1.5 rounded-2xl border border-slate-200 flex items-center gap-1 shadow-sm h-fit">
                    <button onclick="setGridDensity('default')"
                        class="grid-btn px-4 py-2 text-[10px] font-bold rounded-xl transition-all"
                        id="btn-default">3/Row</button>
                    <button onclick="setGridDensity('compact')"
                        class="grid-btn px-4 py-2 text-[10px] font-bold text-slate-500 rounded-xl hover:bg-slate-200/50 transition-all"
                        id="btn-compact">5/Row</button>
                    <button onclick="setGridDensity('micro')"
                        class="grid-btn px-4 py-2 text-[10px] font-bold text-slate-500 rounded-xl hover:bg-slate-200/50 transition-all"
                        id="btn-micro">8/Row</button>
                </div>
            </header>

            <div id="cardGrid"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8 max-h-[400px] sm:max-h-[500px] md:max-h-[600px] overflow-y-auto scrollbar-hide hover:scrollbar-show">
                <!-- Apps will be loaded dynamically -->
            </div>
        </div>
    </main>

    <aside
        class="w-64 sm:w-72 md:w-80 bg-white/80 backdrop-blur-xl border-l border-slate-200 flex flex-col h-screen shrink-0 z-50">
        <div class="flex-1 flex flex-col pt-24 px-8">
            <div class="mb-8 text-center">
                <h3 id="currentMonthYear" class="text-xl font-black text-gray-900 tracking-tight">...</h3>
                <div class="flex justify-center gap-4 mt-4">
                    <i class="fa-solid fa-chevron-left nav-btn text-slate-400 cursor-pointer"
                        onclick="changeMonth(-1)"></i>
                    <i class="fa-solid fa-chevron-right nav-btn text-slate-400 cursor-pointer"
                        onclick="changeMonth(1)"></i>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-6 shadow-xl border border-slate-100 mb-8">
                <div
                    class="grid grid-cols-7 mb-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                </div>
                <div id="calendarDays" class="grid grid-cols-7 gap-y-2 text-center text-xs font-bold text-gray-600">
                </div>
            </div>

            <div id="holidayList" class="flex-1 overflow-y-auto custom-scrollbar space-y-4 pb-12"></div>
        </div>
    </aside>

    <!-- Sidebar toggle functions (defined before buttons to avoid ReferenceError) -->
    <script>
        function toggleLeftSidebar() {
            const leftSidebar = document.querySelector('aside:first-of-type');
            const rightSidebar = document.querySelector('aside:last-of-type');
            const overlay = document.querySelector('.sidebar-overlay');
            const leftButton = document.querySelector('.sidebar-toggle.left-toggle');
            const rightButton = document.querySelector('.sidebar-toggle.right-toggle');

            if (leftSidebar) {
                const isOpen = leftSidebar.classList.toggle('show-sidebar');

                // Close right sidebar if it's open
                if (rightSidebar && rightSidebar.classList.contains('show-sidebar')) {
                    rightSidebar.classList.remove('show-sidebar');
                    // Show right button again
                    if (rightButton) {
                        rightButton.style.opacity = '1';
                        rightButton.style.visibility = 'visible';
                        rightButton.style.pointerEvents = 'auto';
                    }
                }

                // Handle left button visibility
                if (leftButton) {
                    leftButton.style.opacity = isOpen ? '0' : '1';
                    leftButton.style.visibility = isOpen ? 'hidden' : 'visible';
                    leftButton.style.pointerEvents = isOpen ? 'none' : 'auto';
                }
            }

            // Toggle overlay
            if (overlay) {
                const hasOpenSidebar = document.querySelector('aside.show-sidebar');
                overlay.classList.toggle('active', !!hasOpenSidebar);
            }
        }

        function toggleRightSidebar() {
            const leftSidebar = document.querySelector('aside:first-of-type');
            const rightSidebar = document.querySelector('aside:last-of-type');
            const overlay = document.querySelector('.sidebar-overlay');
            const leftButton = document.querySelector('.sidebar-toggle.left-toggle');
            const rightButton = document.querySelector('.sidebar-toggle.right-toggle');

            if (rightSidebar) {
                const isOpen = rightSidebar.classList.toggle('show-sidebar');

                // Close left sidebar if it's open
                if (leftSidebar && leftSidebar.classList.contains('show-sidebar')) {
                    leftSidebar.classList.remove('show-sidebar');
                    // Show left button again
                    if (leftButton) {
                        leftButton.style.opacity = '1';
                        leftButton.style.visibility = 'visible';
                        leftButton.style.pointerEvents = 'auto';
                    }
                }

                // Handle right button visibility
                if (rightButton) {
                    rightButton.style.opacity = isOpen ? '0' : '1';
                    rightButton.style.visibility = isOpen ? 'hidden' : 'visible';
                    rightButton.style.pointerEvents = isOpen ? 'none' : 'auto';
                }
            }

            // Toggle overlay
            if (overlay) {
                const hasOpenSidebar = document.querySelector('aside.show-sidebar');
                overlay.classList.toggle('active', !!hasOpenSidebar);
            }
        }

        function closeSidebars() {
            const leftSidebar = document.querySelector('aside:first-of-type');
            const rightSidebar = document.querySelector('aside:last-of-type');
            const overlay = document.querySelector('.sidebar-overlay');
            const leftButton = document.querySelector('.sidebar-toggle.left-toggle');
            const rightButton = document.querySelector('.sidebar-toggle.right-toggle');

            // Close both sidebars
            if (leftSidebar) leftSidebar.classList.remove('show-sidebar');
            if (rightSidebar) rightSidebar.classList.remove('show-sidebar');

            // Hide overlay
            if (overlay) overlay.classList.remove('active');

            // Show buttons again when sidebars are closed
            if (leftButton) {
                leftButton.style.opacity = '1';
                leftButton.style.visibility = 'visible';
                leftButton.style.pointerEvents = 'auto';
            }
            if (rightButton) {
                rightButton.style.opacity = '1';
                rightButton.style.visibility = 'visible';
                rightButton.style.pointerEvents = 'auto';
            }
        }

        function setGridDensity(mode) {
            const grid = document.getElementById('cardGrid');
            const buttons = document.querySelectorAll('.grid-btn');

            // Remove all grid classes
            grid.classList.remove('grid-default', 'grid-compact', 'grid-micro');

            // Add selected class
            grid.classList.add('grid-' + mode);

            // Update Button UI
            buttons.forEach(btn => {
                btn.classList.remove('bg-white', 'shadow-sm', 'text-slate-500');
                btn.classList.add('text-slate-500');
            });

            const activeBtn = document.getElementById('btn-' + mode);
            activeBtn.classList.remove('text-slate-500');
            activeBtn.classList.add('bg-white', 'shadow-sm');

            // Save preference to localStorage
            localStorage.setItem('portal_grid_density', mode);
        }

        // Apply saved preference on load
        document.addEventListener('DOMContentLoaded', () => {
            const savedMode = localStorage.getItem('portal_grid_density') || 'default';
            setGridDensity(savedMode);
        });
    </script>

    <!-- Sidebar Toggle Buttons (visible on tablets and smaller) -->
    <button class="sidebar-toggle left-toggle" onclick="toggleLeftSidebar()" type="button">
        <i class="fa-solid fa-bars"></i>
    </button>

    <button class="sidebar-toggle right right-toggle" onclick="toggleRightSidebar()" type="button">
        <i class="fa-solid fa-newspaper"></i>
    </button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="closeSidebars()"></div>

    <div id="imageModal" class="fixed inset-0 z-[100] hidden bg-black/90 flex items-center justify-center p-6"
        onclick="this.classList.add('hidden')">
        <!-- Close Button -->
        <button onclick="event.stopPropagation(); this.parentElement.classList.add('hidden')"
            class="absolute top-4 right-4 z-[101] w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-white hover:text-gray-200 transition-all duration-200">
            <i class="fa-solid fa-times text-xl"></i>
        </button>

        <img id="modalImg" src="" class="max-w-full max-h-full rounded-3xl shadow-2xl object-contain">
    </div>

    <script>
        // Load announcements
        async function loadAnnouncements() {
            try {
                // Add cache-busting parameter to ensure fresh data
                const response = await fetch('data/announcements.json?v=' + Date.now(), {
                    cache: 'no-cache',
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                });
                const data = await response.json();

                const banner = document.getElementById('announcement-banner');
                const title = document.getElementById('announcement-title');
                const message = document.getElementById('announcement-message');
                const icon = document.getElementById('announcement-icon');

                if (data.active && data.title && data.message) {
                    title.textContent = data.title;
                    message.textContent = data.message;

                    // Set icon based on type
                    switch (data.type) {
                        case 'warning':
                            icon.className = 'fa-solid fa-triangle-exclamation text-2xl animate-bounce';
                            break;
                        case 'success':
                            icon.className = 'fa-solid fa-check-circle text-2xl animate-bounce';
                            break;
                        case 'error':
                            icon.className = 'fa-solid fa-times-circle text-2xl animate-bounce';
                            break;
                        default:
                            icon.className = 'fa-solid fa-bullhorn text-2xl animate-bounce';
                    }

                    // Animate banner slide down
                    banner.classList.remove('-translate-y-full');
                    banner.classList.add('translate-y-0');
                } else {
                    // Animate banner slide up
                    banner.classList.remove('translate-y-0');
                    banner.classList.add('-translate-y-full');
                    // Background handled by left_panel settings
                }
            } catch (error) {
                console.error('Error loading announcements:', error);
            }
        }

        // Load left panel data
        async function loadLeftPanelData() {
            try {
                // Add cache-busting parameter to ensure fresh data
                const response = await fetch('data/left_panel.json?v=' + Date.now(), {
                    cache: 'no-cache',
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                });
                const data = await response.json();

                // Handle weather section
                const weatherSection = document.getElementById('weather-section');
                if (data.weather_enabled) {
                    weatherSection.style.opacity = '1';
                    weatherSection.style.pointerEvents = 'auto';
                    weatherSection.style.transform = 'scale(1)';
                    fetchMabalacatWeather();
                } else {
                    weatherSection.style.opacity = '0';
                    weatherSection.style.pointerEvents = 'none';
                    weatherSection.style.transform = 'scale(0.95)';
                }

                // Handle 2D Background
                const bgCanvas = document.getElementById('animationCanvas');
                if (data.background_enabled !== false) { // Default to true
                    startInfiniteBackground();
                    if (bgCanvas) bgCanvas.style.opacity = '1';
                } else {
                    if (bgCanvas) bgCanvas.style.opacity = '0';
                }

                // Handle announcements as carousel
                const announcementsContainer = document.getElementById('left-panel-announcements');
                const enabledAnnouncements = data.announcements.filter(a => a.enabled && a.type === 'image');

                if (enabledAnnouncements.length > 0) {
                    announcementsContainer.innerHTML = `
                    <div class="relative group">
                        <!-- Carousel Container -->
                        <div id="announcements-carousel" class="overflow-hidden rounded-[2.5rem]">
                            <div id="announcements-slides" class="flex transition-transform duration-500 ease-in-out">
                                ${enabledAnnouncements.map((announcement, index) => {
                        // Check if image is in uploads directory (uploaded images have specific naming pattern)
                        const isUploadedImage = announcement.image.startsWith('announcement_') && (announcement.image.includes('.jpg') || announcement.image.includes('.png') || announcement.image.includes('.gif'));
                        const imagePath = isUploadedImage ? 'uploads/' + announcement.image : announcement.image;
                        console.log('Announcement:', announcement.title, 'Image path:', imagePath, 'Is uploaded:', isUploadedImage);
                        return `
                                        <div class="announcement-slide flex-shrink-0 w-full group relative cursor-pointer" onclick="openImageModal('${imagePath}')">
                                            <div class="bg-slate-100/50 p-2 rounded-[2.5rem] border border-slate-200 transition-all group-hover:bg-white shadow-sm">
                                                <div class="relative overflow-hidden rounded-[2.2rem]" style="aspect-ratio: 4/5;">
                                                    <img src="${imagePath}" alt="${announcement.title}" class="w-full h-full object-cover transition-all duration-700 group-hover:scale-110" onerror="this.src='assets/logo.jpg'; this.alt='Image failed to load: ${imagePath}'">
                                                </div>
                                                <div class="px-5 py-5 text-center">
                                                    <p class="text-[11px] font-extrabold text-gray-800 leading-snug uppercase">${announcement.title}</p>
                                                    <p class="text-[8px] text-gray-500 break-all">${imagePath}</p>
                                                    ${announcement.subtitle ? `<p class="text-[9px] font-bold text-pink-500 mt-2 uppercase tracking-widest">${announcement.subtitle}</p>` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                    }).join('')}
                            </div>
                        </div>

                        <!-- Navigation Arrows -->
                        ${enabledAnnouncements.length > 1 ? `
                            <button id="announcement-prev" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white rounded-full shadow-lg flex items-center justify-center text-pink-600 hover:text-pink-700 transition-all duration-200 opacity-0 group-hover:opacity-100 z-10">
                                <i class="fa-solid fa-chevron-left text-sm"></i>
                            </button>
                            <button id="announcement-next" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white rounded-full shadow-lg flex items-center justify-center text-pink-600 hover:text-pink-700 transition-all duration-200 opacity-0 group-hover:opacity-100 z-10">
                                <i class="fa-solid fa-chevron-right text-sm"></i>
                            </button>

                            <!-- Dots Indicator -->
                            <div class="flex justify-center mt-3 space-x-2">
                                ${enabledAnnouncements.map((_, index) => `
                                    <button class="announcement-dot w-2 h-2 rounded-full transition-all duration-300 ${index === 0 ? 'bg-pink-500' : 'bg-pink-200'} cursor-pointer"></button>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                `;

                    // Initialize carousel if there are multiple announcements
                    if (enabledAnnouncements.length > 1) {
                        initializeAnnouncementCarousel(enabledAnnouncements.length);
                    }
                } else {
                    announcementsContainer.innerHTML = '';
                }

                // Handle logos
                const logosContainer = document.getElementById('left-panel-logos');
                logosContainer.innerHTML = '';

                data.logos.forEach(logo => {
                    if (!logo.enabled) return;

                    const logoEl = document.createElement('img');
                    logoEl.src = logo.image;
                    logoEl.alt = logo.alt;
                    logoEl.className = 'max-w-[120px] h-auto opacity-80 hover:opacity-100 transition-opacity';
                    logosContainer.appendChild(logoEl);
                });

            } catch (error) {
                console.error('Error loading left panel data:', error);
            }
        }

        // Load apps and folders
        let globalAppsData = [];
        let globalFoldersData = [];

        async function loadApps() {
            try {
                const timestamp = Date.now();

                // Fetch both apps and folders
                const [appsRes, foldersRes] = await Promise.all([
                    fetch('data/apps.json?v=' + timestamp, { cache: 'no-cache' }),
                    fetch('data/folders.json?v=' + timestamp, { cache: 'no-cache' })
                ]);

                const apps = await appsRes.json();
                const folders = await foldersRes.json();

                globalAppsData = apps;
                globalFoldersData = folders;

                renderMainGrid(apps, folders);

            } catch (error) {
                console.error('Error loading apps/folders:', error);
            }
        }

        function renderMainGrid(apps, folders) {
            const cardGrid = document.getElementById('cardGrid');
            cardGrid.innerHTML = '';

            const enabledFolders = folders.filter(f => f.enabled).sort((a, b) => a.order - b.order);
            const folderMap = new Map();

            // Initialize folder map
            enabledFolders.forEach(folder => {
                folderMap.set(folder.name, []);
            });

            const looseApps = [];

            // Distribute apps
            const enabledApps = apps.filter(app => app.enabled).sort((a, b) => a.order - b.order);

            enabledApps.forEach(app => {
                if (app.folder && folderMap.has(app.folder)) {
                    folderMap.get(app.folder).push(app);
                } else {
                    looseApps.push(app);
                }
            });

            // 1. Render Folders
            enabledFolders.forEach(folder => {
                const folderApps = folderMap.get(folder.name);

                // Skip folders with no apps
                if (!folderApps || folderApps.length === 0) {
                    return;
                }

                const folderCard = document.createElement('div');
                folderCard.className = 'portal-card h-full min-h-[280px] rounded-[2rem] sm:rounded-[3rem] md:rounded-[3.5rem] p-6 sm:p-8 md:p-10 flex flex-col';

                // Create row preview (up to 3 items)
                const miniGridHtml = folderApps.slice(0, 3).map(app => `
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-${app.color}-100 rounded-2xl flex items-center justify-center transition-transform hover:scale-110">
                    <i class="fa-solid ${app.icon} text-${app.color}-500 text-lg sm:text-xl"></i>
                </div>
            `).join('');

                folderCard.innerHTML = `
                <div class="absolute top-6 right-6 text-gray-200">
                    <i class="fa-solid fa-folder-open text-2xl"></i>
                </div>

                <div class="icon-preview-area flex justify-start gap-3 w-full mb-8">
                        ${miniGridHtml}
                </div>
                
                <div class="text-left mt-auto mb-2">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-gray-800 leading-tight">${folder.name}</h3>
                    <p class="text-pink-500 font-bold text-[10px] sm:text-xs uppercase tracking-[0.15em] mt-1">${folderApps.length} APPS</p>
                </div>
            `;

                folderCard.onclick = () => openFolder(folder.name, folderApps);

                cardGrid.appendChild(folderCard);
            });

            // 2. Render Loose Apps
            looseApps.forEach(app => {
                const cardEl = document.createElement('div');
                cardEl.onclick = () => window.open(app.link, '_blank');
                cardEl.className = 'portal-card h-full min-h-[280px] rounded-[2rem] sm:rounded-[3rem] md:rounded-[3.5rem] p-6 sm:p-8 md:p-10 flex flex-col';
                cardEl.innerHTML = `
                <div class="app-icon-container w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 bg-${app.color}-50 text-${app.color}-500 rounded-2xl sm:rounded-3xl flex items-center justify-center mb-4 sm:mb-6 md:mb-8 shadow-inner">
                    <i class="fa-solid ${app.icon} text-2xl sm:text-3xl"></i>
                </div>
                <h3 class="text-lg sm:text-xl md:text-2xl font-extrabold text-gray-800 mb-2 sm:mb-3">${app.title}</h3>
                <p class="text-slate-500 font-medium text-xs sm:text-sm leading-relaxed">${app.description}</p>
            `;

                cardGrid.appendChild(cardEl);
            });
        }

        function openFolder(folderName, apps) {
            const overlay = document.getElementById('folderOverlay');
            const title = document.getElementById('folderTitle');
            const grid = document.getElementById('folderAppsGrid');

            title.textContent = folderName;
            grid.innerHTML = '';

            apps.forEach(app => {
                const cardEl = document.createElement('div');
                cardEl.onclick = (e) => {
                    e.stopPropagation();
                    window.open(app.link, '_blank');
                };
                cardEl.className = 'flex flex-col items-center cursor-pointer w-[120px] sm:w-[140px]';
                cardEl.innerHTML = `
                <div class="folder-app-icon-container w-20 h-20 sm:w-24 sm:h-24 bg-${app.color}-50 rounded-[2rem] flex items-center justify-center mb-4 shadow-sm">
                    <i class="fa-solid ${app.icon} text-${app.color}-500 text-3xl sm:text-4xl"></i>
                </div>
                <h4 class="font-extrabold text-gray-800 text-center text-sm sm:text-base leading-tight w-full">${app.title}</h4>
            `;

                // GSAP Hover for modal items
                const iconContainer = cardEl.querySelector('.folder-app-icon-container');
                cardEl.addEventListener('mouseenter', () => {
                    gsap.to(iconContainer, {
                        scale: 1.15,
                        y: -4,
                        duration: 0.3,
                        ease: "power2.out"
                    });
                });
                cardEl.addEventListener('mouseleave', () => {
                    gsap.to(iconContainer, {
                        scale: 1,
                        y: 0,
                        duration: 0.3,
                        ease: "power2.out"
                    });
                });

                grid.appendChild(cardEl);
            });

            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeFolder() {
            const overlay = document.getElementById('folderOverlay');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        function closeAnnouncement() {
            const banner = document.getElementById('announcement-banner');
            banner.classList.remove('translate-y-0');
            banner.classList.add('-translate-y-full');
        }


        // Announcement carousel functionality
        let announcementCarouselInterval;
        let currentAnnouncementSlide = 0;

        function initializeAnnouncementCarousel(totalSlides) {
            const carousel = document.getElementById('announcements-slides');
            const prevBtn = document.getElementById('announcement-prev');
            const nextBtn = document.getElementById('announcement-next');
            const dots = document.querySelectorAll('.announcement-dot');

            function updateCarousel() {
                carousel.style.transform = `translateX(-${currentAnnouncementSlide * 100}%)`;

                // Update dots
                dots.forEach((dot, index) => {
                    dot.classList.toggle('bg-pink-500', index === currentAnnouncementSlide);
                    dot.classList.toggle('bg-pink-200', index !== currentAnnouncementSlide);
                });
            }

            function nextSlide() {
                currentAnnouncementSlide = (currentAnnouncementSlide + 1) % totalSlides;
                updateCarousel();
            }

            function prevSlide() {
                currentAnnouncementSlide = (currentAnnouncementSlide - 1 + totalSlides) % totalSlides;
                updateCarousel();
            }

            function goToSlide(slideIndex) {
                currentAnnouncementSlide = slideIndex;
                updateCarousel();
            }

            // Event listeners
            if (nextBtn) nextBtn.addEventListener('click', nextSlide);
            if (prevBtn) prevBtn.addEventListener('click', prevSlide);

            // Dot navigation
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => goToSlide(index));
            });

            // Auto-rotation every 5 seconds
            if (announcementCarouselInterval) {
                clearInterval(announcementCarouselInterval);
            }
            announcementCarouselInterval = setInterval(nextSlide, 5000);

            // Pause auto-rotation on hover
            const carouselContainer = document.getElementById('announcements-carousel').parentElement;
            carouselContainer.addEventListener('mouseenter', () => {
                clearInterval(announcementCarouselInterval);
            });
            carouselContainer.addEventListener('mouseleave', () => {
                announcementCarouselInterval = setInterval(nextSlide, 5000);
            });

            // Show/hide navigation on hover
            carouselContainer.addEventListener('mouseenter', () => {
                if (prevBtn) prevBtn.style.opacity = '1';
                if (nextBtn) nextBtn.style.opacity = '1';
            });
            carouselContainer.addEventListener('mouseleave', () => {
                if (prevBtn) prevBtn.style.opacity = '0';
                if (nextBtn) nextBtn.style.opacity = '0';
            });
        }

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

        // Track current data states for change detection
        let currentAnnouncementData = null;
        let currentLeftPanelData = null;
        let currentAppsData = null;
        let currentFoldersData = null;

        // Function to check if data has changed
        async function checkForUpdates() {
            try {
                // Check announcements
                const announcementsResponse = await fetch('data/announcements.json?v=' + Date.now(), {
                    cache: 'no-cache'
                });
                const newAnnouncementData = await announcementsResponse.json();

                // Check left panel
                const leftPanelResponse = await fetch('data/left_panel.json?v=' + Date.now(), {
                    cache: 'no-cache'
                });
                const newLeftPanelData = await leftPanelResponse.json();

                // Check apps & folders
                const appsResponse = await fetch('data/apps.json?v=' + Date.now(), { cache: 'no-cache' });
                const newAppsData = await appsResponse.json();

                const foldersResponse = await fetch('data/folders.json?v=' + Date.now(), { cache: 'no-cache' });
                const newFoldersData = await foldersResponse.json();

                // Compare and update announcements
                if (JSON.stringify(newAnnouncementData) !== JSON.stringify(currentAnnouncementData)) {
                    currentAnnouncementData = newAnnouncementData;
                    loadAnnouncements();
                    showUpdateNotification('Announcements updated');
                    console.log('Announcements updated from admin changes');
                }

                // Compare and update left panel
                if (JSON.stringify(newLeftPanelData) !== JSON.stringify(currentLeftPanelData)) {
                    currentLeftPanelData = newLeftPanelData;
                    loadLeftPanelData();
                    showUpdateNotification('Portal settings updated');
                    console.log('Left panel updated from admin changes');
                }

                // Compare and update apps OR folders
                if (JSON.stringify(newAppsData) !== JSON.stringify(currentAppsData) ||
                    JSON.stringify(newFoldersData) !== JSON.stringify(currentFoldersData)) {

                    currentAppsData = newAppsData;
                    currentFoldersData = newFoldersData;
                    loadApps();
                    showUpdateNotification('Apps layout updated');
                    console.log('Apps updated from admin changes');
                }

            } catch (error) {
                console.error('Error checking for updates:', error);
                // Fallback: refresh everything if there's an error
                loadAnnouncements();
                loadLeftPanelData();
                loadApps();
            }
        }

        // Initialize everything on page load
        document.addEventListener('DOMContentLoaded', async function () {
            // Load all data immediately while intro plays
            await loadAnnouncements();
            await loadLeftPanelData();
            await loadApps();

            // Initialize current data states for comparison
            try {
                const announcementsResponse = await fetch('data/announcements.json?v=' + Date.now());
                currentAnnouncementData = await announcementsResponse.json();

                const leftPanelResponse = await fetch('data/left_panel.json?v=' + Date.now());
                currentLeftPanelData = await leftPanelResponse.json();

                const appsResponse = await fetch('data/apps.json?v=' + Date.now());
                currentAppsData = await appsResponse.json();

                const foldersResponse = await fetch('data/folders.json?v=' + Date.now());
                currentFoldersData = await foldersResponse.json();
            } catch (error) {
                console.error('Error initializing data states:', error);
            }

            // Check for updates every 3 seconds (very responsive to admin changes)
            setInterval(checkForUpdates, 3000);

            // Function to show update notifications
            function showUpdateNotification(message) {
                // Remove any existing notification
                const existingNotification = document.getElementById('update-notification');
                if (existingNotification) {
                    existingNotification.remove();
                }

                // Create new notification
                const notification = document.createElement('div');
                notification.id = 'update-notification';
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
                notification.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-sync-alt fa-spin"></i>
                    <span class="text-sm font-medium">${message}</span>
                </div>
            `;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 3000);
            }

            function closeSidebars() {
                const leftSidebar = document.querySelector('aside:first-of-type');
                const rightSidebar = document.querySelector('aside:last-of-type');
                const overlay = document.querySelector('.sidebar-overlay');

                if (leftSidebar) leftSidebar.classList.remove('show-sidebar');
                if (rightSidebar) rightSidebar.classList.remove('show-sidebar');
                if (overlay) overlay.classList.remove('active');
            }

            // Keyboard support for closing sidebars
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeSidebars();
                }
            });

            // Start intro animation after data starts loading
            setTimeout(() => {
                startIntroAnimation();
            }, 100); // Small delay to ensure DOM is ready
        });

        async function fetchMabalacatWeather() {
            const lat = 15.2210;
            const lon = 120.5735;
            try {
                const response = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`);
                const data = await response.json();
                document.getElementById('weather-temp').innerText = `${Math.round(data.current_weather.temperature)}°C`;
            } catch (e) { document.getElementById('weather-temp').innerText = "28°C"; }
        }
        fetchMabalacatWeather();

        function openImageModal(src) {
            document.getElementById('modalImg').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        let viewDate = new Date();
        const PH_HOLIDAYS = {
            2026: {
                0: { 1: "New Year's Day" },
                1: { 17: "Chinese New Year", 25: "EDSA People Power Anniversary" },
                2: { 20: "Eid'l Fitr (Estimated)" },
                3: { 2: "Maundy Thursday", 3: "Good Friday", 4: "Black Saturday", 9: "Araw ng Kagitingan" },
                4: { 1: "Labor Day", 28: "Eid'l Adha (Estimated)" },
                5: { 12: "Independence Day" },
                7: { 21: "Ninoy Aquino Day", 31: "National Heroes Day" },
                10: { 1: "All Saints' Day", 2: "All Souls' Day", 30: "Bonifacio Day" },
                11: { 8: "Feast of the Immaculate Conception", 24: "Christmas Eve", 25: "Christmas Day", 30: "Rizal Day", 31: "Last Day of Year" }
            },
            2027: {
                0: { 1: "New Year's Day" },
                1: { 6: "Chinese New Year", 25: "EDSA People Power Anniversary" },
                2: { 10: "Eid'l Fitr (Estimated)", 25: "Maundy Thursday", 26: "Good Friday", 27: "Black Saturday" },
                3: { 9: "Araw ng Kagitingan" },
                4: { 1: "Labor Day", 17: "Eid'l Adha (Estimated)" },
                5: { 12: "Independence Day" },
                7: { 21: "Ninoy Aquino Day", 30: "National Heroes Day" },
                10: { 1: "All Saints' Day", 2: "All Souls' Day", 30: "Bonifacio Day" },
                11: { 8: "Feast of the Immaculate Conception", 24: "Christmas Eve", 25: "Christmas Day", 30: "Rizal Day", 31: "Last Day of Year" }
            },
            2028: {
                0: { 1: "New Year's Day", 26: "Chinese New Year" },
                1: { 25: "EDSA People Power Anniversary", 27: "Eid'l Fitr (Estimated)" },
                3: { 9: "Araw ng Kagitingan", 13: "Maundy Thursday", 14: "Good Friday", 15: "Black Saturday" },
                4: { 1: "Labor Day", 5: "Eid'l Adha (Estimated)" },
                5: { 12: "Independence Day" },
                7: { 21: "Ninoy Aquino Day", 28: "National Heroes Day" },
                10: { 1: "All Saints' Day", 2: "All Souls' Day", 30: "Bonifacio Day" },
                11: { 8: "Feast of the Immaculate Conception", 24: "Christmas Eve", 25: "Christmas Day", 30: "Rizal Day", 31: "Last Day of Year" }
            }
        };

        function updateCalendar() {
            const year = viewDate.getFullYear(); const month = viewDate.getMonth(); const today = new Date();
            document.getElementById('currentMonthYear').innerText = new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(viewDate);
            const firstDay = new Date(year, month, 1).getDay(); const daysInMonth = new Date(year, month + 1, 0).getDate();
            const calendarContainer = document.getElementById('calendarDays'); const listContainer = document.getElementById('holidayList');

            calendarContainer.innerHTML = '';
            for (let i = 0; i < firstDay; i++) calendarContainer.innerHTML += '<div></div>';

            const annualHolidays = PH_HOLIDAYS[year] || {};
            const monthHolidays = annualHolidays[month] || {};
            for (let day = 1; day <= daysInMonth; day++) {
                const isToday = day === today.getDate() && month === today.getMonth() && year === today.getFullYear();
                const hName = monthHolidays[day];
                calendarContainer.innerHTML += `<div class="py-2 ${isToday ? 'today' : ''}">${day}${hName ? '<div class="w-1 h-1 bg-pink-400 mx-auto mt-1 rounded-full"></div>' : ''}</div>`;
            }

            listContainer.innerHTML = '';
            const sortedDays = Object.keys(monthHolidays).sort((a, b) => a - b);
            if (sortedDays.length === 0) {
                listContainer.innerHTML = '<p class="text-[11px] italic text-gray-400">No major holidays this month.</p>';
            } else {
                sortedDays.forEach(day => {
                    listContainer.innerHTML += `
                    <div class="flex items-center gap-4 bg-white p-4 rounded-3xl border border-slate-100 shadow-sm">
                        <div class="w-10 h-10 bg-pink-100 text-pink-600 rounded-2xl flex items-center justify-center shrink-0 text-xs font-black shadow-inner">${day}</div>
                        <p class="text-[11px] font-bold text-gray-800">${monthHolidays[day]}</p>
                    </div>`;
                });
            }
        }
        function changeMonth(offset) { viewDate.setMonth(viewDate.getMonth() + offset); updateCalendar(); }
        updateCalendar();

        // Close Announcement with Three.js & GSAP Animation
        function closeAnnouncement() {
            const bannerContainer = document.getElementById('announcement-banner-container');

            if (bannerContainer) {
                // Get button center for explosion
                // Since this is called via onclick, 'event' is available
                const button = event.currentTarget;
                const rect = button.getBoundingClientRect();
                const centerX = rect.left + rect.width / 2;
                const centerY = rect.top + rect.height / 2;

                // GSAP Animation to remove banner
                gsap.to(bannerContainer, {
                    height: 0,
                    opacity: 0,
                    marginBottom: 0,
                    paddingTop: 0,
                    paddingBottom: 0,
                    duration: 0.5,
                    ease: "power2.inOut",
                    onComplete: () => {
                        bannerContainer.remove(); // Remove completely
                    }
                });

                // Background controlled by settings now
            }
        }

        function startInfiniteBackground() {
            if (window.backgroundStarted) return;
            window.backgroundStarted = true;

            const canvas = document.getElementById('animationCanvas');
            canvas.style.opacity = 1;

            const scene = new THREE.Scene();
            // Fog for depth fading
            scene.fog = new THREE.FogExp2(0xf1f5f9, 0.015);

            const camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.1, 1000);
            camera.position.z = 40;

            const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(window.devicePixelRatio);

            // Lighting is crucial for 3D shapes
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
            scene.add(ambientLight);

            const dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
            dirLight.position.set(10, 20, 10);
            scene.add(dirLight);

            const pointLight = new THREE.PointLight(0xff99cc, 0.5);
            pointLight.position.set(-10, -10, 10);
            scene.add(pointLight);

            // Floating Pastry Animation
            const floatingItems = [];

            // La Rose Noire Macaron Colors
            // Texture Loader
            const textureLoader = new THREE.TextureLoader();

            const tartTexture = textureLoader.load('assets/choco-tart-removebg-preview.png');
            tartTexture.colorSpace = THREE.SRGBColorSpace;

            const croissantTexture = textureLoader.load('assets/croissant.png');
            croissantTexture.colorSpace = THREE.SRGBColorSpace;

            const vanillaTartTexture = textureLoader.load('assets/tart.png');
            vanillaTartTexture.colorSpace = THREE.SRGBColorSpace;

            const cakeTexture = textureLoader.load('assets/macarons.png');
            cakeTexture.colorSpace = THREE.SRGBColorSpace;

            const marbleTexture = textureLoader.load('assets/marble.png');
            marbleTexture.colorSpace = THREE.SRGBColorSpace;

            const raspberryTexture = textureLoader.load('assets/raspberry.png');
            raspberryTexture.colorSpace = THREE.SRGBColorSpace;

            // Generic Product Creator
            const createProductItem = (texture, width, height) => {
                const geometry = new THREE.PlaneGeometry(width, height);
                const material = new THREE.MeshStandardMaterial({
                    map: texture,
                    transparent: true,
                    alphaTest: 0.1,
                    side: THREE.DoubleSide,
                    roughness: 0.4,
                    metalness: 0.1
                });
                return new THREE.Mesh(geometry, material);
            };

            // Product Configurations
            const products = [
                { texture: tartTexture, width: 3, height: 3 },
                { texture: croissantTexture, width: 3.5, height: 2.5 },
                { texture: vanillaTartTexture, width: 3, height: 3 },
                { texture: cakeTexture, width: 3, height: 3 },
                { texture: marbleTexture, width: 3, height: 3 },
                { texture: raspberryTexture, width: 3, height: 3 }
            ];

            // Spawn Products
            const count = 25;
            for (let i = 0; i < count; i++) {
                // Pick random product config
                const config = products[Math.floor(Math.random() * products.length)];
                const item = createProductItem(config.texture, config.width, config.height);

                // Random starting positions spread widely
                item.position.x = (Math.random() - 0.5) * 60;
                item.position.y = (Math.random() - 0.5) * 40;
                item.position.z = (Math.random() - 0.5) * 30; // Still keep depth for parallax, but they look 2D

                // 2D Rotation only (Z-axis)
                item.rotation.z = Math.random() * Math.PI * 2;
                // Ensure they face forward
                item.rotation.x = 0;
                item.rotation.y = 0;

                // Custom properties for animation
                item.userData = {
                    rotSpeedZ: (Math.random() - 0.5) * 0.01, // Slow spin
                    floatSpeed: 0.02 + Math.random() * 0.03,
                    yBase: item.position.y,
                    xBase: item.position.x,
                    phase: Math.random() * Math.PI * 2
                };

                scene.add(item);
                floatingItems.push(item);

                // Scale in effect
                item.scale.set(0, 0, 0);
                gsap.to(item.scale, { x: 1, y: 1, z: 1, duration: 1, delay: Math.random() * 0.5, ease: "back.out(1.7)" });
            }

            // Interactive Light
            let mouseX = 0;
            let mouseY = 0;
            document.addEventListener('mousemove', (e) => {
                mouseX = (e.clientX / window.innerWidth) * 2 - 1;
                mouseY = -(e.clientY / window.innerHeight) * 2 + 1;

                // Move light with mouse
                pointLight.position.x = mouseX * 20;
                pointLight.position.y = mouseY * 20;
            });

            const animate = () => {
                requestAnimationFrame(animate);

                const time = Date.now() * 0.001;

                floatingItems.forEach(item => {
                    // Constant 2D rotation (Spinning wheel)
                    item.rotation.z += item.userData.rotSpeedZ;

                    // Floating motion
                    item.position.y = item.userData.yBase + Math.sin(time + item.userData.phase) * 2;
                    item.position.x = item.userData.xBase + Math.cos(time * 0.5 + item.userData.phase) * 1;
                });

                // Subtle camera sway
                camera.position.x += (mouseX * 5 - camera.position.x) * 0.05;
                camera.position.y += (mouseY * 5 - camera.position.y) * 0.05;
                camera.lookAt(scene.position);

                renderer.render(scene, camera);
            };

            animate();

            // Handle Resize
            window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            });
        }
    </script>
</body>

</html>