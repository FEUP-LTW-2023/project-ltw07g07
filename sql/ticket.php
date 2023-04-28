<?php
session_start();

require_once('connection.php');
$db = getDataBaseConnection();

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

// retrieve user data from the database

$stmt = $db->prepare("SELECT * FROM user WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch();

// check if the form has been submitted
if (isset($_POST['submit_ticket'])) {
  // retrieve form data
  $hashtags = $_POST['subject'];
  $message = $_POST['message'];

  // insert ticket data into the database
  $stmt = $db->prepare("INSERT INTO ticket (client_id, hashtags, message) VALUES (:client_id, :hashtags, :message)");
  $stmt->bindParam(':client_id', $_SESSION['client_id']);
  $stmt->bindParam(':hashtags', $hashtags);
  $stmt->bindParam(':message', $message);
  $stmt->execute();

  // redirect to dashboard
  //header('Location: dashboard.php');
  //exit();
}

// retrieve tickets submitted by the user
$stmt = $db->prepare("SELECT * FROM ticket WHERE client_id = :client_id");
$stmt->bindParam(':client_id', $_SESSION['client_id']);
$stmt->execute();
$tickets = $stmt->fetchAll();
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
      <th>Hashtags</th>
      <th>Status</th>
      <th>Department</th>
      <th>Created at</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($tickets as $ticket) { ?>
      <tr>
        <td><?= $ticket['hashtags'] ?></td>
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