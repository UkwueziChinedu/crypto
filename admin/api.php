<?php
// api.php
// This script serves as the API endpoint for handling CRUD operations
// on the wallet_imports table. It responds to AJAX requests from the
// admin dashboard.

// Start the session to manage user state.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security check: Ensure an admin user is logged in.
// A more robust system would check for specific user roles.
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit;
}

// Ensure the request is a POST request.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed.']);
    exit;
}

// Include the database connection file.
require_once '../config/db.php';

// Sanitize inputs
$action = $_POST['action'] ?? '';
$id = intval($_POST['id'] ?? 0);
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$wallet_name = htmlspecialchars($_POST['wallet_name'] ?? '');

// Set the response header to JSON.
header('Content-Type: application/json');

switch ($action) {
    case 'add':
        // Add a new wallet import record.
        $sql = "INSERT INTO wallet_imports (email, wallet_name) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ss', $email, $wallet_name);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Import added successfully.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to add import.']);
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'SQL error: ' . $conn->error]);
        }
        break;

    case 'update':
        // Update an existing wallet import record.
        if ($id === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid ID for update.']);
            break;
        }
        $sql = "UPDATE wallet_imports SET email = ?, wallet_name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ssi', $email, $wallet_name, $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Import updated successfully.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update import.']);
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'SQL error: ' . $conn->error]);
        }
        break;

    case 'delete':
        // Delete a wallet import record.
        if ($id === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid ID for deletion.']);
            break;
        }
        $sql = "DELETE FROM wallet_imports WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Import deleted successfully.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to delete import.']);
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'SQL error: ' . $conn->error]);
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action specified.']);
        break;
}

$conn->close();
?>
