<?php
require_once '../includes/auth.php';
requireRole('client');

$userName = getUserName();
$pdo = getDB();
$clientId = getUserId();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE client_id = ?");
$stmt->execute(array($clientId));
$totalProjects = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(o.id)
    FROM offers o
    JOIN projects p ON o.project_id = p.id
    WHERE p.client_id = ?
");
$stmt->execute(array($clientId));
$totalOffers = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE client_id = ? AND status = 'in_progress'");
$stmt->execute(array($clientId));
$activeProjects = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE client_id = ? AND status = 'closed'");
$stmt->execute(array($clientId));
$doneProjects = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Client Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/client.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header class="site-header">
    <div class="logo-area">
        <img src="../assets/images/logo.png" alt="BridgeX" class="logo-img">
        <span class="logo-text">Bridge<span style="color:var(--pink-main)">X</span></span>
    </div>
    <nav class="navbar">
        <a href="dashboard.php" class="active-link">Dashboard</a>
        <a href="post_project.php">Post Project</a>
        <a href="my_projects.php">My Projects</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="client-hero section">
        <div class="hero-badge">Welcome to BrigeX</div>
        <h1>Hello, <span class="pink-text"><?= htmlspecialchars($userName) ?></span></h1>
        <p>Manage your projects and track developer offers easily from here.</p>
        <div class="hero-buttons" style="margin-top:28px;">
            <a href="post_project.php" class="primary-btn">+ Post a New Project</a>
            <a href="my_projects.php" class="secondary-btn">View My Projects</a>
        </div>
    </section>

    <div class="stats-grid">

        <div class="stat-card">
            <div class="stat-image-box">
                <img src="../assets/images/stat-projects.jpg" alt="Total Projects">
            </div>
            <span class="stat-label">Total Projects</span>
            <span class="stat-number" id="total-projects"><?= $totalProjects ?></span>
        </div>

        <div class="stat-card">
            <div class="stat-image-box">
                <img src="../assets/images/stat-offers.jpg" alt="Received Offers">
            </div>
            <span class="stat-label">Received Offers</span>
            <span class="stat-number" id="total-offers"><?= $totalOffers ?></span>
        </div>

        <div class="stat-card">
            <div class="stat-image-box">
                <img src="../assets/images/stat-active.jpg" alt="Active Projects">
            </div>
            <span class="stat-label">Active Projects</span>
            <span class="stat-number" id="active-projects"><?= $activeProjects ?></span>
        </div>

        <div class="stat-card">
            <div class="stat-image-box">
                <img src="../assets/images/stat-completed.jpg" alt="Completed Projects">
            </div>
            <span class="stat-label">Completed Projects</span>
            <span class="stat-number" id="done-projects"><?= $doneProjects ?></span>
        </div>

    </div>

    <section class="section">
        <div class="section-title">
            <span>What would you like to do?</span>
            <h2>Quick Actions</h2>
        </div>

        <div class="actions-grid">
            <a href="post_project.php" class="action-card">
                <div class="icon-box">✦</div>
                <h3>Post a New Project</h3>
                <p>Describe your idea and wait for developer offers.</p>
            </a>

            <a href="my_projects.php" class="action-card">
                <div class="icon-box">📋</div>
                <h3>My Projects & Offers</h3>
                <p>Review developer offers and accept or reject them.</p>
            </a>

            <a href="../contact.php" class="action-card">
                <div class="icon-box">💬</div>
                <h3>Contact Us</h3>
                <p>For any questions or support.</p>
            </a>
        </div>
    </section>

</main>

<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-brand">
            <div class="footer-logo">
                <img src="../assets/images/logo.png" alt="BridgeX">
                <span class="logo-text">Bridge<span style="color:var(--pink-main)">X</span></span>
            </div>
            <p>We connect clients with the best freelance developers.</p>
        </div>

        <div class="footer-links">
            <h4>Links</h4>
            <a href="post_project.php">Post Project</a>
            <a href="my_projects.php">My Projects</a>
            <a href="../contact.php">Contact Us</a>
        </div>
    </div>

    <div class="footer-bottom">© 2026 BridgeX Platform — All rights reserved</div>
</footer>

</body>
</html>