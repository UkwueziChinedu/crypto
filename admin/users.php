<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users
$sql = "SELECT id, username, email, created_at FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registered Users Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css" />
    <style>
        body {
            background-color: #0b0f14;
            color: #ebebeb;
        }

        .card {
            background-color: #1a1a1a;
            border: none;
            color: #ebebeb;
        }

        .table th,
        .table td {
            vertical-align: middle;
            border-color: #333;
        }

        .table thead th {
            background-color: #2a2a2a;
            color: #bdbdbd;
        }

        .modal-content {
            animation: zoomIn 0.3s ease;
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-3xl font-bold">Registered Users</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crudModal" id="add-user-btn">
                Add User
            </button>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Registered At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['id']); ?></td>
                                        <td><?= htmlspecialchars($row['username']); ?></td>
                                        <td><?= htmlspecialchars($row['email']); ?></td>
                                        <td><?= htmlspecialchars($row['created_at']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info edit-btn"
                                                data-id="<?= $row['id']; ?>"
                                                data-username="<?= htmlspecialchars($row['username']); ?>"
                                                data-email="<?= htmlspecialchars($row['email']); ?>">Edit</button>
                                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id']; ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No users found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CRUD Modal -->
    <div class="modal fade" id="crudModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header">
                    <h5 class="modal-title" id="crudModalLabel">Manage User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="crudForm">
                        <input type="hidden" id="user-id" name="id">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="username-input" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email-input" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="save-changes-btn">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let deleteId = null;

            // Add new user
            $('#add-user-btn').on('click', function() {
                $('#crudModalLabel').text('Add New User');
                $('#user-id').val('');
                $('#username-input').val('');
                $('#email-input').val('');
                $('#status-input').val('active');
                $('#save-changes-btn').text('Add');
            });

            // Edit
            $('.edit-btn').on('click', function() {
                $('#crudModalLabel').text('Edit User');
                $('#user-id').val($(this).data('id'));
                $('#username-input').val($(this).data('username'));
                $('#email-input').val($(this).data('email'));
                $('#status-input').val($(this).data('status'));
                $('#save-changes-btn').text('Update');
                new bootstrap.Modal(document.getElementById('crudModal')).show();
            });

            // Delete
            $('.delete-btn').on('click', function() {
                deleteId = $(this).data('id');
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });

            $('#confirm-delete-btn').on('click', function() {
                if (deleteId) {
                    $.post('api.php', {
                        id: deleteId,
                        action: 'delete_user'
                    }, function() {
                        location.reload();
                    });
                }
            });

            // Form submission
            $('#crudForm').on('submit', function(e) {
                e.preventDefault();
                const action = $('#save-changes-btn').text() === 'Update' ? 'update_user' : 'add_user';
                const formData = $(this).serialize() + '&action=' + action;
                $.post('api.php', formData, function() {
                    location.reload();
                });
            });
        });
    </script>

    <?php $conn->close(); ?>