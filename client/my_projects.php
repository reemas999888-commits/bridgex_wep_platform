<?php
require_once '../includes/auth.php';
requireRole('client');

$pdo      = getDB();
$clientId = getUserId();

// Fetch client projects with offer count
$stmt = $pdo->prepare("
    SELECT p.*, 
           COUNT(o.id) AS offer_count
    FROM projects p
    LEFT JOIN offers o ON o.project_id = p.id
    WHERE p.client_id = ?
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
$stmt->execute(array($clientId));
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Accept / reject offer
if (isset($_GET['accept']) && is_numeric($_GET['accept'])) {
    $offerId = (int)$_GET['accept'];

    $pdo->prepare("UPDATE offers SET status='accepted' WHERE id=?")->execute(array($offerId));

    $pdo->prepare("
        UPDATE projects SET status='in_progress'
        WHERE id = (SELECT project_id FROM offers WHERE id=?) AND client_id=?
    ")->execute(array($offerId, $clientId));

    header('Location: my_projects.php?msg=accepted');
    exit;
}

if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
    $offerId = (int)$_GET['reject'];

    $pdo->prepare("UPDATE offers SET status='rejected' WHERE id=?")->execute(array($offerId));

    header('Location: my_projects.php?msg=rejected');
    exit;
}

// Fetch offers for a selected project
$selectedOffers  = array();
$selectedProject = null;

if (isset($_GET['view_offers']) && is_numeric($_GET['view_offers'])) {
    $projId = (int)$_GET['view_offers'];

    $sp = $pdo->prepare("SELECT * FROM projects WHERE id=? AND client_id=?");
    $sp->execute(array($projId, $clientId));
    $selectedProject = $sp->fetch(PDO::FETCH_ASSOC);

    if ($selectedProject) {
        $so = $pdo->prepare("
            SELECT o.*, u.name AS dev_name, u.email AS dev_email
            FROM offers o
            JOIN users u ON u.id = o.developer_id
            WHERE o.project_id = ?
            ORDER BY o.created_at DESC
        ");
        $so->execute(array($projId));
        $selectedOffers = $so->fetchAll(PDO::FETCH_ASSOC);
    }
}

$statusLabels = array(
        'open'        => array('label' => 'Open', 'class' => 'badge-open'),
        'in_progress' => array('label' => 'In Progress', 'class' => 'badge-progress'),
        'closed'      => array('label' => 'Closed', 'class' => 'badge-closed')
);

$offerStatusLabels = array(
        'pending'  => array('label' => 'Pending', 'class' => 'badge-pending'),
        'accepted' => array('label' => 'Accepted', 'class' => 'badge-accepted'),
        'rejected' => array('label' => 'Rejected', 'class' => 'badge-rejected')
);

$typeLabels = array(
        'web' => 'Website',
        'mobile' => 'Mobile App',
        'design' => 'UI/UX Design',
        'backend' => 'Backend / API',
        'ecommerce' => 'E-commerce Store',
        'other' => 'Other'
);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — My Projects</title>
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
        <a href="dashboard.php">Dashboard</a>
        <a href="post_project.php">Post Project</a>
        <a href="my_projects.php" class="active-link">My Projects</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="section">

        <div class="page-hero" style="text-align:left; padding:40px 0 20px;">
            <div class="hero-badge">📋 My Projects</div>
            <h1 style="margin-top:12px; font-size:36px;">Track <span class="pink-text">Projects & Offers</span></h1>
            <p style="color:var(--text-muted); margin-top:8px;">Review your projects, check offers, and make your decision.</p>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'accepted'): ?>
                <div class="alert alert-success">✅ Offer accepted successfully and project status updated.</div>
            <?php elseif ($_GET['msg'] === 'rejected'): ?>
                <div class="alert alert-error">❌ Offer rejected.</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (empty($projects)): ?>
            <div class="empty-state glass-panel">
                <div style="font-size:48px;">📭</div>
                <h3>No projects yet</h3>
                <p style="color:var(--text-muted); margin:12px 0 20px;">Post your first project and start receiving offers from developers.</p>
                <a href="post_project.php" class="primary-btn">+ Post a New Project</a>
            </div>
        <?php else: ?>

            <div class="projects-list">
                <?php foreach ($projects as $proj): ?>
                    <?php
                    $projectStatus = isset($proj['status']) ? $proj['status'] : 'open';

                    if (isset($statusLabels[$projectStatus])) {
                        $st = $statusLabels[$projectStatus];
                    } else {
                        $st = array(
                                'label' => $projectStatus,
                                'class' => 'badge-open'
                        );
                    }

                    $projectType = isset($proj['project_type']) ? $proj['project_type'] : '';
                    $projectTypeLabel = isset($typeLabels[$projectType]) ? $typeLabels[$projectType] : $projectType;
                    ?>

                    <div class="project-row glass-panel <?= $selectedProject && $selectedProject['id'] == $proj['id'] ? 'project-row--active' : '' ?>">
                        <div class="project-row-header">
                            <div>
                                <span class="badge <?= $st['class'] ?>"><?= htmlspecialchars($st['label']) ?></span>
                                <h3 class="project-title"><?= htmlspecialchars($proj['title']) ?></h3>
                                <span class="project-meta">
                                    <?= htmlspecialchars($projectTypeLabel) ?> · <?= date('d/m/Y', strtotime($proj['created_at'])) ?>
                                </span>
                            </div>

                            <div class="project-row-actions">
                                <span class="offers-badge"><?= $proj['offer_count'] ?> offers</span>
                                <a href="my_projects.php?view_offers=<?= $proj['id'] ?>" class="secondary-btn btn-sm">
                                    View Offers ↓
                                </a>
                            </div>
                        </div>

                        <p class="project-desc"><?= nl2br(htmlspecialchars(mb_substr($proj['description'], 0, 160))) ?>...</p>

                        <div class="project-tracker">
                            <div class="tracker-step <?= in_array($projectStatus, array('open', 'in_progress', 'closed')) ? 'done' : '' ?>">
                                <span class="tracker-dot"></span>
                                <span>Posted</span>
                            </div>

                            <div class="tracker-line"></div>

                            <div class="tracker-step <?= $proj['offer_count'] > 0 ? 'done' : '' ?>">
                                <span class="tracker-dot"></span>
                                <span>Receiving Offers</span>
                            </div>

                            <div class="tracker-line"></div>

                            <div class="tracker-step <?= in_array($projectStatus, array('in_progress', 'closed')) ? 'done' : '' ?>">
                                <span class="tracker-dot"></span>
                                <span>Offer Accepted</span>
                            </div>

                            <div class="tracker-line"></div>

                            <div class="tracker-step <?= $projectStatus === 'closed' ? 'done' : '' ?>">
                                <span class="tracker-dot"></span>
                                <span>Completed</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($selectedProject): ?>
                <div class="offers-panel glass-panel" id="offers-panel">
                    <h3 class="form-section-title">
                        📬 Offers for Project:
                        <span class="pink-text"><?= htmlspecialchars($selectedProject['title']) ?></span>
                    </h3>

                    <?php if (empty($selectedOffers)): ?>
                        <div class="empty-state" style="padding:30px 0;">
                            <div style="font-size:36px;">🕐</div>
                            <p style="color:var(--text-muted); margin-top:10px;">There are no offers for this project yet.</p>
                        </div>
                    <?php else: ?>

                        <div class="offers-grid">
                            <?php foreach ($selectedOffers as $offer): ?>
                                <?php
                                $offerStatus = isset($offer['status']) ? $offer['status'] : 'pending';

                                if (isset($offerStatusLabels[$offerStatus])) {
                                    $os = $offerStatusLabels[$offerStatus];
                                } else {
                                    $os = array(
                                            'label' => $offerStatus,
                                            'class' => 'badge-pending'
                                    );
                                }
                                ?>

                                <div class="offer-card">
                                    <div class="offer-card-header">
                                        <div class="dev-avatar"><?= mb_substr($offer['dev_name'], 0, 1) ?></div>

                                        <div>
                                            <strong><?= htmlspecialchars($offer['dev_name']) ?></strong>
                                            <div style="font-size:12px; color:var(--text-muted);">
                                                <?= htmlspecialchars($offer['dev_email']) ?>
                                            </div>
                                        </div>

                                        <span class="badge <?= $os['class'] ?>" style="margin-left:auto;">
                                            <?= htmlspecialchars($os['label']) ?>
                                        </span>
                                    </div>

                                    <div class="offer-details-row">
                                        <span class="offer-detail">
                                            <strong>💰 Price:</strong> <?= htmlspecialchars($offer['price']) ?> SAR
                                        </span>

                                        <span class="offer-detail">
                                            <strong>⏱ Duration:</strong> <?= htmlspecialchars($offer['duration']) ?>
                                        </span>
                                    </div>

                                    <p class="offer-message"><?= nl2br(htmlspecialchars($offer['message'])) ?></p>

                                    <?php if ($offerStatus === 'pending'): ?>
                                        <div class="offer-actions">
                                            <a href="my_projects.php?accept=<?= $offer['id'] ?>&view_offers=<?= $selectedProject['id'] ?>"
                                               class="btn-accept"
                                               onclick="return confirm('Are you sure you want to accept this offer?')">
                                                ✅ Accept Offer
                                            </a>

                                            <a href="my_projects.php?reject=<?= $offer['id'] ?>&view_offers=<?= $selectedProject['id'] ?>"
                                               class="btn-reject"
                                               onclick="return confirm('Are you sure you want to reject this offer?')">
                                                ❌ Reject
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </section>
</main>

<footer class="site-footer">
    <div class="footer-bottom">© 2026 BridgeX Platform — All rights reserved</div>
</footer>

</body>
</html>