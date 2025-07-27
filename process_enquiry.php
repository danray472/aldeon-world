<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "info@aldeonworld.com"; // Aldeon email
    $subject = "New Enquiry from Website";

    $name = strip_tags(trim($_POST["name"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = strip_tags(trim($_POST["phone"] ?? ''));
    $subjectLine = strip_tags(trim($_POST["subject"] ?? ''));
    $message = strip_tags(trim($_POST["message"] ?? ''));

    // Validate required fields
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => implode(", ", $errors)]);
        exit;
    }

    // Build email body
    $body = "New Enquiry from Aldeon World Website:\n\n";
    $body .= "Name: $name\n";
    $body .= "Email: $email\n";
    if (!empty($phone)) $body .= "Phone: $phone\n";
    if (!empty($subjectLine)) $body .= "Subject: $subjectLine\n";
    $body .= "\nMessage:\n$message\n";

    // Set headers
    $headers = "From: Aldeon Website <no-reply@aldeonworld.com>\r\n";
    $headers .= "Reply-To: $name <$email>\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Try to send email
    if (mail($to, $subject, wordwrap($body, 70), $headers)) {
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
    } else {
        error_log("Failed to send email. Headers: " . print_r($headers, true));
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again later.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
