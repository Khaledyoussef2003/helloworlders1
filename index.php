<?php
// Include visitor tracking
require_once 'visitor_tracking.php';

// Database connection
$servername = "localhost";
$username = "root"; // change if needed
$password = ""; // change if needed
$dbname = "website_projects";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $project_idea = $conn->real_escape_string($_POST['project_idea']);
    $description = $conn->real_escape_string($_POST['description']);

    $sql = "INSERT INTO project_requests (name, email, phone, project_idea, description)
            VALUES ('$name', '$email', '$phone', '$project_idea', '$description')";

    if ($conn->query($sql) === TRUE) {
        $message = "✅ Your request has been submitted successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello Worlders | You Dream It, We Build It</title>
    <style>
        :root {
            --primary: #4e37ce;
            --primary-light: #7561d8;
            --primary-dark: #3521a8;
            --secondary: #ff4081;
            --secondary-light: #ff79b0;
            --secondary-dark: #e73370;
            --accent: #43cbff;
            --accent-light: #6bd7ff;
            --accent-dark: #2bb5e8;
            --success: #2ecc71;
            --warning: #f39c12;
            --dark: #0c023d;
            --darker: #070321;
            --light: #ffffff;
            --gray: #f5f5f7;
            --gray-dark: #e0e0e8;
            --text: #333333;
            --text-light: #666666;
            --card-bg: #ffffff;
            --section-bg: #f9f9ff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 100%);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 5%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            min-height: 70px;
        }

        .header-container.scrolled {
            padding: 8px 5%;
            background: rgba(12, 2, 61, 0.95);
        }

        .logo {
            display: flex;
            align-items: center;
            flex: 0 0 auto;
        }

        .logo a {
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .logo-img {
            height: 50px;
            width: auto;
            transition: transform 0.3s ease;
            object-fit: contain;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .nav-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .nav-button {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 15px rgba(78, 55, 206, 0.3);
            position: relative;
            overflow: hidden;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .nav-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .nav-button:hover::before {
            left: 100%;
        }

        .nav-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(78, 55, 206, 0.5);
        }

        .nav-button i {
            margin-right: 6px;
            font-size: 0.9rem;
        }

        header {
            text-align: center;
            padding: 100px 20px 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: relative;
            overflow: hidden;
            margin-top: 0;
        }

        header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(67, 203, 255, 0.15) 0%, transparent 20%),
                radial-gradient(circle at 80% 20%, rgba(255, 64, 129, 0.15) 0%, transparent 20%),
                radial-gradient(circle at 40% 40%, rgba(46, 204, 113, 0.1) 0%, transparent 20%);
        }

        h1 {
            font-size: 3.5rem;
            margin-bottom: 15px;
            font-weight: 800;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }

        .tagline {
            font-size: 1.5rem;
            margin-bottom: 30px;
            font-weight: 300;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            opacity: 0.9;
        }

        section {
            padding: 80px 5%;
            scroll-margin-top: 100px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: white;
            display: inline-block;
            margin-bottom: 15px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--secondary));
            border-radius: 2px;
        }

        .vision {
            background: rgba(255, 255, 255, 0.05);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .vision p {
            margin-bottom: 20px;
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--gray);
        }

        .projects {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .project-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .project-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--accent), var(--secondary));
        }

        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .project-image {
            width: 100%;
            height: 180px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.2);
        }

        .project-card img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.5s ease;
            padding: 15px;
        }

        .project-card:hover img {
            transform: scale(1.05);
        }

        .project-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .project-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: white;
        }

        .project-card p {
            margin-bottom: 20px;
            color: var(--gray-dark);
            flex-grow: 1;
        }

        .project-card a {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            align-self: flex-start;
            margin-top: auto;
            box-shadow: 0 4px 15px rgba(78, 55, 206, 0.3);
        }

        .project-card a:hover {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 55, 206, 0.5);
        }

        form {
            background: rgba(255, 255, 255, 0.05);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        input, textarea {
            width: 100%;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            color: white;
        }

        input::placeholder, textarea::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(67, 203, 255, 0.2);
            background: rgba(255, 255, 255, 0.15);
        }


        input[type="submit"] {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 64, 129, 0.3);
        }

        input[type="submit"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 64, 129, 0.5);
            background: linear-gradient(135deg, var(--secondary-light) 0%, var(--secondary) 100%);
        }

        .contact {
            background: rgba(255, 255, 255, 0.05);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contact > p {
            color: var(--gray);
            text-align: center;
            margin-bottom: 30px;
        }

        .contact-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .contact-method {
            text-align: center;
            padding: 30px 20px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-radius: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: inherit;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contact-method:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.1) 100%);
        }

        .contact-method i {
            font-size: 2.5rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .contact-method h3 {
            margin-bottom: 15px;
            color: white;
        }

        .contact-method p {
            color: var(--gray-dark);
        }

        .message {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            border-radius: 10px;
            font-weight: 600;
        }

        .success {
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        .error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        footer {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            text-align: center;
            padding: 40px 20px;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(67, 203, 255, 0.15) 0%, transparent 20%),
                radial-gradient(circle at 80% 20%, rgba(255, 64, 129, 0.15) 0%, transparent 20%);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .footer-content p {
            margin: 10px 0;
        }

        /* Animation for elements */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .project-card, .contact-method, .vision, form, .contact {
            animation: fadeIn 0.6s ease-out;
        }

        /* Responsive design */
        @media (max-width: 992px) {
            .header-container {
                flex-direction: row;
                padding: 10px 15px;
            }
            
            .nav-buttons {
                margin-top: 0;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .nav-button {
                padding: 8px 15px;
                font-size: 0.85rem;
            }
            
            .logo-img {
                height: 40px;
            }
            
            h1 {
                font-size: 2.8rem;
            }
            
            .tagline {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                padding: 10px;
            }
            
            .logo {
                margin-bottom: 10px;
            }
            
            .nav-buttons {
                width: 100%;
                justify-content: center;
            }
            
            section {
                padding: 60px 20px;
            }
            
            .vision, form, .contact {
                padding: 30px;
            }
            
            .projects {
                grid-template-columns: 1fr;
            }
            
            .contact-methods {
                grid-template-columns: 1fr;
            }
            
            h1 {
                font-size: 2.5rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .project-image {
                height: 160px;
            }
        }

        @media (max-width: 576px) {
            .nav-button {
                padding: 8px 12px;
                font-size: 0.8rem;
            }
            
            .nav-button i {
                margin-right: 4px;
            }
            
            .logo-img {
                height: 35px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .tagline {
                font-size: 1rem;
            }
            
            .vision, form, .contact {
                padding: 20px;
            }
            
            .project-image {
                height: 140px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<div class="header-container" id="header">
    <div class="logo">
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>">
            <img src="1000439478.png" alt="Hello Worlders Logo" class="logo-img">
        </a>
    </div>
    
    <div class="nav-buttons">
        <button class="nav-button" onclick="scrollToSection('vision')">
            <i class="fas fa-lightbulb"></i> Our Vision
        </button>
        <button class="nav-button" onclick="scrollToSection('projects')">
            <i class="fas fa-project-diagram"></i> Our Projects
        </button>
        <button class="nav-button" onclick="scrollToSection('submit-idea')">
            <i class="fas fa-envelope"></i> Submit Project
        </button>
        <button class="nav-button" onclick="scrollToSection('contact')">
            <i class="fas fa-envelope"></i> Contact Us
        </button>
    </div>
</div>

<header>
    <h1>Hello Worlders</h1>
    <p class="tagline">You dream it, we build it.</p>
</header>

<section id="vision">
    <div class="section-title">
        <h2>Our Vision</h2>
    </div>
    <div class="vision">
        <p>At Hello Worlders, we believe in turning ideas into reality through innovative web solutions. Our team is dedicated to creating user-friendly, visually appealing, and functional websites that help businesses and individuals establish their online presence.</p>
        <p>We strive to make web development accessible to everyone, offering personalized solutions tailored to your specific needs. Whether you're a small business owner, an entrepreneur with a great idea, or just someone looking to establish an online presence, we're here to bring your vision to life.</p>
    </div>
</section>

<section id="projects">
    <div class="section-title">
        <h2>Our Projects</h2>
        <h4> This is a demo of our main projects </h4>
    </div>
    <div class="projects">
        <div class="project-card">
            <div class="project-image">
                <img src="oneshoot/one shoot.png" alt="OneShoot Logo">
            </div>
            <div class="project-content">
                <h3>One Shoot</h3>
                <p>Website to watch the old football matches.</p>
                <a href="oneshoot/index.php" target="_blank">View Project</a>
            </div>
        </div>
        <div class="project-card">
            <div class="project-image">
                <img src="flowersshop/flowershop/flower.jpg" alt="Flowers shop Logo">
            </div>
            <div class="project-content">
                <h3>Flowers shop</h3>
                <p>Shop your favourite flowers from here.</p>
                <a href="flowersshop/flowershop/client.php" target="_blank">View Project</a>
            </div>
        </div>
       <div class="project-card">
    <div class="project-image">
        <img src="telecomwebsite\images\kycell.png" alt="KY Cell Logo">
    </div>
    <div class="project-content">
        <h3>KY Cell</h3>
        <p>Purchase gift cards and recharge cards.</p>
        <a href="telecomwebsite/index.php" target="_blank">View Project</a>
    </div>
</div>
        <div class="project-card">
            <div class="project-image">
                <img src="gpacalculator/favicon.png" alt="Gpa calculator Logo">
            </div>
            <div class="project-content">
                <h3>Gpa Calculator</h3>
                <p>Helps the student to calculate their Gpa.</p>
                <a href="gpacalculator/index.html" target="_blank">View Project</a>
            </div>
        </div>
    </div>
</section>

<section id="submit-idea">
    <div class="section-title">
        <h2>Submit Your Project Idea</h2>
    </div>
    <?php if ($message) { 
        echo '<div class="message ' . (strpos($message, '✅') !== false ? 'success' : 'error') . '">' . $message . '</div>'; 
    } ?>
    <form method="POST" action="">
        <div class="form-group">
            <input type="text" name="name" placeholder="Your Name" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Your Email" required>
        </div>
        <div class="form-group">
            <input type="tel" name="phone" placeholder="Your Phone Number" required>
        </div>
        <div class="form-group">
            <input type="text" name="project_idea" placeholder="Your Project Idea" required>
        </div>
        <div class="form-group">
            <textarea name="description" placeholder="Describe your project..." rows="4" required></textarea>
        </div>
        <input type="submit" value="Submit Request">
    </form>
</section>

<section id="contact">
    <div class="section-title">
        <h2>Contact Us</h2>
    </div>
    <div class="contact">
        <p>Have questions or want to discuss your project? Reach out to us through any of these methods:</p>
        
        <div class="contact-methods">
            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=worldershello@gmail.com" target="_blank" class="contact-method">
                <i class="fas fa-envelope"></i>
                <h3>Email Us</h3>
                <p>worldershello@gmail.com</p>
            </a>
            
            <a href="https://instagram.com/hello_worlders_" target="_blank" class="contact-method">
                <i class="fab fa-instagram"></i>
                <h3>Instagram</h3>
                <p>@hello_worlders_</p>
            </a>
            
            <a href="https://wa.me/qr/FXNZDOZ3NBNVB1" target="_blank" class="contact-method">
                <i class="fab fa-whatsapp"></i>
                <h3>WhatsApp</h3>
                <p> Chat directly </p>
            </a>
        </div>
    </div>
</section>

<footer>
    <div class="footer-content">
        <p>&copy; <?php echo date('Y'); ?> Hello Worlders. All rights reserved.</p>
        <p>You dream it, we build it.</p>
    </div>
</footer>

<script>
    function scrollToSection(sectionId) {
        const element = document.getElementById(sectionId);
        const headerHeight = document.querySelector('.header-container').offsetHeight;
        
        window.scrollTo({
            top: element.offsetTop - headerHeight - 20,
            behavior: 'smooth'
        });
    }

    // Header scroll effect
    window.addEventListener('scroll', function() {
        const header = document.getElementById('header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
</script>
</body>
</html>