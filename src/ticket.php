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

  if ($user['status'] == 'Agent'){
    header('Location: agent.php');
  }
  else if ($user['status'] == 'Admin'){
    header('Location: admin.php');
  }
  else header('Location: main.php');
  exit();
}


$stmt = $db->prepare("SELECT name FROM department ORDER BY id ASC");
$stmt->execute();
$departments = $stmt->fetchAll();


// retrieve tickets submitted by the user
$stmt = $db->prepare("SELECT * FROM ticket WHERE client_id = :client_id");
$stmt->bindParam(':client_id', $_SESSION['client_id']);
$stmt->execute();
$tickets = $stmt->fetchAll();
?>

<!-- ticket submission form -->
<!DOCTYPE html>
<html lang="en-US">
  <head>
    <title> Ticket </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style2.css">
  </head>

  <header>
    <?php  if ($user['status'] == 'Admin'): ?>
        <h1><a href="admin.php">Trouble Ticket Handler</a></h1>
    <?php  elseif ($user['status'] == 'Agent'): ?>
        <h1><a href="agent.php">Trouble Ticket Handler</a></h1>
    <?php else: ?>
        <h1><a href="main.php">Trouble Ticket Handler</a></h1>
    <?php endif; ?>
  </header>

  <form action="" method="POST">
    <label for="message">Message:</label>
    <textarea id="message" name="message" rows="5" required></textarea>
    <label for="department">Department:</label>
    <select id="department" name="department">
      <option value="">Select department</option>
      <?php foreach ($departments as $deparment): ?>
        <option><?php echo $deparment['name']; ?>
      <?php endforeach; ?>
    </select>
    <br>
    
    <input type="checkbox" id="high" class = "priority" name="high" value="on">
    <label for="high" class = "priority">High priority</label>
  
    <br>
    <br>
    <input type="submit" name="submit_ticket" value="Submit">
  </form>


  <footer>
  &copy; Trouble Ticket
  </footer>
</html>