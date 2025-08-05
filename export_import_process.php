<?php
session_start();
require_once 'client_connection.php'; // Adjust path as needed

if (!isset($_SESSION['client'])) {
    header("Location: index.php");
    exit();
}

if (!extension_loaded('zip')) {
    die("Error: ZIP extension is not enabled on this server. Please contact your hosting provider to enable it.");
}

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set higher limits for file uploads and processing
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '55M');
ini_set('max_execution_time', 300); // 5 minutes
ini_set('max_input_time', 300); // 5 minutes

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'export':
                handleExport();
                break;
            case 'import':
                handleImport();
                break;
            default:
                header("Location: export_import.php?error=invalid_action");
                exit();
        }
    }
}

function handleExport()
{
    $dataType = $_POST['export_data'] ?? 'all';
    $format = $_POST['export_format'] ?? 'csv';

    // Validate inputs
    if (
        !in_array($dataType, ['all', 'candidates', 'vacancies']) ||
        !in_array($format, ['csv', 'sql', 'json', 'zip'])
    ) {
        header("Location: export_import.php?error=invalid_parameters");
        exit();
    }

    // For ZIP exports, handle files differently
    if ($format === 'zip') {
        exportWithFiles($dataType);
        exit();
    }

    // Regular exports (CSV, JSON, SQL)
    $filename = "skylink_export_" . $dataType . "_" . date('Y-m-d') . "." . $format;

    // Get data from database
    $data = [];
    switch ($dataType) {
        case 'candidates':
            $result = Database::search("SELECT * FROM candidate");
            while ($row = $result->fetch_assoc()) {
                // Remove full paths, keep only filenames
                $row = sanitizeFilePaths($row, 'candidate');
                $data[] = $row;
            }
            break;
        case 'vacancies':
            $result = Database::search("SELECT * FROM vacancy");
            while ($row = $result->fetch_assoc()) {
                $row = sanitizeFilePaths($row, 'vacancy');
                $data[] = $row;
            }
            break;
        case 'all':
            $result = Database::search("SELECT * FROM candidate");
            while ($row = $result->fetch_assoc()) {
                $row = sanitizeFilePaths($row, 'candidate');
                $data['candidates'][] = $row;
            }
            $result = Database::search("SELECT * FROM vacancy");
            while ($row = $result->fetch_assoc()) {
                $row = sanitizeFilePaths($row, 'vacancy');
                $data['vacancies'][] = $row;
            }
            break;
    }

    // Export based on format
    switch ($format) {
        case 'csv':
            exportCSV($data, $filename);
            break;
        case 'json':
            exportJSON($data, $filename);
            break;
        case 'sql':
            exportSQL($data, $filename);
            break;
    }
}

function sanitizeFilePaths($row, $tableType)
{
    // List of fields that might contain file paths
    $fileFields = [];

    if ($tableType === 'candidate') {
        $fileFields = ['photo_path', 'cv_path', 'passport_path']; // Add your actual file fields
    } elseif ($tableType === 'vacancy') {
        $fileFields = ['attachment_path']; // Add your actual file fields
    }

    foreach ($fileFields as $field) {
        if (isset($row[$field]) && !empty($row[$field])) {
            $row[$field] = basename($row[$field]);
        }
    }

    return $row;
}

function exportCSV($data, $filename)
{
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    if (isset($data['candidates'])) {
        // All data export
        fputcsv($output, ['Candidates Data']);
        if (!empty($data['candidates'])) {
            fputcsv($output, array_keys($data['candidates'][0]));
            foreach ($data['candidates'] as $row) {
                fputcsv($output, $row);
            }
        }

        fputcsv($output, ['']);
        fputcsv($output, ['Vacancies Data']);
        if (!empty($data['vacancies'])) {
            fputcsv($output, array_keys($data['vacancies'][0]));
            foreach ($data['vacancies'] as $row) {
                fputcsv($output, $row);
            }
        }
    } else {
        // Single table export
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
    }

    fclose($output);
    exit();
}

function exportJSON($data, $filename)
{
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit();
}

function exportSQL($data, $filename)
{
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    if (isset($data['candidates'])) {
        // All data export
        echo "-- SkyLink Data Export\n";
        echo "-- Date: " . date('Y-m-d H:i:s') . "\n";
        echo "-- Candidates Data\n\n";

        if (!empty($data['candidates'])) {
            $columns = array_keys($data['candidates'][0]);
            foreach ($data['candidates'] as $row) {
                $values = array_map(function ($value) {
                    return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                }, $row);

                echo "INSERT INTO candidate (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
        }

        echo "\n-- Vacancies Data\n\n";
        if (!empty($data['vacancies'])) {
            $columns = array_keys($data['vacancies'][0]);
            foreach ($data['vacancies'] as $row) {
                $values = array_map(function ($value) {
                    return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                }, $row);

                echo "INSERT INTO vacancy (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
        }
    } else {
        // Single table export
        $table = isset($_POST['export_data']) ? $_POST['export_data'] : 'data';
        echo "-- SkyLink " . ucfirst($table) . " Export\n";
        echo "-- Date: " . date('Y-m-d H:i:s') . "\n\n";

        if (!empty($data)) {
            $columns = array_keys($data[0]);
            foreach ($data as $row) {
                $values = array_map(function ($value) {
                    return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                }, $row);

                echo "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
        }
    }

    exit();
}

function exportWithFiles($dataType)
{
    // Create temporary directory
    $tempDir = sys_get_temp_dir() . '/skylink_export_' . uniqid();
    mkdir($tempDir, 0755, true);
    mkdir($tempDir . '/files', 0755, true);

    // Get data from database
    $data = [];
    $fileFields = []; // Track which fields contain file paths

    if ($dataType === 'candidates' || $dataType === 'all') {
        $result = Database::search("SELECT * FROM candidate");
        $candidateData = [];

        // Get candidate table structure to identify file fields
        $structure = Database::search("DESCRIBE candidate");
        while ($row = $structure->fetch_assoc()) {
            if (preg_match('/path|file|image|pdf|photo|cv|passport/i', $row['Field'])) {
                $fileFields['candidate'][] = $row['Field'];
            }
        }

        while ($row = $result->fetch_assoc()) {
            // Copy files to export directory
            foreach ($fileFields['candidate'] as $field) {
                if (!empty($row[$field])) {
                    $originalPath = '../uploads/candidates/' . basename($row[$field]);
                    $newPath = $tempDir . '/files/' . basename($row[$field]);

                    // Copy the file if it exists
                    if (file_exists($originalPath)) {
                        copy($originalPath, $newPath);
                    }

                    // Update path in data to be relative to export
                    $row[$field] = 'files/' . basename($row[$field]);
                }
            }
            $candidateData[] = $row;
        }

        if ($dataType === 'all') {
            $data['candidates'] = $candidateData;
        } else {
            $data = $candidateData;
        }
    }

    if ($dataType === 'vacancies' || $dataType === 'all') {
        $result = Database::search("SELECT * FROM vacancy");
        $vacancyData = [];

        // Get vacancy table structure to identify file fields
        $structure = Database::search("DESCRIBE vacancy");
        while ($row = $structure->fetch_assoc()) {
            if (preg_match('/path|file|attachment/i', $row['Field'])) {
                $fileFields['vacancy'][] = $row['Field'];
            }
        }

        while ($row = $result->fetch_assoc()) {
            // Copy files to export directory
            foreach ($fileFields['vacancy'] as $field) {
                if (!empty($row[$field])) {
                    $originalPath = '../uploads/vacancies/' . basename($row[$field]);
                    $newPath = $tempDir . '/files/' . basename($row[$field]);

                    // Copy the file if it exists
                    if (file_exists($originalPath)) {
                        copy($originalPath, $newPath);
                    }

                    // Update path in data to be relative to export
                    $row[$field] = 'files/' . basename($row[$field]);
                }
            }
            $vacancyData[] = $row;
        }

        if ($dataType === 'all') {
            $data['vacancies'] = $vacancyData;
        } elseif ($dataType === 'vacancies') {
            $data = $vacancyData;
        }
    }

    // Create data file
    $dataFile = $tempDir . '/data.json';
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

    // Create ZIP archive
    $zipFilename = 'skylink_export_' . $dataType . '_' . date('Y-m-d') . '.zip';
    $zipPath = sys_get_temp_dir() . '/' . $zipFilename;

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        // Add data file
        $zip->addFile($dataFile, 'data.json');

        // Add all files from the files directory
        if (file_exists($tempDir . '/files')) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($tempDir . '/files'),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempDir . '/'));
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        $zip->close();
    } else {
        throw new Exception("Failed to create ZIP file");
    }

    // Send the ZIP file to browser
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
    header('Content-Length: ' . filesize($zipPath));
    readfile($zipPath);

    // Clean up
    unlink($zipPath);
    array_map('unlink', glob("$tempDir/files/*"));
    if (is_dir("$tempDir/files"))
        rmdir("$tempDir/files");
    if (file_exists($dataFile))
        unlink($dataFile);
    if (is_dir($tempDir))
        rmdir($tempDir);

    exit();
}

function handleImport()
{
    if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
        header("Location: exportImport.php?error=upload_failed");
        exit();
    }

    $file = $_FILES['import_file']['tmp_name'];
    $fileName = $_FILES['import_file']['name'];
    $dataType = $_POST['import_data_type'] ?? 'candidates';
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate inputs
    if (!in_array($dataType, ['candidates', 'vacancies'])) {
        header("Location: exportImport.php?error=invalid_parameters");
        exit();
    }

    if (!in_array($extension, ['csv', 'sql', 'json', 'zip'])) {
        header("Location: exportImport.php?error=invalid_format");
        exit();
    }

    try {
        if ($extension === 'zip') {
            importWithFiles($file, $dataType);
        } else {
            // Handle regular imports (CSV, JSON, SQL)
            switch ($extension) {
                case 'csv':
                    importCSV($file, $dataType);
                    break;
                case 'json':
                    importJSON($file, $dataType);
                    break;
                case 'sql':
                    importSQL($file);
                    break;
            }
        }

        header("Location: exportImport.php?success=import_completed");
    } catch (Exception $e) {
        header("Location: exportImport.php?error=import_failed&message=" . urlencode($e->getMessage()));
    }
}

function importWithFiles($zipFile, $dataType)
{
    $tempDir = sys_get_temp_dir() . '/skylink_import_' . uniqid();
    mkdir($tempDir, 0755, true);

    // Extract ZIP file
    $zip = new ZipArchive();
    if ($zip->open($zipFile) !== TRUE) {
        throw new Exception("Failed to open ZIP file");
    }
    $zip->extractTo($tempDir);
    $zip->close();

    // Load data
    $dataFile = $tempDir . '/data.json';
    if (!file_exists($dataFile)) {
        throw new Exception("Data file not found in ZIP");
    }

    $data = json_decode(file_get_contents($dataFile), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid data format in JSON file: " . json_last_error_msg());
    }

    // Determine table and file fields
    $table = $dataType === 'candidates' ? 'candidate' : 'vacancy';
    $fileFields = [];
    $uploadDir = $dataType === 'candidates' ? '../uploads/candidates/' : '../uploads/vacancies/';

    // Get table structure to identify file fields
    $structure = Database::search("DESCRIBE $table");
    while ($row = $structure->fetch_assoc()) {
        if (preg_match('/path|file|image|pdf|photo|cv|passport|attachment/i', $row['Field'])) {
            $fileFields[] = $row['Field'];
        }
    }

    // Begin transaction
    Database::iud("START TRANSACTION");

    try {
        $records = isset($data['candidate']) ? $data['candidate'] : (isset($data['vacancies']) ? $data['vacancies'] : $data);

        foreach ($records as $record) {
            // Copy files to uploads directory
            foreach ($fileFields as $field) {
                if (!empty($record[$field]) && strpos($record[$field], 'files/') === 0) {
                    $relativePath = $record[$field];
                    $sourcePath = $tempDir . '/' . $relativePath;
                    $filename = basename($relativePath);
                    $destPath = $uploadDir . $filename;

                    if (file_exists($sourcePath)) {
                        // Create uploads directory if it doesn't exist
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        // Check if file already exists and create unique name if needed
                        $counter = 1;
                        $originalFilename = $filename;
                        while (file_exists($destPath)) {
                            $fileInfo = pathinfo($originalFilename);
                            $filename = $fileInfo['filename'] . '_' . $counter . '.' . $fileInfo['extension'];
                            $destPath = $uploadDir . $filename;
                            $counter++;
                        }

                        copy($sourcePath, $destPath);

                        // Update path in record to match our system
                        $record[$field] = $filename;
                    } else {
                        // File not found in import, set to NULL
                        $record[$field] = null;
                    }
                }
            }

            // Insert record into database
            $columns = [];
            $values = [];

            foreach ($record as $key => $value) {
                $columns[] = $key;
                $values[] = is_null($value) ? 'NULL' : "'" . Database::$connection->real_escape_string($value) . "'";
            }

            $query = "INSERT INTO $table (" . implode(', ', $columns) . ") 
                      VALUES (" . implode(', ', $values) . ")";
            Database::iud($query);
        }

        Database::iud("COMMIT");
    } catch (Exception $e) {
        Database::iud("ROLLBACK");
        throw $e;
    } finally {
        // Clean up temporary files
        if (file_exists($tempDir . '/files')) {
            array_map('unlink', glob("$tempDir/files/*"));
            rmdir("$tempDir/files");
        }
        if (file_exists($dataFile))
            unlink($dataFile);
        if (is_dir($tempDir))
            rmdir($tempDir);
    }
}

function importCSV($file, $dataType)
{
    $table = $dataType === 'candidates' ? 'candidate' : 'vacancy';

    if (($handle = fopen($file, 'r')) !== FALSE) {
        $columns = fgetcsv($handle); // Get header row

        // Prepare column mapping (you might need to adjust this based on your CSV structure)
        $columnMapping = [];
        foreach ($columns as $col) {
            $columnMapping[$col] = $col; // Simple mapping, adjust as needed
        }

        // Begin transaction for atomicity
        Database::iud("START TRANSACTION");

        try {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $values = [];
                foreach ($columnMapping as $dbColumn => $csvColumn) {
                    $index = array_search($csvColumn, $columns);
                    $value = $index !== FALSE ? $data[$index] : '';
                    $values[$dbColumn] = $value === '' ? 'NULL' : "'" . Database::$connection->real_escape_string($value) . "'";
                }

                $query = "INSERT INTO $table (" . implode(', ', array_keys($values)) . ") 
                          VALUES (" . implode(', ', $values) . ")";
                Database::iud($query);
            }

            Database::iud("COMMIT");
        } catch (Exception $e) {
            Database::iud("ROLLBACK");
            throw $e;
        }

        fclose($handle);
    }
}

function importJSON($file, $dataType)
{
    $table = $dataType === 'candidates' ? 'candidate' : 'vacancy';
    $json = file_get_contents($file);
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON format: " . json_last_error_msg());
    }

    // Begin transaction
    Database::iud("START TRANSACTION");

    try {
        if (isset($data[0])) {
            // Array of records
            foreach ($data as $row) {
                $columns = [];
                $values = [];

                foreach ($row as $key => $value) {
                    $columns[] = $key;
                    $values[] = is_null($value) ? 'NULL' : "'" . Database::$connection->real_escape_string($value) . "'";
                }

                $query = "INSERT INTO $table (" . implode(', ', $columns) . ") 
                          VALUES (" . implode(', ', $values) . ")";
                Database::iud($query);
            }
        }

        Database::iud("COMMIT");
    } catch (Exception $e) {
        Database::iud("ROLLBACK");
        throw $e;
    }
}

function importSQL($file)
{
    $sql = file_get_contents($file);

    // Begin transaction
    Database::iud("START TRANSACTION");

    try {
        // Execute SQL file line by line
        $queries = explode(';', $sql);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                Database::iud($query);
            }
        }

        Database::iud("COMMIT");
    } catch (Exception $e) {
        Database::iud("ROLLBACK");
        throw $e;
    }
}