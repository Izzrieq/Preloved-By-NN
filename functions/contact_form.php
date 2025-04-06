<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = htmlspecialchars($_POST['name']);
  $email = htmlspecialchars($_POST['email']);
  $message = htmlspecialchars($_POST['message']);


  $to = 'izzrieqilhan@gmail.com';
  $subject = 'New Contact Us Message from ' . $name;


  $body = "<h2>Contact Us Form Submission</h2>";
  $body .= "<p><strong>Name:</strong> $name</p>";
  $body .= "<p><strong>Email:</strong> $email</p>";
  $body .= "<p><strong>Message:</strong><br>$message</p>";


  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-Type:text/html;charset=UTF-8" . "\r\n";
  $headers .= "From: $email" . "\r\n";


  if (mail($to, $subject, $body, $headers)) {
    echo "<script>
                alert('Your message has been sent successfully.');
                window.location.href = '../contact_us.php';
              </script>";
  } else {
    echo "<script>
                alert('Sorry, there was an issue sending your message. Please try again later.');
                window.location.href = '../contact_us.php';
              </script>";
  }
}
