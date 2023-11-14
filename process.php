<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function extract_coordinates($coordinates_element) {
    $coordinates = $coordinates_element->nodeValue;
    $coords_list = explode(' ', $coordinates);
    $result = [];

    foreach ($coords_list as $coord) {
        $coords = explode(',', $coord);
        if (count($coords) >= 2) {
            $result[] = [floatval($coords[1]), floatval($coords[0])];
        }
    }
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['kmlFile']) && isset($_POST['jobNumber'])) {
    $alert = ''; // Initialize alert message

    try {
        $uploaded_file = $_FILES['kmlFile'];
        $selected_job_number = filter_var($_POST['jobNumber'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $upload_dir = __DIR__ . '/uploads/';
        $download_dir = __DIR__ . '/downloads/';

        if (!file_exists($upload_dir) || !is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true) && !is_dir($upload_dir)) {
                throw new \RuntimeException('Upload directory creation failed');
            }
        }

        if (!file_exists($download_dir) || !is_dir($download_dir)) {
            if (!mkdir($download_dir, 0777, true) && !is_dir($download_dir)) {
                throw new \RuntimeException('Download directory creation failed');
            }
        }

        $unique_folder_id = uniqid();
        $unique_filename = uniqid() . '.' . pathinfo($uploaded_file['name'], PATHINFO_EXTENSION);
        $kml_dest_path = $upload_dir . $unique_filename;

        if (move_uploaded_file($uploaded_file['tmp_name'], $kml_dest_path)) {
            $dom = new DOMDocument();
            if ($dom->load($kml_dest_path)) {
                $satloc_job = [".JOB " . $selected_job_number, ".VERSION 2", ""];

                $placemarks = $dom->getElementsByTagName('Placemark');
                $polygon_number = 1; // Initialize with 1

                foreach ($placemarks as $placemark) {
                    $coordinates_element = $placemark->getElementsByTagName('coordinates')->item(0);
                    if ($coordinates_element) {
                        $coords_list = extract_coordinates($coordinates_element);
                        if (!empty($coords_list)) {
                            $satloc_job[] = ".POL " . $polygon_number . ' ' . $polygon_number . '.1';
                            $satloc_job[] = "\tINC";

                            foreach ($coords_list as $coords) {
                                list($lat, $lon) = $coords;
                                $formatted_lat = number_format($lat, 6);
                                $formatted_lon = number_format($lon, 6);
                                $satloc_job[] = "\t$formatted_lat $formatted_lon";
                            }

                            $polygon_number++;
                        }
                    }
                }
                
                $job_content = implode("\n", $satloc_job);

                $job_folder = $download_dir . $unique_folder_id . '/';
                if (!file_exists($job_folder) || !is_dir($job_folder)) {
                    if (!mkdir($job_folder, 0777, true) && !is_dir($job_folder)) {
                        throw new \RuntimeException('Job folder creation failed');
                    }
                }

                $job_file_path = $job_folder . $selected_job_number . '.job';
                file_put_contents($job_file_path, $job_content);

                $encoded_filename = rawurlencode($selected_job_number . '.job');
                $encoded_filename = str_replace('%2E', '.', $encoded_filename);
                header("Content-Disposition: attachment; filename*=UTF-8''$encoded_filename");
                header("Content-Type: application/octet-stream");
                header("Content-Length: " . filesize($job_file_path));
                header("Cache-Control: no-cache, must-revalidate");
                header("Expires: 0");
                header("Pragma: public");

                $file_url = "download.php?folder_id=" . urlencode($unique_folder_id) . "&job_file_name=" . urlencode($selected_job_number . '.job');

                $alert = "<a href='$file_url' download>Download SATLOC Job File</a>";
            } else {
                $alert = "Failed to process the uploaded file. Please make sure it's a valid KML file.";
            }
        } else {
            $alert = "Failed to move the uploaded file. Please try again.";
        }

        echo $alert;
    } catch (Exception $e) {
        error_log("An error occurred: " . $e->getMessage());
        echo "An error occurred. Please try again later.";
    }
}
?>
