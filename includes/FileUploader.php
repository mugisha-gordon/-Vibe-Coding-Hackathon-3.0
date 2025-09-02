<?php
class FileUploader {
    private $upload_dir;
    private $allowed_types;
    private $max_size;
    private $error;

    public function __construct($upload_dir = '../uploads/profiles', $max_size = 5242880) { // 5MB
        $this->upload_dir = $upload_dir;
        $this->allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $this->max_size = $max_size;
        $this->error = null;

        // Create upload directory if it doesn't exist
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
    }

    public function upload($file) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $this->error = 'No file uploaded';
            return false;
        }

        // Check file size
        if ($file['size'] > $this->max_size) {
            $this->error = 'File size exceeds limit';
            return false;
        }

        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $this->allowed_types)) {
            $this->error = 'Invalid file type';
            return false;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = $this->upload_dir . '/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        $this->error = 'Failed to move uploaded file';
        return false;
    }

    public function getError() {
        return $this->error;
    }

    public function delete($filename) {
        $filepath = $this->upload_dir . '/' . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
} 