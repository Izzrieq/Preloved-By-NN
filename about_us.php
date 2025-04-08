<!-- about us php start -->
<?php
session_start();
include 'components/nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Preloved by NN</title>
    <link rel="stylesheet" href="styles/style.css">
    <style>
        .about-us-section {
            background-color: #f7f7f7;
            text-align: center;
        }

        .how-we-started {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .how-we-started img {
            width: 45%;
            max-width: 100%;
            height: auto;
        }

        .how-we-started .text {
            width: 50%;
            text-align: left;
            padding-left: 20px;
        }

        .how-we-started .text h2 {
            font-size: 2em;
            color: #333;
            margin-bottom: 10px;
        }

        .how-we-started .text p {
            font-size: 1.2em;
            color: #555;
            line-height: 1.6;
        }

        .how-we-expand {
            position: relative;
            width: 100%;
            height: 400px;
        }

        .how-we-expand img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .how-we-expand .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .how-we-expand .text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            text-align: center;
            width: 90%;
            z-index: 2;
        }

        .how-we-expand .text h2 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .how-we-expand .text p {
            font-size: 1.2em;
            line-height: 1.6;
        }

        .full-width-image {
            width: 100%;
            height: auto;
        }

        .video-section {
            margin-top: 20px;
            text-align: center;
        }

        .video-section video {
            width: 100%;
            max-width: 800px;
            height: auto;
        }

        .thank-you {
            font-size: 1.5em;
            color: #333;
            padding: 20px;
            background-color: #222;
            color: #fff;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="about-us-section">
        <h1>About Preloved by NN</h1>

        <div class="video-section">
            <video autoplay muted loop controls>
                <source src="assets/intro.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <div class="how-we-started">
            <img src="assets/logo2.png" alt="How We Started">
            <div class="text">
                <h2>How We Started</h2>
                <p>At Preloved by NN, our journey began with a passion for high-quality, preloved luxury fashion. We wanted to make designer items accessible to everyone while promoting sustainability. From humble beginnings, we have grown into a trusted brand that curates a collection of perfumes, clothing, and accessories from top luxury brands. Every item in our collection has been carefully inspected to ensure it meets our standards of quality, so our customers can shop with confidence.</p>
            </div>
        </div>

        <div class="how-we-expand">
            <img src="assets/business.jpg" alt="How We Expand">
            <div class="overlay"></div>
            <div class="text">
                <h2>How We Expand</h2>
                <p>As we grew, we expanded our offerings and improved our services to meet the demands of our customers. With a focus on providing exceptional value and promoting sustainable fashion, we have built a loyal customer base. Today, we offer a range of luxury items, and our platform has become a destination for fashion enthusiasts looking for both style and sustainability. Our journey is far from over, and we are committed to continuing our growth and enhancing the shopping experience for our customers.</p>
            </div>
        </div>

        <div class="thank-you">
            <p>Thank you, customers, for your continuous support. We couldn't have come this far without you!</p>
        </div>

    </div>
</body>
<?php include 'components/footer.php'; ?>

</html>
<!-- about us php end -->