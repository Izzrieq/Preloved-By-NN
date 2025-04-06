<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    /* Footer Styles */
    .footer {
        background-color: #f1f1f1;
        /* Dark background */
        color: black;
        padding: 30px 0;
        text-align: center;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        flex-wrap: wrap;
        /* Ensures it wraps on smaller screens */
    }

    .footer-logo img {
        width: 150px;
    }

    .footer-links ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
        display: flex;
        gap: 20px;
    }

    .footer-links a {
        color: black;
        text-decoration: none;
        font-size: 1rem;
        transition: color 0.3s ease-in-out;
    }

    .footer-links a:hover {
        color: #f4a261;
        /* Highlight color on hover */
    }

    .footer-social {
        display: flex;
        gap: 15px;
    }

    .footer-social a {
        color: black;
        font-size: 1.5rem;
        transition: color 0.3s ease-in-out;
    }

    .footer-social a:hover {
        color: #f4a261;
        /* Highlight color on hover */
    }

    /* Footer Bottom */
    .footer-bottom {
        margin-top: 20px;
        font-size: 0.9rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            text-align: center;
        }

        .footer-logo {
            margin-bottom: 20px;
        }

        .footer-links ul {
            flex-direction: column;
            gap: 10px;
        }

        .footer-social {
            margin-top: 20px;
        }
    }

    @media (max-width: 480px) {
        .footer-links ul {
            gap: 5px;
        }

        .footer-social a {
            font-size: 1.2rem;
        }
    }
</style>

<body>
    <!-- Footer Component -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="assets/logo2.png" alt="Logo" />
            </div>
            <div class="footer-links">
                <ul>
                    <li><a href="about_us.php">About Us</a></li>
                    <li><a href="privacy_policy.php">Privacy Policy</a></li>
                    <li><a href="terms_of_service.php">Terms of Service</a></li>
                    <li><a href="contact_us.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-social">
                <a href="https://t.me/shoppingwithnns" class="social-icon"><i class="fab fa-telegram"></i></a>
                <a href="https://www.tiktok.com/@psbynn._?_t=ZS-8uwPpSbgG8O&_r=1" class="social-icon"><i class="fab fa-tiktok"></i></a>
                <a href="https://www.instagram.com/preloved.bynn_?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="social-icon"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 prelovedbynn. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>