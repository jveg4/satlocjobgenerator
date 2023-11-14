<?php
$uploads_dir = 'uploads/';
$downloads_dir = 'downloads/';

function cleanDirectory($dir) {
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . $file;
            if (is_dir($path)) {
                cleanDirectory($path . '/');
                rmdir($path);
            } else {
                unlink($path);
            }
        }
    }
}

cleanDirectory($uploads_dir);
cleanDirectory($downloads_dir);

echo 'Directories cleaned successfully!';
?>
