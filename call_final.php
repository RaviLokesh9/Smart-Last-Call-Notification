<?php
// Call the Python script using exec or shell_exec

$command = '/usr/bin/python3 /Applications/XAMPP/xamppfiles/htdocs/my_project/democall.py';
$output = shell_exec($command);

if ($output === null) {
    echo "Failed to make final call.";
} else {
    echo "Final call initiated successfully.";
}
?>
