<?php
require_once '../includes/auth.php';
requireRole('client');

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = isset($_POST['title']) ? trim($_POST['title']) : '';
    $category    = isset($_POST['category']) ? trim($_POST['category']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $budget      = isset($_POST['budget']) ? trim($_POST['budget']) : '';
    $duration    = isset($_POST['duration']) ? trim($_POST['duration']) : '';
    $skills      = isset($_POST['skills']) ? trim($_POST['skills']) : '';
    $notes       = isset($_POST['notes']) ? trim($_POST['notes']) : '';

    if (!$title || !$category || !$description || !$budget || !$duration) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $pdo = getDB();

            $features = "Required Skills or Technologies: " . $skills . "\n";
            $features .= "Additional Notes: " . $notes;

            $stmt = $pdo->prepare("
                INSERT INTO projects 
                (client_id, project_type, title, description, budget, duration, features, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'open', NOW())
            ");

            $stmt->execute(array(
                    getUserId(),
                    $category,
                    $title,
                    $description,
                    $budget,
                    $duration,
                    $features
            ));

            $success = 'Your project has been posted successfully! Developers will contact you soon.';
        } catch (Exception $e) {
            $error = 'An error occurred while saving. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Post a New Project</title>
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
        <a href="post_project.php" class="active-link">Post Project</a>
        <a href="my_projects.php">My Projects</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="section" style="max-width:780px; margin-left:auto; margin-right:auto;">

        <div class="page-hero" style="text-align:left; padding:40px 0 20px;">
            <div class="hero-badge">✦ New Project</div>
            <h1 style="margin-top:12px; font-size:36px;">Tell us about your <span class="pink-text">project</span></h1>
            <p style="color:var(--text-muted); margin-top:10px;">Answer the following questions to help developers understand your requirements clearly.</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="my_projects.php">View My Projects →</a></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-progress">
            <div class="progress-step active" data-step="1">
                <span class="step-num">1</span>
                <span class="step-label">Idea</span>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step" data-step="2">
                <span class="step-num">2</span>
                <span class="step-label">Details</span>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step" data-step="3">
                <span class="step-num">3</span>
                <span class="step-label">Summary</span>
            </div>
        </div>

        <form method="POST" action="post_project.php" id="projectForm" novalidate>

            <div class="form-step" id="step-1">
                <div class="glass-panel form-card">
                    <h3 class="form-section-title">💡 What is your project idea?</h3>

                    <div class="form-group">
                        <label>Project Title <span class="required">*</span></label>
                        <input type="text" name="title" id="title" placeholder="Example: Appointment Booking App" value="<?= htmlspecialchars(isset($_POST['title']) ? $_POST['title'] : '') ?>">
                        <span class="field-error" id="err-title"></span>
                    </div>

                    <div class="form-group">
                        <label>Project Category <span class="required">*</span></label>
                        <select name="category" id="category">
                            <option value="">-- Select Category --</option>
                            <option value="web" <?= ((isset($_POST['category']) ? $_POST['category'] : '') === 'web') ? 'selected' : '' ?>>Website</option>
                            <option value="mobile" <?= ((isset($_POST['category']) ? $_POST['category'] : '') === 'mobile') ? 'selected' : '' ?>>Mobile App</option>
                            <option value="design" <?= ((isset($_POST['category']) ? $_POST['category'] : '') === 'design') ? 'selected' : '' ?>>UI/UX Design</option>
                            <option value="backend" <?= ((isset($_POST['category']) ? $_POST['category'] : '') === 'backend') ? 'selected' : '' ?>>Backend / API</option>
                            <option value="ecommerce" <?= ((isset($_POST['category']) ? $_POST['category'] : '') === 'ecommerce') ? 'selected' : '' ?>>E-commerce Store</option>
                            <option value="other" <?= ((isset($_POST['category']) ? $_POST['category'] : '') === 'other') ? 'selected' : '' ?>>Other</option>
                        </select>
                        <span class="field-error" id="err-category"></span>
                    </div>

                    <div class="form-group">
                        <label>Detailed Project Description <span class="required">*</span></label>
                        <textarea name="description" id="description" rows="5" placeholder="Describe your idea in detail: What does the project do? Who are the target users? What problem does it solve?"><?= htmlspecialchars(isset($_POST['description']) ? $_POST['description'] : '') ?></textarea>
                        <span class="field-error" id="err-description"></span>
                    </div>

                    <div class="step-nav">
                        <button type="button" class="primary-btn" onclick="nextStep(1)">Next →</button>
                    </div>
                </div>
            </div>

            <div class="form-step hidden" id="step-2">
                <div class="glass-panel form-card">
                    <h3 class="form-section-title">⚙️ Details & Requirements</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Estimated Budget (SAR) <span class="required">*</span></label>
                            <input type="text" name="budget" id="budget" placeholder="Example: 2000" value="<?= htmlspecialchars(isset($_POST['budget']) ? $_POST['budget'] : '') ?>">
                            <span class="field-error" id="err-budget"></span>
                        </div>

                        <div class="form-group">
                            <label>Expected Duration <span class="required">*</span></label>
                            <select name="duration" id="duration">
                                <option value="">-- Select --</option>
                                <option value="less_week" <?= ((isset($_POST['duration']) ? $_POST['duration'] : '') === 'less_week') ? 'selected' : '' ?>>Less than a week</option>
                                <option value="1_2_weeks" <?= ((isset($_POST['duration']) ? $_POST['duration'] : '') === '1_2_weeks') ? 'selected' : '' ?>>1-2 weeks</option>
                                <option value="1_month" <?= ((isset($_POST['duration']) ? $_POST['duration'] : '') === '1_month') ? 'selected' : '' ?>>1 month</option>
                                <option value="2_3_months" <?= ((isset($_POST['duration']) ? $_POST['duration'] : '') === '2_3_months') ? 'selected' : '' ?>>2-3 months</option>
                                <option value="more_3" <?= ((isset($_POST['duration']) ? $_POST['duration'] : '') === 'more_3') ? 'selected' : '' ?>>More than 3 months</option>
                            </select>
                            <span class="field-error" id="err-duration"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Required Skills or Technologies</label>
                        <input type="text" name="skills" id="skills" placeholder="Example: PHP, MySQL, React, Laravel ..." value="<?= htmlspecialchars(isset($_POST['skills']) ? $_POST['skills'] : '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Any other details you want to add..."><?= htmlspecialchars(isset($_POST['notes']) ? $_POST['notes'] : '') ?></textarea>
                    </div>

                    <div class="step-nav">
                        <button type="button" class="secondary-btn" onclick="prevStep(2)">← Back</button>
                        <button type="button" class="primary-btn" onclick="nextStep(2)">Next →</button>
                    </div>
                </div>
            </div>

            <div class="form-step hidden" id="step-3">
                <div class="glass-panel form-card">
                    <h3 class="form-section-title">📋 Project Summary</h3>
                    <p style="color:var(--text-muted); margin-bottom:24px;">Review the information before posting.</p>

                    <div class="summary-grid" id="project-summary">
                        <!-- Filled by JS -->
                    </div>

                    <div class="step-nav">
                        <button type="button" class="secondary-btn" onclick="prevStep(3)">← Edit</button>
                        <button type="submit" class="primary-btn">🚀 Post Project Now</button>
                    </div>
                </div>
            </div>

        </form>
    </section>
</main>

<footer class="site-footer">
    <div class="footer-bottom">© 2026 BridgeX Platform — All rights reserved</div>
</footer>

<script src="../assets/js/project_form.js"></script>
</body>
</html>

