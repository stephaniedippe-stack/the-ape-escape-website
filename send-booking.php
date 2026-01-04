<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Get POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $guest_name = $data['guest_name'] ?? '';
    $arrival_date = $data['arrival_date'] ?? '';
    $departure_date = $data['departure_date'] ?? '';
    $adults = $data['adults'] ?? 0;
    $children = $data['children'] ?? 0;
    $message = $data['message'] ?? '';
    $cc_email = $data['cc_email'] ?? 'info@eastafricanretreats.com';
    
    // Calculate total guests
    $total_guests = intval($adults) + intval($children);
    
    // Email content
    $email_body = "
BOOKING INQUIRY - THE APE ESCAPE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

GUEST INFORMATION
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Name:             $guest_name
Arrival Date:     $arrival_date
Departure Date:   $departure_date
Adults:           $adults
Children:         $children
Total Guests:     $total_guests

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

MESSAGE / SPECIAL REQUESTS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

" . ($message ?: 'No special requests') . "

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

BOOKING DETAILS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Property:         The Ape Escape
Agency:           East African Retreats
Submitted:        " . gmdate('D, d M Y H:i:s') . " GMT
Source:           East African Retreats Booking Portal

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

AGENCY CONTACT INFORMATION
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Agency:           East African Retreats
Email:            info@eastafricanretreats.com
Phone:            +254 724 529 421

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
";

    // Email headers with SMTP authentication
    $to = 'stay@the-ape-escape.com';
    $subject = 'New Booking Inquiry - East African Retreats - The Ape Escape';
    
    // Headers for proper email formatting
    $headers = "From: The Ape Escape <stay@the-ape-escape.com>\r\n";
    $headers .= "Reply-To: stay@the-ape-escape.com\r\n";
    $headers .= "Cc: $cc_email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Send email using PHP mail() - Namecheap handles SMTP
    $success = mail($to, $subject, $email_body, $headers);
    
    if ($success) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Booking inquiry sent successfully!'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to send email'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>