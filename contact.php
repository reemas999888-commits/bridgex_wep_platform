<?php
require_once 'includes/db.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = isset($_POST['name'])    ? trim($_POST['name'])    : '';
    $email   = isset($_POST['email'])   ? trim($_POST['email'])   : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        try {
            $pdo  = getDB();
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            $success = 'Your message has been sent successfully!';
        } catch (PDOException $e) {
            error_log("Contact Error: " . $e->getMessage());
            $error = 'Server error. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — BridgeX</title>
    <link rel="stylesheet" href="/bridgx/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        .contact-hero {
            width: 90%;
            margin: 60px auto 0;
            border-radius: 28px;
            overflow: hidden;
            position: relative;
            min-height: 420px;
            display: flex;
            align-items: center;
        }

        .contact-hero-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            z-index: 0;
        }

        .contact-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                    90deg,
                    rgba(8, 14, 22, 0.92) 0%,
                    rgba(8, 14, 22, 0.75) 50%,
                    rgba(8, 14, 22, 0.2) 100%
            );
            z-index: 1;
        }

        .contact-hero-text {
            position: relative;
            z-index: 2;
            padding: 60px;
            max-width: 560px;
        }

        .contact-hero-text .hero-badge { margin-bottom: 18px; }

        .contact-hero-text h1 {
            font-family: 'Syne', sans-serif;
            font-size: 56px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 18px;
            color: var(--text-main);
        }

        .contact-hero-text h1 span { color: var(--pink-light); }

        .contact-hero-text p {
            color: var(--text-muted);
            font-size: 16px;
            line-height: 1.75;
        }

        .contact-form-section {
            width: 90%;
            margin: 60px auto 80px;
        }

        .contact-form-card {
            background: linear-gradient(160deg, #111A24 0%, #0d1520 100%);
            border: 1px solid var(--border-color);
            border-radius: 28px;
            padding: 60px 50px;
            position: relative;
            overflow: hidden;
        }

        .contact-form-card::before {
            content: '';
            position: absolute;
            bottom: -100px; right: -100px;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(215,101,154,0.12), transparent 70%);
            pointer-events: none;
        }

        .form-header { text-align: center; margin-bottom: 48px; }

        .form-header .section-badge {
            color: var(--pink-light);
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .form-header .section-badge::before {
            content: '';
            width: 6px; height: 6px;
            background: var(--pink-light);
            border-radius: 50%;
        }

        .form-header h2 {
            font-family: 'Syne', sans-serif;
            font-size: 40px;
            font-weight: 800;
            color: var(--text-main);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-field { display: flex; flex-direction: column; gap: 8px; }
        .form-field.full-width { grid-column: 1 / -1; }

        .form-field label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
            font-family: 'DM Sans', sans-serif;
        }

        .form-field label span { color: var(--pink-light); }

        .form-field input,
        .form-field textarea {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 14px 18px;
            color: var(--text-main);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            transition: border-color 0.3s, box-shadow 0.3s;
            outline: none;
            width: 100%;
        }

        .form-field input::placeholder,
        .form-field textarea::placeholder { color: rgba(174,183,194,0.45); }

        .form-field input:focus,
        .form-field textarea:focus {
            border-color: var(--pink-main);
            box-shadow: 0 0 0 3px rgba(215,101,154,0.12);
            background: rgba(215,101,154,0.04);
        }

        .form-field textarea { resize: vertical; min-height: 150px; }

        .submit-row {
            grid-column: 1 / -1;
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--pink-main), var(--pink-light));
            color: #fff;
            border: none;
            padding: 15px 48px;
            border-radius: 30px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Syne', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(215,101,154,0.3);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 40px rgba(215,101,154,0.45);
        }

        .btn-submit svg { width: 18px; height: 18px; fill: #fff; }

        .alert {
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 28px;
            font-size: 14px;
            text-align: center;
        }
        .alert-error   { background: rgba(220,53,69,0.12); border: 1px solid rgba(220,53,69,0.3); color: #ff6b7a; }
        .alert-success { background: rgba(40,167,69,0.12); border: 1px solid rgba(40,167,69,0.3); color: #5fdd82; }

        @media (max-width: 768px) {
            .contact-hero-text { padding: 40px 28px; }
            .contact-hero-text h1 { font-size: 38px; }
            .form-grid { grid-template-columns: 1fr; }
            .form-field.full-width, .submit-row { grid-column: 1; }
            .contact-form-card { padding: 36px 24px; }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<section class="contact-hero">
    <img src="/bridgx/assets/images/contact-hero.jpg" alt="Contact Hero" class="contact-hero-bg">
    <div class="contact-hero-overlay"></div>
    <div class="contact-hero-text">
        <span class="hero-badge">We're Here to Help</span>
        <h1>Contact <span>Us</span></h1>
        <p>Have a project in mind or need expert guidance?<br>
            Reach out and let's build something great together.</p>
    </div>
</section>

<section class="contact-form-section">
    <div class="contact-form-card">
        <div class="form-header">
            <div class="section-badge">CONTACT US</div>
            <h2>Get in Touch</h2>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="contact.php">
            <div class="form-grid">

                <div class="form-field">
                    <label for="name">Full Name <span>*</span></label>
                    <input type="text" id="name" name="name" placeholder="Your name" required
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <div class="form-field">
                    <label for="email">Email Address <span>*</span></label>
                    <input type="email" id="email" name="email" placeholder="your@email.com" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-field full-width">
                    <label for="message">Message <span>*</span></label>
                    <textarea id="message" name="message" placeholder="Tell us about your project or question..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                </div>

                <div class="submit-row">
                    <button type="submit" class="btn-submit">
                        Send Message
                        <svg viewBox="0 0 24 24"><path d="M2 21l21-9L2 3v7l15 2-15 2z"/></svg>
                    </button>
                </div>

            </div>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
