<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

// Fetch all users for recipient selection
$sql = "SELECT id, username, email FROM users ORDER BY username ASC";
$result = $conn->query($sql);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Compose Email</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f5f6fa; }
    .compose-card {
      max-width: 700px;
      margin: 40px auto;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      border-radius: 12px;
      background: #fff;
      padding: 32px;
    }
    .form-control, .form-select { border-radius: 8px; }
    .compose-header { font-size: 1.5rem; font-weight: 600; margin-bottom: 24px; }
    .btn-send { background: #0b57d0; color: #fff; font-weight: 500; }
    .btn-send:hover { background: #0946a7; }
    .form-label { font-weight: 500; }
    .alert { margin-top: 20px; }
  </style>
</head>
<body>
  <div class="compose-card">
    <div class="compose-header">New Message</div>
    <form id="emailForm" method="post" action="send_email.php">
      <div class="mb-3">
        <label class="form-label">To</label>
        <select class="form-select" name="recipient" required>
          <option value="">Select recipient...</option>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <option value="<?= htmlspecialchars($row['email']); ?>">
                <?= htmlspecialchars($row['username']); ?> (<?= htmlspecialchars($row['email']); ?>)
              </option>
            <?php endwhile; ?>
          <?php endif; ?>
          <option value="other">Other email...</option>
        </select>
        <input type="email" class="form-control mt-2" name="other_email" id="otherEmailInput" placeholder="Enter email address" style="display:none;">
      </div>
      <div class="mb-3">
        <label class="form-label">Subject</label>
        <input type="text" class="form-control" name="subject" required maxlength="120">
      </div>
      <div class="mb-3">
        <label class="form-label">Message</label>
        <textarea class="form-control" name="message" rows="8" required style="resize:vertical;"></textarea>
      </div>
      <button type="submit" class="btn btn-send w-100">Send</button>
    </form>
    <div id="alertBox"></div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Show/hide other email input
    $('select[name="recipient"]').on('change', function() {
      if ($(this).val() === 'other') {
        $('#otherEmailInput').show().attr('required', true);
      } else {
        $('#otherEmailInput').hide().val('').removeAttr('required');
      }
    });

    // AJAX form submission (optional, for instant feedback)
    $('#emailForm').on('submit', function(e) {
      e.preventDefault();
      $.post('send_email.php', $(this).serialize(), function(response) {
        $('#alertBox').html(response);
        $('#emailForm')[0].reset();
        $('#otherEmailInput').hide();
      });
    });
  </script>
</body>
</html>
<?php $conn->close(); ?>