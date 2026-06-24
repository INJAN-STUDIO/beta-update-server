<?php
// ================================================================
// BETA OS - Registration API (Hardware-Bound)
// ================================================================

// The JSON file where keys are stored
$DATA_FILE = 'update-data.json';

// Read existing data
$data = [];
if (file_exists($DATA_FILE)) {
    $data = json_decode(file_get_contents($DATA_FILE), true);
    if (!isset($data['keys'])) {
        $data['keys'] = [];
    }
}

// Get POST data
$key = $_POST['key'] ?? '';
$fingerprint = $_POST['fingerprint'] ?? '';
$hostname = $_POST['hostname'] ?? '';
$username = $_POST['username'] ?? '';
$mac = $_POST['mac'] ?? '';
$version = $_POST['version'] ?? '';

// Validate input
if (empty($key) || empty($fingerprint)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing key or fingerprint']);
    exit;
}

// Check if key already exists
if (isset($data['keys'][$key])) {
    // Key exists - check if fingerprint matches
    if ($data['keys'][$key]['fingerprint'] === $fingerprint) {
        echo json_encode(['status' => 'success', 'message' => 'Key already registered to this device']);
    } else {
        echo json_encode(['status' => 'duplicate', 'message' => 'Key already in use on another device']);
    }
} else {
    // New key - register it
    $data['keys'][$key] = [
        'fingerprint' => $fingerprint,
        'hostname' => $hostname,
        'username' => $username,
        'mac' => $mac,
        'version' => $version,
        'registered_at' => date('Y-m-d H:i:s')
    ];
    
    // Save to JSON file
    file_put_contents($DATA_FILE, json_encode($data, JSON_PRETTY_PRINT));
    echo json_encode(['status' => 'success', 'message' => 'Key registered successfully']);
}
?>
