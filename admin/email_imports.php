<?php
// email_imports.php
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

// Fetch all data from wallet_imports
$sql = "SELECT id, user_id, email, wallet_name, method, input, transaction_id 
        FROM wallet_imports ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Email Imports Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css" />
  <style>
    body { background-color: #0b0f14; color: #ebebeb; }
    .card { background-color: #1a1a1a; border: none; color: #ebebeb; }
    .table th, .table td { vertical-align: middle; border-color: #333; }
    .table thead th { background-color: #2a2a2a; color: #bdbdbd; }
    .modal-content { animation: zoomIn 0.3s ease; }
    @keyframes zoomIn {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body>
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-3xl font-bold">Wallet Email Imports</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crudModal" id="add-import-btn">
      Add Import
    </button>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
          <tr>
            <th>#</th>
            <th>User ID</th>
            <th>Email</th>
            <th>Wallet Name</th>
            <th>Method</th>
            <th>Input</th>
            <th>Transaction ID</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']); ?></td>
                <td><?= htmlspecialchars($row['user_id']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['wallet_name']); ?></td>
                <td><?= htmlspecialchars($row['method']); ?></td>
                <td><?= htmlspecialchars($row['input']); ?></td>
                <td><?= htmlspecialchars($row['transaction_id']); ?></td>
                <td>
                  <button class="btn btn-sm btn-info edit-btn"
                          data-id="<?= $row['id']; ?>"
                          data-user="<?= htmlspecialchars($row['user_id']); ?>"
                          data-email="<?= htmlspecialchars($row['email']); ?>"
                          data-wallet="<?= htmlspecialchars($row['wallet_name']); ?>"
                          data-method="<?= htmlspecialchars($row['method']); ?>"
                          data-input="<?= htmlspecialchars($row['input']); ?>"
                          data-tx="<?= htmlspecialchars($row['transaction_id']); ?>">Edit</button>
                  <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id']; ?>">Delete</button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="8" class="text-center">No wallet imports found.</td></tr>
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
        <h5 class="modal-title" id="crudModalLabel">Manage Import</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="crudForm">
          <input type="hidden" id="import-id" name="id">
          <div class="mb-3">
            <label class="form-label">User ID</label>
            <input type="number" class="form-control" id="user-input" name="user_id" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" id="email-input" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Wallet Name</label>
            <input type="text" class="form-control" id="wallet-input" name="wallet_name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Method</label>
            <input type="text" class="form-control" id="method-input" name="method" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Input</label>
            <input type="text" class="form-control" id="input-input" name="input" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Transaction ID</label>
            <input type="text" class="form-control" id="tx-input" name="transaction_id" readonly>
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
        Are you sure you want to delete this import?
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
function generateTransactionId() {
  const ts = Date.now().toString(36);
  const rand = Math.random().toString(36).substring(2, 8).toUpperCase();
  return "TX-" + ts + "-" + rand;
}

$(document).ready(function() {
  let deleteId = null;

  // Add new import
  $('#add-import-btn').on('click', function() {
    $('#crudModalLabel').text('Add New Import');
    $('#import-id').val('');
    $('#user-input').val('');
    $('#email-input').val('');
    $('#wallet-input').val('');
    $('#method-input').val('');
    $('#input-input').val('');
    $('#tx-input').val(generateTransactionId());
    $('#save-changes-btn').text('Add');
  });

  // Edit
  $('.edit-btn').on('click', function() {
    $('#crudModalLabel').text('Edit Import');
    $('#import-id').val($(this).data('id'));
    $('#user-input').val($(this).data('user'));
    $('#email-input').val($(this).data('email'));
    $('#wallet-input').val($(this).data('wallet'));
    $('#method-input').val($(this).data('method'));
    $('#input-input').val($(this).data('input'));
    $('#tx-input').val($(this).data('tx'));
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
      $.post('api.php', { id: deleteId, action: 'delete' }, function() {
        location.reload();
      });
    }
  });

  // Form submission
  $('#crudForm').on('submit', function(e) {
    e.preventDefault();
    const action = $('#save-changes-btn').text() === 'Update' ? 'update' : 'add';
    const formData = $(this).serialize() + '&action=' + action;
    $.post('api.php', formData, function() {
      location.reload();
    });
  });
});
</script>

<?php $conn->close(); ?>
</body>
</html>
