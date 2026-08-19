<?php
function createIcon($size, $filename) {
    $img = imagecreatetruecolor($size, $size);
    // Green theme color
    $bg = imagecolorallocate($img, 22, 163, 74); 
    $text_color = imagecolorallocate($img, 255, 255, 255);
    imagefilledrectangle($img, 0, 0, $size, $size, $bg);
    
    $text = "AGRI";
    $font_size = 5; // Built in font 5
    $x = $size / 2 - (imagefontwidth($font_size) * strlen($text)) / 2;
    $y = $size / 2 - imagefontheight($font_size) / 2;
    
    imagestring($img, $font_size, $x, $y, $text, $text_color); 
    imagepng($img, $filename);
    imagedestroy($img);
}

$base = "c:\\xampp\\htdocs\\AGRI PRO\\public\\assets\\images\\icons";
createIcon(192, $base . '\\icon-192x192.png');
createIcon(512, $base . '\\icon-512x512.png');
echo "Icons generated successfully.\n";
