<?php
$social_links = [
    'instagram' => 'https://www.instagram.com/bumbobi_childsupportuganda?igsh=MWV6cnR6bWs0eGtwYQ%3D%3D&utm_source=qr',
    'tiktok' => 'https://www.tiktok.com/@bumbobi_child_support_ug?_t=ZM-8wH1nVtlIes&_r=1',
    'linkedin' => 'https://www.linkedin.com/company/childsupportuganda/',
    'facebook' => 'https://www.facebook.com/share/1HS4nc1kBL/?mibextid=wwXIfr',
    'youtube' => 'https://www.youtube.com/@Bumbobichildsupportuganda',
    'email' => 'info@childsupport-uganda.org',
    'tel' => '+256774586279',
    'twitter' => 'https://x.com/ChildSupportUg1',
    'whatsapp' => '+256774586279'
];
?>

<div class="social-media-links">
    <a href="<?php echo $social_links['instagram']; ?>" target="_blank" class="social-link instagram" title="Follow us on Instagram">
        <i class="fab fa-instagram"></i>
    </a>
    <a href="<?php echo $social_links['tiktok']; ?>" target="_blank" class="social-link tiktok" title="Follow us on TikTok">
        <i class="fab fa-tiktok"></i>
    </a>
    <a href="<?php echo $social_links['linkedin']; ?>" target="_blank" class="social-link linkedin" title="Connect with us on LinkedIn">
        <i class="fab fa-linkedin"></i>
    </a>
    <a href="<?php echo $social_links['facebook']; ?>" target="_blank" class="social-link facebook" title="Like us on Facebook">
        <i class="fab fa-facebook"></i>
    </a>
    <a href="<?php echo $social_links['youtube']; ?>" target="_blank" class="social-link youtube" title="Subscribe to our YouTube">
        <i class="fab fa-youtube"></i>
    </a>
    <a href="mailto:<?php echo $social_links['email']; ?>" class="social-link email" title="Email us">
        <i class="fas fa-envelope"></i>
    </a>
    <a href="tel:<?php echo $social_links['tel']; ?>" class="social-link phone" title="Call us">
        <i class="fas fa-phone"></i>
    </a>
    <a href="<?php echo $social_links['twitter']; ?>" target="_blank" class="social-link twitter" title="Follow us on X (Twitter)">
        <i class="fab fa-x-twitter"></i>
    </a>
    <a href="https://wa.me/<?php echo str_replace('+', '', $social_links['whatsapp']); ?>" target="_blank" class="social-link whatsapp" title="Message us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
</div>

