<?php
// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the file name is set and is a string
if (isset($_POST['filename']) && is_string($_POST['filename'])) {
    $filename = $_POST['filename'];

    // Check if the file contents are set and is a string
    if (isset($_POST['filecontent']) && is_string($_POST['filecontent'])) {
        $filecontent = $_POST['filecontent'];

        // Set the correct headers for the file download
        header('Content-Type: text/css');
        header('Content-Disposition: attachment; filename="'.$filename.'"');

        // Output the file contents
        echo $filecontent;
        exit;
    } else {
        echo 'Error: file content not set or is not a string';
    }
} else {
    echo 'Error: file name not set or is not a string';
}
var_dump($_POST);