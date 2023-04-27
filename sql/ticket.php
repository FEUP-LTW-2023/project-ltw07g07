<?php
// start session
session_start();

// connect to the database
require_once('connection.php');
$db = getDataBaseConnection();

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
  // redirect to login page
  header('Location: login.php');
  exit();
}

// retrieve user data from the database
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// check if the form has been submitted
if (isset($_POST['submit_ticket'])) {
  // retrieve form data
  $subject = $_POST['subject'];
  $message = $_POST['message'];
  $department = $_POST['department'];

  // insert ticket data into the database
  $stmt = $db->prepare("INSERT INTO tickets (user_id, subject, message, department) VALUES (:user_id, :subject, :message, :department)");
  $stmt->bindParam(':user_id', $_SESSION['user_id']);
  $stmt->bindParam(':subject', $subject);
  $stmt->bindParam(':message', $message);
  $stmt->bindParam(':department', $department);
  $stmt->execute();

  // redirect to dashboard
  header('Location: dashboard.php');
  exit();
}

// retrieve tickets submitted by the user
$stmt = $db->prepare("SELECT * FROM tickets WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ticket submission form -->
<form action="" method="POST">
  <label for="subject">Subject:</label>
  <input type="text" id="subject" name="subject" required>

  <label for="message">Message:</label>
  <textarea id="message" name="message" rows="5" required></textarea>

  <label for="department">Department:</label>
  <select id="department" name="department">
    <option value="">Select department</option>
    <option value="accounting">Accounting</option>
    <option value="sales">Sales</option>
    <option value="support">Support</option>
  </select>

  <input type="submit" name="submit_ticket" value="Submit">
</form>

<!-- list of submitted tickets -->
<table>
  <thead>
    <tr>
      <th>Subject</th>
      <th>Status</th>
      <th>Department</th>
      <th>Created at</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($tickets as $ticket) { ?>
      <tr>
        <td><?= $ticket['subject'] ?></td>
        <td><?= $ticket['status'] ?></td>
        <td><?= $ticket['department'] ?></td>
        <td><?= $ticket['created_at'] ?></td>
        <td>
          <a href="view_ticket.php?id=<?= $ticket['id'] ?>">View</a>
          <?php if ($ticket['status'] != 'closed') { ?>
            <a href="reply_ticket.php?id=<?= $ticket['id'] ?>">Reply</a>
          <?php } ?>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>