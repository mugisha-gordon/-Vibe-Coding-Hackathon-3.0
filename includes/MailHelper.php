<?php
require_once __DIR__ . '/../config/mail_config.php';

class MailHelper {
    public function sendVolunteerNotification($volunteerData) {
        $to = ADMIN_EMAIL;
        $subject = 'New Volunteer Request';
        
        // Create message
        $message = "A new volunteer request has been submitted:\n\n";
        $message .= "Name: " . $volunteerData['first_name'] . " " . $volunteerData['last_name'] . "\n";
        $message .= "Email: " . $volunteerData['email'] . "\n";
        $message .= "Phone: " . $volunteerData['phone'] . "\n";
        $message .= "Skills: " . $volunteerData['skills'] . "\n";
        $message .= "Availability: " . $volunteerData['availability'] . "\n";
        $message .= "Areas of Interest: " . $volunteerData['areas_of_interest'] . "\n";
        
        // Set headers
        $headers = "From: " . $volunteerData['email'] . "\r\n";
        $headers .= "Reply-To: " . $volunteerData['email'] . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Send email
        return mail($to, $subject, $message, $headers);
    }

    public function sendVolunteerConfirmation($volunteerData) {
        $to = $volunteerData['email'];
        $subject = 'Volunteer Application Received';
        
        // Create message
        $message = "Dear " . $volunteerData['first_name'] . ",\n\n";
        $message .= "Thank you for your interest in volunteering with " . SITE_NAME . ".\n\n";
        $message .= "We have received your application and will review it shortly. Here's a summary of your application:\n\n";
        $message .= "Name: " . $volunteerData['first_name'] . " " . $volunteerData['last_name'] . "\n";
        $message .= "Email: " . $volunteerData['email'] . "\n";
        $message .= "Phone: " . $volunteerData['phone'] . "\n";
        $message .= "Skills: " . $volunteerData['skills'] . "\n";
        $message .= "Availability: " . $volunteerData['availability'] . "\n";
        $message .= "Areas of Interest: " . $volunteerData['areas_of_interest'] . "\n\n";
        $message .= "If you have any questions, please don't hesitate to contact us.\n\n";
        $message .= "Best regards,\n" . SITE_NAME . " Team";
        
        // Set headers
        $headers = "From: " . ADMIN_EMAIL . "\r\n";
        $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Send email
        return mail($to, $subject, $message, $headers);
    }
}
?> 