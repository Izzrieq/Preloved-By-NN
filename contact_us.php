<!-- contact us start -->
<?php
session_start();
include 'components/nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Preloved by NN</title>
    <link rel="stylesheet" href="styles/style.css">
</head>

<style>
    .contact-us-section {
        background-color: #f7f7f7;
        padding: 50px 20px;
    }

    .contact-us-section h1 {
        text-align: center;
        font-size: 2.5em;
        color: #333;
        margin-bottom: 20px;
    }

    .contact-us-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .contact-form-container {
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .contact-us-section p {
        font-size: 1.2em;
        color: #555;
        line-height: 1.6;
        margin-bottom: 20px;
        text-align: center;
    }

    .contact-form {
        max-width: 600px;
        margin: 0 auto;
        text-align: left;
    }

    .contact-form label {
        font-weight: bold;
        margin-top: 10px;
    }

    .contact-form input,
    .contact-form textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .contact-form button {
        background-color: #007bff;
        color: white;
        padding: 15px 30px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .contact-form button:hover {
        background-color: #0056b3;
        color: white;
    }

    .contact-location {
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .contact-location h2 {
        font-size: 2em;
        color: #333;
        margin-bottom: 15px;
    }

    .contact-location p {
        font-size: 1.2em;
        color: #555;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .contact-us-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<body>
    <div class="contact-us-section">
        <h1>Contact Us</h1>

        <div class="contact-us-container">
            <div class="contact-form-container">
                <p>If you have any questions, concerns, or feedback, we'd love to hear from you. Please reach out to us using the form below or through our contact details.</p>

                <form action="functions/contact_form.php" method="POST" class="contact-form">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>

                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>

                    <button type="submit" class="btn">Send Message</button>
                </form>
            </div>

            <div class="contact-location">
                <h2>Our Location</h2>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.0880332174984!2d101.49119347488501!3d3.071151596904555!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc52f6deda0e3b%3A0x72455bb93ace7b02!2s43%2C%20Jalan%20Pualam%207%2F32%2C%20Seksyen%207%2C%2040000%20Shah%20Alam%2C%20Selangor!5e0!3m2!1sen!2smy!4v1742791446131!5m2!1sen!2smy" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
</body>

</html>
<!-- contact us end -->