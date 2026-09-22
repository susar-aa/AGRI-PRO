<?php
$files = [
    'app/Views/members/directory.php',
    'app/Views/directors/directory.php',
    'app/Views/members/view.php',
    'app/Views/directors/view.php'
];

foreach ($files as $f) {
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    
    // Only replace variables like $m['field'] or $member['field'] inside htmlspecialchars if they don't already have ??
    $c = preg_replace('/htmlspecialchars\(\s*(\$[a-zA-Z0-9_]+\[\'[a-zA-Z0-9_]+\'\])\s*\)/', 'htmlspecialchars($1 ?? \'\')', $c);
    
    file_put_contents($f, $c);
    echo "Processed $f\n";
}
