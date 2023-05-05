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

  $message = $_POST['message'];
  $department = $_POST['department'];
  $priority = 'Low';
  if ($_POST['high'] == 'on'){
    $priority = 'High';
  }


  $stmt = $db->prepare("INSERT INTO ticket (client_id, department, message, priority) VALUES (:client_id, :department, :message, :priority)");
  $stmt->bindParam(':client_id', $_SESSION['user_id']);
  $stmt->bindParam(':message', $message);
  $stmt->bindParam(':department', $department);
  $stmt->bindParam(':priority', $priority);

  $stmt->execute();

  header('Location: main.php');
  exit();
}

// retrieve tickets submitted by the user
$stmt = $db->prepare("SELECT * FROM ticket WHERE client_id = :client_id");
$stmt->bindParam(':client_id', $_SESSION['client_id']);
$stmt->execute();
$tickets = $stmt->fetchAll();
?>

<!-- ticket submission form -->
<link rel="stylesheet" href="style.css">
<form action="" method="POST">
  <label for="message">Message:</label>
  <textarea id="message" name="message" rows="5" required></textarea>
  <label for="department">Department:</label>
  <select id="department" name="department">
    <option value="">Select department</option>
    <option value="accounting">Accounting</option>
    <option value="sales">Sales</option>
    <option value="support">Support</option>
  </select>
  <br>
  <input type="checkbox" id="high" name="high" value="on">
  <label for="high">High priority</label>
  <br>
  <br>
  <input type="submit" name="submit_ticket" value="Submit">
</form>

<a href="login.php">Logout</a>

<footer>
    Trouble Ticket
  </footer>