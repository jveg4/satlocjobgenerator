<?php
if(isset($_GET['folder_id']) && isset($_GET['job_file_name'])) {
    $folder_id = $_GET['folder_id'];
    $job_file_name = $_GET['job_file_name'];

    // Validate folder_id and job_file_name to prevent directory traversal
    $folder_id = preg_replace('/[^a-zA-Z0-9_]/', '', $folder_id);
    $job_file_name = preg_replace('/[^a-zA-Z0-9_.]/', '', $job_file_name);

    $job_file_path = "downloads/$folder_id/$job_file_name";

    if(file_exists($job_file_path)) {
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$job_file_name\"");
        header("Content-Length: " . filesize($job_file_path));
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: 0");
        header("Pragma: public");

        readfile($job_file_path);
        exit;
    } else {
        echo "File not found.";
    }
} else {
    echo "Invalid request.";
}
?>
