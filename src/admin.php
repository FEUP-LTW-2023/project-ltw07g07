<?php
session_start();
require_once('connection.php');
$db = getDataBaseConnection();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $db->prepare("SELECT * FROM user WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch();

if ($user['status'] != 'Admin'){
  header('Location: login.php');
  exit();
}

$stmt = $db->prepare("SELECT user.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id,
                      ticket.priority
                      FROM ticket
                      JOIN user ON ticket.client_id = user.id");
$stmt->execute();
$tickets = $stmt->fetchAll();


if (isset($_POST['reply'])) {

  $message = $_POST['message'];
  $ticket_id = $_POST['ticket_id'];

  $stmt = $db->prepare("INSERT INTO reply (client_id, message, ticket_id) VALUES (:client_id, :message,:ticket_id)");
  $stmt->bindParam(':client_id', $_SESSION['user_id']);
  $stmt->bindParam(':message', $message);
  $stmt->bindParam(':ticket_id', $ticket_id);

  $stmt->execute();

}


$stmt = $db->prepare("SELECT message, ticket_id, name FROM reply, user
                      WHERE user.id = client_id
                       ORDER BY reply.id ASC");
$stmt->execute();
$replies = $stmt->fetchAll();


$stmt = $db->prepare("SELECT name FROM department ORDER BY id ASC");
$stmt->execute();
$departments = $stmt->fetchAll();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["department"]) && isset($_POST["sort"])) {
    $dep = $_POST["department"];
    $option = $_POST["sort"];
    showDepEach($dep, $option);
  } else if (isset($_POST["idTicket"])) {
    $idTicket = $_POST['idTicket'];
    closeTicket($idTicket);
  }
}



function closeTicket($idTicket){
    global $db;
    $stmt = $db->prepare("UPDATE ticket SET status = 'Closed' WHERE id = :ticket_id");
    $stmt->bindParam(':ticket_id', $idTicket);
    $stmt->execute();
}

function showDepEach($dep, $option){
  global $db;
  global $tickets;
  if ($dep == "all"){
    if ($option == "date"){
      $stmt = $db->prepare("SELECT user.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
      ,ticket.priority
      FROM ticket
      JOIN user where ticket.client_id = user.id");
      $stmt->execute();
      $tickets = $stmt->fetchAll();
    }
    else if ($option == "status"){
      $stmt = $db->prepare("SELECT user.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
      ,ticket.priority
      FROM ticket
      JOIN user where ticket.client_id = user.id
      ORDER BY CASE WHEN ticket.status = 'Open' THEN 0 ELSE 1 END");
      $stmt->execute();
      $tickets = $stmt->fetchAll();
    }
    else if ($option == "priority"){
      $stmt = $db->prepare("SELECT user.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
      ,ticket.priority
      FROM ticket
      JOIN user where ticket.client_id = user.id
      ORDER BY CASE WHEN priority = 'High' THEN 0 ELSE 1 END");
      $stmt->execute();
      $tickets = $stmt->fetchAll();
    }
  }
  
  else {
    if ($option == "date"){
      $stmt = $db->prepare("SELECT user.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
      ,ticket.priority
      FROM ticket
      JOIN user where ticket.client_id = user.id and ticket.department = :dep");
      $stmt->bindParam(':dep', $dep);
      $stmt->execute();
      $tickets = $stmt->fetchAll();
    }
    else if ($option == "status"){
      $stmt = $db->prepare("SELECT user.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
      ,ticket.priority
      FROM ticket
      JOIN user where ticket.client_id = user.id and ticket.department = :dep
      ORDER BY CASE WHEN ticket.status = 'Open' THEN 0 ELSE 1 END");
      $stmt->bindParam(':dep', $dep);
      $stmt->execute();
      $tickets = $stmt->fetchAll();
    }
    else if ($option == "priority"){
      $stmt = $db->prepare("SELECT user.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
      ,ticket.priority
      FROM ticket
      JOIN user where ticket.client_id = user.id and ticket.department = :dep
      ORDER BY CASE WHEN priority = 'High' THEN 0 ELSE 1 END");
      $stmt->bindParam(':dep', $dep);
      $stmt->execute();
      $tickets = $stmt->fetchAll();
    }
  }
}

if ($_GET['function'] === 'showDepEach') {
  showDepEach($_GET['dep'], $_GET($option));
}


if ($_GET['function'] === 'closeTicket') {
  closeTicket($_GET['idTicket']);
}

?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>Trouble Ticket Handler</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style2.css">

    <script src = "script.js"></script>
</head>

<body>
<header>
    <h1><a href="admin.php">Trouble Ticket Handler - Admin</a></h1>
</header>

<a href="login.php" class="a-prof">Logout</a>

<ul id="menu">
    <a href="ticket.php">
        <li class="first">Create new Ticket</li>
    </a>
    <a href="profile.php">
        <li class="second"><?= $user['name'] ?></li>
    </a>
    <a href="admin_menu.php">
        <li class = "last"> Admin Menu </li>
    </a>
</ul>




<form action="" method="post">
<label for="sort">Department:</label>
  <select name="department">
  <option value = "all"> All </option>
  <?php foreach ($departments as $deparment): ?>
    <option value="<?php echo $deparment['name']; ?>"> <?php echo $deparment['name']; ?> </option>
    <?php endforeach; ?>
  </select>
  
  <label for="sort">Sort by:</label>
  <select id="sort" name="sort">
    <option value="date">Date</option>
    <option value="status">Status</option>
    <option value="priority">Priority</option>
    <option value="agent">Assigned Agent</option>
    <option value="hashtag">Hashtag</option>
  </select>
  <input type="submit" value="Submit">
</form>



<section id = "tickets">

<?php foreach ($tickets as $ticket): ?>
  <div id="ticket">
    <h2><?= $ticket['client_name'] ?></h2>
    <p><?= $ticket['message'] ?></p>
    <p><?= $ticket['dep'] ?></p>
    <p><?= $ticket['priority']. " Priority" ?></p>
    <p><?= $ticket['status'] ?></p>
    <form action = "" method = "POST">
      <input type = "hidden" name = "idTicket" value = <?= $ticket['ticket_id'] ?>>
      <input type = "submit" value = "Close ticket">
    </form>
  </div>


  <?php foreach ($replies as $reply):
    if ($reply['ticket_id'] == $ticket['ticket_id']): ?>
      <div id = "reply">
        <h3><?= $reply['name'] ?></h3>
        <p><?= $reply['message'] ?></p>
      </div>
    <?php endif;
  endforeach; ?>


  <a href="#" class="reply-ticket" onclick="showFormReply(<?= $ticket['ticket_id'] ?>)">Reply</a>
  <form id="reply-<?= $ticket['ticket_id'] ?>" style="display: none;" action="" method="post">
    <label for="message">Message:</label>
    <textarea id="message" name="message" rows="5" required></textarea>
    <input type="hidden" name="ticket_id" value="<?= $ticket['ticket_id'] ?>">
    <input type="submit" name="reply" value="Reply">
  </form>

<?php endforeach; ?>

  <div id = "agent" style = "display:none;">
    <p> AGENTE </p>
  </div>

</section>


<footer>
&copy; Trouble Ticket
</footer>
</body>
</html>