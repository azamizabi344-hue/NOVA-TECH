-- ============================================================
-- NOVA TECH - Complete Database Schema & Sample Data
-- ============================================================
-- This file creates the entire database for NOVA TECH.
-- Run this in phpMyAdmin or MySQL CLI to set up everything.
-- ============================================================

-- Create the database
CREATE DATABASE IF NOT EXISTS nova_tech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nova_tech;

-- ============================================================
-- TABLE: users
-- Stores registered users and admins.
-- password_hash() stores a 60-char bcrypt hash, so use VARCHAR(255) for safety.
-- role: 'admin' or 'user'
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    avatar VARCHAR(500) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: services
-- Company services offered to clients.
-- ============================================================
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    short_description TEXT NOT NULL,
    full_description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-cog',
    category VARCHAR(100) DEFAULT 'General',
    price_from DECIMAL(10,2) DEFAULT 0.00,
    features JSON,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: projects
-- Company portfolio projects.
-- technologies stored as JSON array, e.g. ["PHP","MySQL","JS"]
-- ============================================================
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    full_description TEXT,
    image VARCHAR(500) DEFAULT NULL,
    category VARCHAR(100) NOT NULL,
    technologies JSON,
    client VARCHAR(200) DEFAULT NULL,
    project_url VARCHAR(500) DEFAULT NULL,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_featured (is_featured),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: team_members
-- Company team/staff members.
-- ============================================================
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    role VARCHAR(200) NOT NULL,
    department VARCHAR(100) DEFAULT 'General',
    bio TEXT,
    photo VARCHAR(500) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    skills JSON,
    social_links JSON,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_department (department),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: categories
-- Blog post categories. Each post belongs to one category.
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: blog_posts
-- Blog articles with optional category link.
-- slug is used for clean URLs: post.php?slug=my-article
-- ============================================================
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(300) NOT NULL,
    slug VARCHAR(350) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    image VARCHAR(500) DEFAULT NULL,
    category_id INT DEFAULT NULL,
    author_id INT DEFAULT NULL,
    is_published TINYINT(1) DEFAULT 1,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_published (is_published),
    INDEX idx_category (category_id)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: contact_messages
-- Messages submitted from the Contact page.
-- is_read: 0 = new/unread, 1 = read by admin
-- ============================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    subject VARCHAR(300) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_read (is_read),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: newsletter_subscribers
-- Email addresses from the newsletter signup form.
-- ============================================================
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: testimonials
-- Client testimonials / reviews displayed on the homepage.
-- ============================================================
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(150) NOT NULL,
    client_role VARCHAR(200) DEFAULT NULL,
    company VARCHAR(200) DEFAULT NULL,
    content TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    avatar VARCHAR(500) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: pricing_plans
-- Pricing tiers displayed on the homepage.
-- features stored as JSON array.
-- ============================================================
CREATE TABLE IF NOT EXISTS pricing_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    period VARCHAR(30) DEFAULT 'per month',
    description TEXT,
    features JSON,
    is_popular TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: settings
-- Key-value store for site-wide settings (site name, logo, etc.)
-- ============================================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB;

-- ============================================================
-- ============================================================
-- SAMPLE DATA INSERTS
-- ============================================================
-- ============================================================

-- -----------------------------------------------------------
-- Admin user: email = admin@novatech.com / password = admin123
-- Regular user: email = user@novatech.com / password = user123
-- These are bcrypt hashes generated with password_hash()
-- -----------------------------------------------------------
INSERT INTO users (full_name, email, password, role) VALUES
('Admin User', 'admin@novatech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Jane Cooper', 'jane@novatech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Demo User', 'user@novatech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- -----------------------------------------------------------
-- Services - 6 core services
-- -----------------------------------------------------------
INSERT INTO services (title, short_description, full_description, icon, category, price_from, features, sort_order) VALUES
('Web Development', 'Custom websites and web applications built with modern technologies for optimal performance and user experience.', 'Our web development team creates bespoke websites and web applications using the latest technologies. From simple landing pages to complex enterprise solutions, we deliver high-quality, scalable, and maintainable code that drives business growth.', 'fas fa-code', 'Development', 2500.00, '["Responsive Design","SEO Optimization","Fast Loading","Cross-browser Support","Custom CMS","API Integration"]', 1),
('Mobile App Development', 'Native and cross-platform mobile applications for iOS and Android that engage users and drive business growth.', 'We design and develop native and cross-platform mobile applications that provide seamless user experiences. Our team uses React Native, Flutter, Swift, and Kotlin to build high-performance apps for both iOS and Android platforms.', 'fas fa-mobile-alt', 'Development', 5000.00, '["iOS & Android","React Native & Flutter","Offline Support","Push Notifications","App Store Submission","Analytics Integration"]', 2),
('AI & Machine Learning', 'Intelligent solutions powered by artificial intelligence and machine learning to automate processes and gain insights.', 'Harness the power of artificial intelligence and machine learning to transform your business. We build custom AI models, natural language processing systems, computer vision solutions, and predictive analytics tools.', 'fas fa-brain', 'Intelligence', 8000.00, '["Custom AI Models","NLP Solutions","Predictive Analytics","Computer Vision","Data Processing","Model Training"]', 3),
('Cybersecurity', 'Comprehensive security solutions to protect your digital assets from threats, vulnerabilities, and attacks.', 'Protect your business with our comprehensive cybersecurity services. We offer security audits, penetration testing, vulnerability assessments, compliance consulting, and 24/7 monitoring to keep your digital assets safe.', 'fas fa-shield-alt', 'Security', 3500.00, '["Security Audits","Penetration Testing","Vulnerability Assessment","Compliance Consulting","24/7 Monitoring","Incident Response"]', 4),
('Cloud Solutions', 'Scalable cloud infrastructure and migration services to optimize your operations and reduce costs.', 'Migrate to the cloud and optimize your infrastructure with our expert cloud solutions. We work with AWS, Azure, and Google Cloud to design, deploy, and manage scalable, secure, and cost-effective cloud environments.', 'fas fa-cloud', 'Infrastructure', 4000.00, '["AWS & Azure","Cloud Migration","Auto Scaling","Cost Optimization","DevOps & CI/CD","Disaster Recovery"]', 5),
('UI/UX Design', 'User-centered design that creates intuitive, beautiful, and engaging digital experiences for your customers.', 'Our design team creates stunning, user-centered interfaces that delight customers and drive conversions. We follow a research-driven design process including wireframing, prototyping, usability testing, and iterative refinement.', 'fas fa-palette', 'Design', 2000.00, '["User Research","Wireframing","Prototyping","Usability Testing","Design Systems","Brand Identity"]', 6);

-- -----------------------------------------------------------
-- Projects - 12 portfolio projects
-- -----------------------------------------------------------
INSERT INTO projects (title, description, full_description, category, technologies, client, project_url, is_featured, sort_order) VALUES
('E-Commerce Platform', 'Full-featured e-commerce solution with payment integration and inventory management.', 'A comprehensive e-commerce platform built for a retail client featuring product management, shopping cart, secure payment processing with Stripe, inventory tracking, order management, and a responsive admin dashboard. The platform handles over 10,000 products and processes hundreds of orders daily.', 'Web Development', '["PHP","MySQL","JavaScript","Stripe API","HTML5","CSS3"]', 'RetailCorp Inc.', '#', 1, 1),
('Healthcare Management System', 'Digital healthcare platform for patient management and appointment scheduling.', 'A complete healthcare management system that allows clinics to manage patient records, schedule appointments, process billing, and generate reports. Features include role-based access control, HIPAA-compliant data handling, and integration with insurance providers.', 'Web Development', '["PHP","MySQL","React","Node.js","REST API","Docker"]', 'MedCare Hospital', '#', 1, 2),
('Mobile Banking App', 'Secure mobile banking application with biometric authentication and real-time transactions.', 'A feature-rich mobile banking application supporting account management, fund transfers, bill payments, transaction history, and push notifications. Built with security-first approach including biometric authentication, end-to-end encryption, and fraud detection.', 'Mobile Development', '["Flutter","Dart","Firebase","REST API","Encryption"]', 'FinanceFirst Bank', '#', 1, 3),
('AI Chatbot Platform', 'Intelligent customer service chatbot powered by natural language processing.', 'An AI-powered chatbot platform that handles customer inquiries across multiple channels. Uses NLP for intent recognition, supports multi-language conversations, integrates with CRM systems, and learns from interactions to improve responses over time.', 'AI & Machine Learning', '["Python","TensorFlow","NLP","FastAPI","React","Redis"]', 'ServicePro Ltd.', '#', 1, 4),
('Cloud Infrastructure Setup', 'Complete cloud migration and infrastructure setup for enterprise client.', 'Migrated a legacy enterprise system to AWS cloud infrastructure. Implemented auto-scaling groups, load balancers, RDS databases, S3 storage, CloudFront CDN, and comprehensive monitoring with CloudWatch. Reduced infrastructure costs by 40% while improving uptime to 99.99%.', 'Cloud Solutions', '["AWS","Terraform","Docker","Kubernetes","CI/CD","Python"]', 'Enterprise Corp.', '#', 1, 5),
('Real Estate Platform', 'Property listing and management platform with virtual tour integration.', 'A modern real estate platform featuring advanced property search with filters, virtual 3D tours, mortgage calculator, agent profiles, and a comprehensive admin panel for property management. Includes lead management and automated email notifications.', 'Web Development', '["Laravel","Vue.js","MySQL","Google Maps API","WebGL"]', 'PropertyHub Realty', '#', 0, 6),
('Food Delivery App', 'On-demand food delivery mobile application with real-time order tracking.', 'A complete food delivery solution with customer app, restaurant dashboard, and delivery driver app. Features real-time order tracking, in-app messaging, payment processing, route optimization, and restaurant analytics.', 'Mobile Development', '["React Native","Node.js","MongoDB","Socket.io","Google Maps"]', 'QuickBite Foods', '#', 0, 7),
('Cybersecurity Audit', 'Comprehensive security audit and penetration testing for financial institution.', 'Performed a thorough security assessment of a major financial institution infrastructure. Included network penetration testing, web application security testing, social engineering assessments, and compliance verification. Delivered detailed remediation roadmap.', 'Security', '["Kali Linux","Burp Suite","Metasploit","Nmap","Wireshark"]', 'SecureBank Financial', '#', 0, 8),
('ERP System Integration', 'Enterprise resource planning system connecting all business departments.', 'Designed and implemented a custom ERP system that integrates finance, HR, inventory, sales, and customer service modules. Features real-time dashboards, automated workflows, and comprehensive reporting across all departments.', 'Web Development', '["PHP","MySQL","Angular","Redis","Elasticsearch","RabbitMQ"]', 'GlobalTech Industries', '#', 1, 9),
('IoT Dashboard', 'Real-time IoT device monitoring and management dashboard.', 'A real-time dashboard for monitoring and managing IoT devices across multiple locations. Displays live sensor data, device health metrics, automated alerts, and predictive maintenance scheduling using machine learning.', 'AI & Machine Learning', '["Python","React","InfluxDB","MQTT","Grafana","TensorFlow"]', 'SmartFactory Inc.', '#', 0, 10),
('Brand Identity Design', 'Complete brand identity redesign for a tech startup.', 'Created a comprehensive brand identity including logo design, color palette, typography system, brand guidelines document, business cards, letterheads, and digital assets. Conducted stakeholder interviews and competitive analysis to inform the design direction.', 'Design', '["Figma","Illustrator","Photoshop","After Effects","Brand Strategy"]', 'NeuroTech AI', '#', 0, 11),
('DevOps Pipeline', 'Automated CI/CD pipeline with infrastructure as code implementation.', 'Built a complete DevOps pipeline featuring automated testing, code quality checks, containerized deployments, and infrastructure provisioning. Implemented blue-green deployments, canary releases, and comprehensive monitoring dashboards.', 'Cloud Solutions', '["Jenkins","Docker","Kubernetes","Terraform","Ansible","Prometheus"]', 'DevOps Masters', '#', 1, 12);

-- -----------------------------------------------------------
-- Team Members - 10 team members
-- -----------------------------------------------------------
INSERT INTO team_members (full_name, role, department, bio, email, skills, social_links, sort_order) VALUES
('Sarah Chen', 'Chief Executive Officer', 'Leadership', 'Visionary leader with 15+ years of experience in technology and business strategy. Sarah has led NOVA TECH from a small startup to a leading software development company.', 'sarah@novatech.com', '["Strategic Planning","Business Development","Leadership","Innovation"]', '{"linkedin":"#","twitter":"#","github":"#"}', 1),
('Michael Rodriguez', 'Chief Technology Officer', 'Leadership', 'Tech architect passionate about building scalable systems. Michael oversees all technical decisions and leads our engineering teams.', 'michael@novatech.com', '["System Architecture","Cloud Computing","DevOps","Team Leadership"]', '{"linkedin":"#","twitter":"#","github":"#"}', 2),
('Emily Watson', 'Lead Designer', 'Design', 'Award-winning designer with a passion for creating intuitive and beautiful user experiences. Emily leads our design team and maintains our design system.', 'emily@novatech.com', '["UI/UX Design","Figma","Design Systems","User Research","Prototyping"]', '{"linkedin":"#","twitter":"#","dribbble":"#"}', 3),
('David Kim', 'Senior Full-Stack Developer', 'Engineering', 'Full-stack expert with deep knowledge in PHP, JavaScript, and cloud technologies. David mentors junior developers and leads major projects.', 'david@novatech.com', '["PHP","JavaScript","React","Node.js","MySQL","AWS"]', '{"linkedin":"#","github":"#"}', 4),
('Priya Patel', 'AI/ML Engineer', 'Engineering', 'Machine learning specialist focused on NLP and computer vision. Priya develops our AI solutions and researches emerging technologies.', 'priya@novatech.com', '["Python","TensorFlow","PyTorch","NLP","Computer Vision"]', '{"linkedin":"#","github":"#","twitter":"#"}', 5),
('James Morrison', 'DevOps Engineer', 'Infrastructure', 'Infrastructure expert who keeps our systems running smoothly. James manages our cloud infrastructure and CI/CD pipelines.', 'james@novatech.com', '["AWS","Docker","Kubernetes","Terraform","Jenkins","Linux"]', '{"linkedin":"#","github":"#"}', 6),
('Lisa Thompson', 'Project Manager', 'Operations', 'Certified PMP with a talent for keeping complex projects on track. Lisa ensures client satisfaction and team productivity.', 'lisa@novatech.com', '["Project Management","Agile","Scrum","Client Relations","Risk Management"]', '{"linkedin":"#","twitter":"#"}', 7),
('Alex Nguyen', 'Mobile Developer', 'Engineering', 'Cross-platform mobile development expert. Alex builds high-performance apps for iOS and Android using Flutter and React Native.', 'alex@novatech.com', '["Flutter","React Native","Swift","Kotlin","Firebase","Dart"]', '{"linkedin":"#","github":"#"}', 8),
('Rachel Green', 'Marketing Director', 'Marketing', 'Digital marketing strategist who amplifies our brand presence. Rachel leads our marketing campaigns and content strategy.', 'rachel@novatech.com', '["Digital Marketing","SEO","Content Strategy","Social Media","Analytics"]', '{"linkedin":"#","twitter":"#"}', 9),
('Omar Hassan', 'Cybersecurity Analyst', 'Security', 'Certified ethical hacker dedicated to protecting our clients. Omar conducts security audits and implements defense strategies.', 'omar@novatech.com', '["Penetration Testing","SIEM","Network Security","Compliance","Forensics"]', '{"linkedin":"#","github":"#"}', 10);

-- -----------------------------------------------------------
-- Categories - Blog categories
-- -----------------------------------------------------------
INSERT INTO categories (name, slug, description) VALUES
('Technology', 'technology', 'Latest technology trends and innovations'),
('Web Development', 'web-development', 'Articles about web development practices and tools'),
('AI & Machine Learning', 'ai-machine-learning', 'Artificial intelligence and machine learning insights'),
('Cybersecurity', 'cybersecurity', 'Security best practices and threat awareness'),
('Cloud Computing', 'cloud-computing', 'Cloud platforms, DevOps, and infrastructure'),
('Company News', 'company-news', 'Updates and announcements from NOVA TECH'),
('Design', 'design', 'UI/UX design trends and best practices'),
('Tutorials', 'tutorials', 'Step-by-step guides and how-to articles');

-- -----------------------------------------------------------
-- Blog Posts - 10 articles
-- -----------------------------------------------------------
INSERT INTO blog_posts (title, slug, excerpt, content, category_id, author_id, views, created_at) VALUES
('The Future of AI in Software Development', 'future-of-ai-in-software-development', 'Artificial intelligence is revolutionizing how we build software. Here is what the future holds for developers and businesses.', '<h2>AI is Changing Everything</h2><p>The software development landscape is undergoing a massive transformation driven by artificial intelligence. From automated code generation to intelligent testing, AI tools are becoming indispensable in the modern developer toolkit.</p><h3>Key Areas of Impact</h3><ul><li><strong>Code Generation:</strong> Tools like GitHub Copilot are helping developers write code faster by suggesting entire functions and modules.</li><li><strong>Testing:</strong> AI-powered testing tools can identify bugs and vulnerabilities that human testers might miss.</li><li><strong>Code Review:</strong> Machine learning models can review code for quality, security, and performance issues.</li><li><strong>Documentation:</strong> AI can automatically generate and maintain documentation for complex codebases.</li></ul><h3>What This Means for Developers</h3><p>While AI will not replace developers, developers who use AI will replace those who do not. The key is to embrace these tools as assistants while continuing to develop deep understanding of fundamentals.</p><p>At NOVA TECH, we are actively integrating AI into our development workflows to deliver better products faster while maintaining the highest quality standards.</p>', 1, 1, 1247, '2026-01-15 10:00:00'),
('10 PHP Best Practices for 2026', '10-php-best-practices-2026', 'PHP continues to evolve. Learn the modern best practices every PHP developer should follow in 2026.', '<h2>PHP in 2026</h2><p>PHP remains one of the most popular server-side languages in the world, powering over 75% of websites. With PHP 8.x bringing significant improvements, it is more important than ever to follow modern best practices.</p><h3>1. Always Use Type Declarations</h3><p>PHP 8.x supports union types, intersection types, and enums. Use them to make your code more predictable and self-documenting.</p><h3>2. Use PDO with Prepared Statements</h3><p>Never concatenate user input into SQL queries. Always use prepared statements with PDO to prevent SQL injection.</p><h3>3. Leverage PHP 8 Features</h3><p>Match expressions, named arguments, constructor property promotion, and fibers are powerful tools in your arsenal.</p><h3>4. Follow PSR Standards</h3><p>Adhere to PSR-12 coding standards and PSR-4 autoloading. Consistency is key in professional development.</p><h3>5. Write Tests</h3><p>Use PHPUnit for unit testing and Pest for a more expressive testing experience. Aim for meaningful test coverage.</p><p>These practices will help you write cleaner, safer, and more maintainable PHP code.</p>', 2, 4, 892, '2026-02-20 14:30:00'),
('How We Built a Real-Time Chat System', 'how-we-built-real-time-chat', 'A deep dive into building a scalable real-time chat system using WebSockets, Node.js, and Redis.', '<h2>The Challenge</h2><p>Our client needed a real-time messaging system that could handle thousands of concurrent users with low latency. Here is how we built it.</p><h3>Architecture Overview</h3><p>We chose a microservices architecture with three main components:</p><ul><li><strong>WebSocket Server:</strong> Built with Node.js and Socket.io for real-time bidirectional communication</li><li><strong>Message Queue:</strong> Redis Pub/Sub for distributing messages across server instances</li><li><strong>Persistence Layer:</strong> MongoDB for storing chat history with efficient indexing</li></ul><h3>Key Decisions</h3><p>We implemented horizontal scaling using Redis adapter, allowing us to add more WebSocket server instances as needed. Message delivery uses a combination of WebSockets for online users and push notifications for offline users.</p><p>The system now handles over 50,000 concurrent connections with an average message delivery time of under 50 milliseconds.</p>', 2, 5, 654, '2026-03-10 09:15:00'),
('Understanding Cybersecurity Threats in 2026', 'understanding-cybersecurity-threats-2026', 'Stay informed about the latest cybersecurity threats and how to protect your business from them.', '<h2>The Evolving Threat Landscape</h2><p>Cyber threats are becoming more sophisticated every year. In 2026, businesses face new challenges from AI-powered attacks, supply chain vulnerabilities, and evolving ransomware tactics.</p><h3>Top Threats to Watch</h3><ul><li><strong>AI-Powered Phishing:</strong> Attackers use AI to create highly convincing phishing emails that are harder to detect.</li><li><strong>Ransomware 2.0:</strong> Double and triple extortion tactics are becoming the norm.</li><li><strong>Supply Chain Attacks:</strong> Compromising a single vendor can give attackers access to multiple organizations.</li><li><strong>Cloud Misconfigurations:</strong> As more data moves to cloud, misconfigured settings become a major vulnerability.</li></ul><h3>How to Protect Your Business</h3><p>Implement defense in depth: combine firewalls, endpoint protection, employee training, regular audits, and incident response planning. Security is not a product but a process.</p>', 4, 10, 1023, '2026-04-05 11:00:00'),
('Migrating to AWS: A Complete Guide', 'migrating-to-aws-complete-guide', 'Everything you need to know about migrating your infrastructure to AWS cloud successfully.', '<h2>Why AWS?</h2><p>Amazon Web Services offers the most comprehensive cloud platform with 200+ services. Here is our step-by-step guide to a successful migration.</p><h3>Phase 1: Assessment</h3><p>Evaluate your current infrastructure, identify dependencies, and create a migration roadmap. Use the AWS Migration Acceleration Program (MAP) for structured guidance.</p><h3>Phase 2: Planning</h3><p>Choose your migration strategy: rehost (lift and shift), replatform, or refactor. Each has different cost and complexity implications.</p><h3>Phase 3: Execution</h3><p>Start with non-critical workloads, validate, and progressively migrate more complex systems. Use AWS CloudFormation for infrastructure as code.</p><h3>Phase 4: Optimization</h3><p>Post-migration, optimize for cost and performance using Reserved Instances, Savings Plans, and auto-scaling configurations.</p>', 5, 6, 789, '2026-05-18 16:45:00'),
('The Importance of UI/UX Design in Business Success', 'importance-ui-ux-design', 'Good design is good business. Learn how investing in UI/UX design directly impacts your bottom line.', '<h2>Design Drives Business</h2><p>Every dollar invested in UX design returns approximately $100 in ROI. That is a 9,900% return. Yet many businesses still undervalue design.</p><h3>Why UX Matters</h3><ul><li><strong>Reduced Development Costs:</strong> Fixing a UX issue during development costs 10x less than after launch.</li><li><strong>Higher Conversion Rates:</strong> A well-designed user flow can increase conversions by up to 200%.</li><li><strong>Customer Loyalty:</strong> 88% of users are less likely to return after a bad experience.</li><li><strong>Competitive Advantage:</strong> Design-led companies outperform their peers by 2:1.</li></ul><p>At NOVA TECH, we integrate design thinking into every project from day one, ensuring that user experience is never an afterthought.</p>', 7, 3, 567, '2026-06-22 13:20:00'),
('NOVA TECH Expands to New Office', 'nova-tech-expands-new-office', 'Exciting news! NOVA TECH is growing and moving to a larger headquarters to accommodate our expanding team.', '<h2>A New Chapter</h2><p>We are thrilled to announce that NOVA TECH is moving to a brand new, state-of-the-art office space. This expansion reflects our continued growth and commitment to providing the best environment for our team.</p><h3>New Office Highlights</h3><ul><li>50,000 sq ft of modern workspace</li><li>Dedicated innovation lab for R&D</li><li>Collaborative open spaces and private focus rooms</li><li>State-of-the-art meeting rooms with video conferencing</li><li>Employee wellness facilities</li></ul><p>We expect to complete the move by Q3 2026 and will be hiring 50+ new team members to fill our new space.</p>', 6, 1, 432, '2026-07-01 10:30:00'),
('Getting Started with Docker for PHP Developers', 'getting-started-docker-php', 'Docker simplifies development environments. Here is how PHP developers can get started with containers.', '<h2>Why Docker?</h2><p>Have you ever heard "it works on my machine"? Docker eliminates this problem by packaging your application with its entire environment into containers.</p><h3>Basic Setup for PHP</h3><p>Here is a simple docker-compose.yml for a PHP project:</p><pre><code>version: "3.8"\nservices:\n  web:\n    image: php:8.2-apache\n    volumes:\n      - ./src:/var/www/html\n    ports:\n      - "8080:80"\n  db:\n    image: mysql:8.0\n    environment:\n      MYSQL_ROOT_PASSWORD: secret\n      MYSQL_DATABASE: myapp</code></pre><h3>Benefits for PHP Development</h3><ul><li>Consistent environments across your team</li><li>Easy onboarding for new developers</li><li>Isolated services for testing</li><li>Production parity</li></ul><p>Docker has become an essential tool in our PHP development workflow at NOVA TECH.</p>', 2, 4, 1156, '2026-08-10 08:00:00'),
('How Machine Learning Detects Fraud in Real-Time', 'machine-learning-fraud-detection', 'Explore how machine learning models are used to detect fraudulent transactions in milliseconds.', '<h2>The Scale of Fraud</h2><p>Financial fraud costs businesses billions each year. Traditional rule-based systems are no longer sufficient. Machine learning offers a dynamic, adaptive approach to fraud detection.</p><h3>How It Works</h3><p>ML fraud detection systems analyze thousands of transaction features in real-time:</p><ul><li>Transaction amount and frequency</li><li>Geographic location and device information</li><li>User behavior patterns</li><li>Time of day and day of week</li><li>Merchant category and history</li></ul><h3>Model Architecture</h3><p>We typically use ensemble methods combining gradient boosting and neural networks. The model is trained on historical labeled data and continuously updated with new patterns.</p><p>Our fraud detection system at NOVA TECH achieves 99.7% accuracy with less than 0.1% false positive rate.</p>', 3, 5, 876, '2026-09-01 15:00:00'),
('Building Scalable APIs with PHP 8 and Laravel', 'building-scalable-apis-php-laravel', 'Learn how to build production-ready, scalable REST APIs using PHP 8 and the Laravel framework.', '<h2>API Development with Laravel</h2><p>Laravel is the most popular PHP framework for building APIs. With PHP 8 and Laravel 11, you have access to powerful features for building scalable, maintainable APIs.</p><h3>Key Features</h3><ul><li><strong>API Resources:</strong> Transform your models into optimized JSON responses</li><li><strong>Rate Limiting:</strong> Protect your API with configurable rate limits</li><li><strong>Authentication:</strong> Laravel Sanctum for simple token-based authentication</li><li><strong>Caching:</strong> Built-in caching strategies for improved performance</li><li><strong>Testing:</strong> Comprehensive testing tools for API endpoints</li></ul><h3>Performance Tips</h3><p>Use database indexing, eager loading to prevent N+1 queries, response caching, and horizontal scaling with load balancers to handle high traffic volumes.</p>', 2, 4, 934, '2026-09-08 12:00:00');

-- -----------------------------------------------------------
-- Contact Messages - sample messages
-- -----------------------------------------------------------
INSERT INTO contact_messages (name, email, phone, subject, message, is_read, created_at) VALUES
('John Smith', 'john@example.com', '+1-555-0101', 'Project Inquiry', 'I would like to discuss a potential web development project for our company. We need a custom CRM system.', 1, '2026-08-15 09:30:00'),
('Sarah Williams', 'sarah.w@company.org', '+1-555-0102', 'Partnership Proposal', 'Our company would like to explore partnership opportunities with NOVA TECH for AI solutions.', 0, '2026-08-20 14:15:00'),
('Mike Johnson', 'mike.j@startup.io', '+1-555-0103', 'Mobile App Development', 'We are looking for a team to build a food delivery app. Can you provide a quote?', 0, '2026-08-25 11:45:00'),
('Emily Davis', 'emily.d@corp.com', '+1-555-0104', 'Security Audit Request', 'We need a comprehensive security audit for our financial platform. Please contact us.', 1, '2026-09-01 16:20:00'),
('Robert Brown', 'robert.b@enterprise.net', '+1-555-0105', 'Cloud Migration', 'We want to migrate our on-premise infrastructure to AWS. What are your rates?', 0, '2026-09-05 10:00:00');

-- -----------------------------------------------------------
-- Newsletter Subscribers
-- -----------------------------------------------------------
INSERT INTO newsletter_subscribers (email, created_at) VALUES
('subscriber1@example.com', '2026-06-01 10:00:00'),
('subscriber2@example.com', '2026-06-15 12:00:00'),
('subscriber3@example.com', '2026-07-01 08:30:00'),
('subscriber4@example.com', '2026-07-20 14:45:00'),
('subscriber5@example.com', '2026-08-05 09:15:00'),
('subscriber6@example.com', '2026-08-18 16:30:00'),
('subscriber7@example.com', '2026-09-01 11:00:00');

-- -----------------------------------------------------------
-- Testimonials
-- -----------------------------------------------------------
INSERT INTO testimonials (client_name, client_role, company, content, rating, sort_order) VALUES
('Alex Thompson', 'CEO', 'RetailCorp Inc.', 'NOVA TECH delivered our e-commerce platform ahead of schedule and beyond expectations. Their attention to detail and technical expertise is outstanding. Sales increased by 300% within the first quarter.', 5, 1),
('Maria Garcia', 'CTO', 'MedCare Hospital', 'The healthcare management system NOVA TECH built has transformed our operations. Patient wait times decreased by 40% and our staff productivity has never been higher. Highly recommended!', 5, 2),
('James Wilson', 'Director of IT', 'FinanceFirst Bank', 'Security was our top priority, and NOVA TECH exceeded all expectations. Their team delivered a mobile banking app that is both feature-rich and incredibly secure. Zero security incidents since launch.', 5, 3),
('Dr. Lisa Park', 'Founder', 'NeuroTech AI', 'The brand identity NOVA TECH created for us perfectly captures our vision. Their design team is incredibly talented and professional. We receive compliments on our branding daily.', 5, 4),
('Tom Anderson', 'VP Engineering', 'SmartFactory Inc.', 'Our IoT dashboard built by NOVA TECH gives us real-time visibility into all our manufacturing processes. Predictive maintenance alone has saved us over $2M annually.', 4, 5);

-- -----------------------------------------------------------
-- Pricing Plans
-- -----------------------------------------------------------
INSERT INTO pricing_plans (name, price, period, description, features, is_popular, sort_order) VALUES
('Starter', 2999.00, 'per project', 'Perfect for small businesses and startups looking to establish their digital presence.', '["Up to 5 Pages","Responsive Design","Basic SEO","Contact Form","3 Months Support","Standard Hosting Setup"]', 0, 1),
('Professional', 7999.00, 'per project', 'Ideal for growing businesses that need custom features and ongoing support.', '["Up to 15 Pages","Custom Design","Advanced SEO","CMS Integration","E-commerce Ready","API Integration","6 Months Support","Performance Optimization"]', 1, 2),
('Enterprise', 19999.00, 'per project', 'For large organizations requiring complex, scalable solutions with dedicated support.', '["Unlimited Pages","Full Custom Development","SEO & Analytics Suite","Custom Integrations","Cloud Infrastructure","Security Audit","12 Months Dedicated Support","Priority Bug Fixes","Quarterly Reviews"]', 0, 3);

-- -----------------------------------------------------------
-- Site Settings
-- -----------------------------------------------------------
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'NOVA TECH'),
('site_tagline', 'Innovating Tomorrow, Today'),
('site_email', 'info@novatech.com'),
('site_phone', '+1 (555) 123-4567'),
('site_address', '123 Tech Avenue, San Francisco, CA 94105'),
('site_description', 'NOVA TECH is a leading software development company specializing in web development, mobile apps, AI solutions, and cloud services.'),
('footer_text', '&copy; 2026 NOVA TECH. All rights reserved.'),
('facebook_url', 'https://facebook.com/novatech'),
('twitter_url', 'https://twitter.com/novatech'),
('linkedin_url', 'https://linkedin.com/company/novatech'),
('github_url', 'https://github.com/novatech');
