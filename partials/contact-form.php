<?php
// Simple contact form module
// Expects to be included in a page template or partial

$submitted = ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_name'] ?? '') === 'contact');

if ($submitted) {
  // Grab sanitized input
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $message = trim($_POST['message'] ?? '');

  // Show submitted values for now
  echo "<div class='form-submitted'>";
  echo "<h2>Form submitted</h2>";
  echo "<p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>";
  echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
  if ($phone) {
    echo "<p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>";
  }
  echo "<p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";
  echo "</div>";
} else {
  ?>
  <form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" class="contact-form">
    <input type="hidden" name="form_name" value="contact">

    <label>
      Name *
      <input type="text" name="name" required>
    </label>

    <label>
      Email *
      <input type="email" name="email" required>
    </label>

    <label>
      Phone (optional)
      <input type="tel" name="phone" pattern="[\d\s\-\+\(\)]*">
    </label>

    <label>
      Message *
      <textarea name="message" rows="5" required></textarea>
    </label>

    <button type="submit">Send</button>
  </form>
<?php } ?>