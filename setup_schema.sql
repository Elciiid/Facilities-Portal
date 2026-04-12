-- 1. Session Storage
CREATE TABLE IF NOT EXISTS fcl_app_sessions (
    id VARCHAR(255) PRIMARY KEY,
    data TEXT NOT NULL,
    timestamp INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_sessions_timestamp ON fcl_app_sessions(timestamp);

-- 2. User Authentication
CREATE TABLE IF NOT EXISTS fcl_app_users (
    user_id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- bcrypt hash
    full_name VARCHAR(255),
    employee_id VARCHAR(50),
    role VARCHAR(50) DEFAULT 'admin',
    department VARCHAR(255),
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 3. Top Banner Announcements
CREATE TABLE IF NOT EXISTS fcl_announcements (
    id SERIAL PRIMARY KEY,
    active BOOLEAN DEFAULT true,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    type VARCHAR(50) DEFAULT 'info', -- info, success, warning, error
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- 4. Left Panel Carousel Announcements
CREATE TABLE IF NOT EXISTS fcl_carousel_announcements (
    id VARCHAR(100) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    image_url TEXT,
    enabled BOOLEAN DEFAULT true,
    "order" INTEGER DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 5. Portal Settings (Toggles)
CREATE TABLE IF NOT EXISTS fcl_portal_settings (
    key VARCHAR(100) PRIMARY KEY,
    value JSONB NOT NULL,
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- 6. Apps and Folders
CREATE TABLE IF NOT EXISTS fcl_folders (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    enabled BOOLEAN DEFAULT true,
    "order" INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS fcl_apps (
    id VARCHAR(100) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    color VARCHAR(50),
    link TEXT,
    folder_name VARCHAR(100) REFERENCES fcl_folders(name) ON DELETE SET NULL ON UPDATE CASCADE,
    enabled BOOLEAN DEFAULT true,
    "order" INTEGER DEFAULT 0
);

-- MOCK DATA INSERTS

-- Admin User (Password: password)
-- Hash generated using: password_hash('password', PASSWORD_BCRYPT)
INSERT INTO fcl_app_users (username, password, full_name, employee_id, department, role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Admin', 'ADMIN-001', 'Information Technology Department', 'admin')
ON CONFLICT (username) DO NOTHING;

-- Initial Announcement
INSERT INTO fcl_announcements (id, active, title, message)
VALUES (1, true, 'Facilities Portal', 'Welcome to the centralized workspace for compliance and reporting.')
ON CONFLICT (id) DO UPDATE SET title = EXCLUDED.title, message = EXCLUDED.message;

-- Settings
INSERT INTO fcl_portal_settings (key, value)
VALUES 
    ('weather', '{"enabled": true}'),
    ('background', '{"enabled": true}')
ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value;

-- Apps (from existing apps.json)
INSERT INTO fcl_apps (id, title, description, icon, color, link, "order")
VALUES 
    ('return_to_work', 'Return to Work', 'Medical clearances and health status reporting.', 'fa-heart-pulse', 'pink', '../return-to-work-final/auth/login.php', 1),
    ('disposal_form', 'Disposal Form', 'Asset and equipment decommissioning requests.', 'fa-dumpster', 'rose', '../Disposal-Form/login.php', 2),
    ('gmp_checklist', 'GMP Checklist', 'Quality assurance and hygiene compliance audit.', 'fa-list-check', 'indigo', '../gmp_checklist/login.php', 3),
    ('uniform_inspection', 'Uniform Inspection', 'Personal hygiene and uniform quality tracking.', 'fa-shirt', 'blue', '../uniform/login.php', 4),
    ('item_request', 'Item Request', 'Requisition for supplies and facility materials.', 'fa-dolly', 'amber', '../item-request/auth/login.php', 5),
    ('gmp_scanner', 'GMP Scanner', 'Scan location QR codes to access individual GMP checklist on your mobile device', 'fa-eye', 'pink', '../gmp_checklist/pages/mbl_scan.php', 6)
ON CONFLICT (id) DO UPDATE SET 
    title = EXCLUDED.title, 
    description = EXCLUDED.description, 
    icon = EXCLUDED.icon,
    color = EXCLUDED.color,
    link = EXCLUDED.link,
    "order" = EXCLUDED."order";
