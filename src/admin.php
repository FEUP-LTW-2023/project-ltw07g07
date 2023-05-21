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

$stmt = $db->prepare("SELECT * FROM user WHERE status = 'Agent' or status = 'Admin'");
$stmt->execute();
$agents = $stmt->fetchAll();

if ($user['status'] != 'Admin'){
  header('Location: login.php');
  exit();
}

$stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id,
                      ticket.priority, assigned_to_user.name as assigned_to_name
                      FROM ticket
                      INNER JOIN user as client ON ticket.client_id = client.id
                      LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id");
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


$stmt = $db->prepare("SELECT name, id FROM department ORDER BY id ASC");
$stmt->execute();
$departments = $stmt->fetchAll();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["department"]) && isset($_POST["sort"]) && isset($_POST["agent"])) {
    $dep = $_POST["department"];
    $option = $_POST["sort"];
    $agent = $_POST["agent"];
    showDepEach($dep, $option, $agent);
  } 
  else if (isset($_POST["idTicket"])) {
    $idTicket = $_POST['idTicket'];
    closeTicket($idTicket);
  }
  else if (isset($_POST["agent"]) && isset($_POST["id_t"])) {
    $agent = $_POST["agent"];
    $id_t = $_POST["id_t"];
    assignAgent($agent, $id_t);
  }
  else if (isset($_POST["id_ttt"]) && isset($_POST["depChange"])) {
    $id_ttt = $_POST['id_ttt'];
    $change = $_POST['depChange'];
    changeDep($id_ttt, $change);
  }
}

function closeTicket($idTicket){
    global $db;
    $stmt = $db->prepare("UPDATE ticket SET status = 'Closed' WHERE id = :ticket_id");
    $stmt->bindParam(':ticket_id', $idTicket);
    $stmt->execute();
    header('Location: admin.php');
    exit();
}

function assignAgent($agent, $id){
  global $db;
  
  $stmt = $db->prepare("SELECT id FROM user WHERE name = :_name");
  $stmt->bindParam(':_name', $agent);
  $stmt->execute();
  $result = $stmt->fetch();

  $stmt = $db->prepare("SELECT status FROM ticket WHERE id = :ticket_id");
  $stmt->bindParam(':ticket_id', $id);
  $stmt->execute();
  $ticket = $stmt->fetch();
  
  if ($result && $ticket) {
    $agent_id = $result['id'];
    $status = $ticket['status'];

    if($status != 'Closed'){
      $stmt = $db->prepare("UPDATE ticket SET assigned_to = :agent_id WHERE id = :ticket_id");
      $stmt->bindParam(':agent_id', $agent_id);
      $stmt->bindParam(':ticket_id', $id);
      $stmt->execute();
      $stmt = $db->prepare("UPDATE ticket SET status = 'Assigned' WHERE id = :ticket_id");
      $stmt->bindParam(':ticket_id', $id);
      $stmt->execute();
    }
  }
  header('Location: admin.php');
  exit();
}



function showDepEach($dep, $option, $agent){
  global $db;
  global $tickets;
  if ($dep == "all"){
    if ($agent == "all"){
      if ($option == "date"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client ON ticket.client_id = client.id
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id");
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
      else if ($option == "status"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client ON ticket.client_id = client.id
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id
        where (ticket.status = 'Open' or ticket.status = 'Assigned')");
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
      else if ($option == "priority"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client ON ticket.client_id = client.id
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id
        ORDER BY CASE WHEN priority = 'High' THEN 0 ELSE 1 END");
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
    }
    else{
      if ($option == "date"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client ON ticket.client_id = client.id
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id
        where assigned_to_user.name = :agent");
        $stmt->bindParam(':agent', $agent);
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
      else if ($option == "status"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client ON ticket.client_id = client.id
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id
        where (ticket.status = 'Open' or ticket.status = 'Assigned') and assigned_to_user.name = :agent");
        $stmt->bindParam(':agent', $agent);
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
      else if ($option == "priority"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client ON ticket.client_id = client.id
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id
        where assigned_to_user.name = :agent
        ORDER BY CASE WHEN priority = 'High' THEN 0 ELSE 1 END");
        $stmt->bindParam(':agent', $agent);
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
    }
  }
  else {
    if ($agent == "all"){
      if ($option == "date"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client ON ticket.client_id = client.id
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id
        where ticket.department = :dep");
        $stmt->bindParam(':dep', $dep);
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
      else if ($option == "status"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client ON ticket.client_id = client.id
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id
        where ticket.department = :dep
        and (ticket.status = 'Open' or ticket.status = 'Assigned')");
        $stmt->bindParam(':dep', $dep);
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
      else if ($option == "priority"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client ON ticket.client_id = client.id
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id
        where ticket.department = :dep
        ORDER BY CASE WHEN priority = 'High' THEN 0 ELSE 1 END");
        $stmt->bindParam(':dep', $dep);
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
    }
    else{
      if ($option == "date"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client on ticket.client_id = client.id 
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id
        where assigned_to_user.name = :agent and ticket.department = :dep");
        $stmt->bindParam(':dep', $dep);
        $stmt->bindParam(':agent', $agent);
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
      else if ($option == "status"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client on ticket.client_id = client.id
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id
        where assigned_to_user.name = :agent  and ticket.department = :dep
        and (ticket.status = 'Open' or ticket.status = 'Assigned')");
        $stmt->bindParam(':dep', $dep);
        $stmt->bindParam(':agent', $agent);
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
      else if ($option == "priority"){
        $stmt = $db->prepare("SELECT client.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
        ,ticket.priority, assigned_to_user.name as assigned_to_name
        FROM ticket
        INNER JOIN user as client on ticket.client_id = client.id
        LEFT JOIN user as assigned_to_user ON ticket.assigned_to = assigned_to_user.id
        where assigned_to_user.name = :agent and ticket.department = :dep
        ORDER BY CASE WHEN priority = 'High' THEN 0 ELSE 1 END");
        $stmt->bindParam(':dep', $dep);
        $stmt->bindParam(':agent', $agent);
        $stmt->execute();
        $tickets = $stmt->fetchAll();
      }
    }
  }
}


function changeDep($id_ttt, $change){
  global $db;
  $stmt = $db->prepare("UPDATE ticket SET department = :new WHERE id = :ticket_id");
  $stmt->bindParam(':new', $change);
  $stmt->bindParam(':ticket_id', $id_ttt);
  $stmt->execute();
  header('Location: admin.php');
  exit();
}

if ($_GET['function'] === 'showDepEach') {
  showDepEach($_GET['dep'], $_GET('option'), $_GET('agent'));
}

if ($_GET['function'] === 'closeTicket') {
  closeTicket($_GET['idTicket']);
}

if ($_GET['function'] === 'assignAgent') {
  assignAgent($_GET['agent'], $_GET['id_t']);
}

if ($_GET['function'] === 'changeDep') {
  assignAgent($_GET['id_ttt'], $_GET['depChange']);
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
        <li class = "third"> Admin Menu </li>
    </a>
    <a href="faq.php">
        <li class="last">FAQ</li>
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

  <label for="sort">Agent:</label>
  <select name="agent">
    <option value = "all"> All </option>
    <?php foreach ($agents as $agent): ?>
          <option value="<?php echo $agent['name']; ?>"> <?php echo $agent['name']; ?> </option>
    <?php endforeach; ?>
  </select>
  
  <label for="sort">Sort by:</label>
  <select id="sort" name="sort">
    <option value="date">Date</option>
    <option value="status">Status</option>
    <option value="priority">Priority</option>
    <option value="hashtag">Hashtag</option>
  </select>
  <input type="submit" value="Submit">
</form>

<section id = "tickets">

<?php foreach ($tickets as $ticket): ?>
  <div id="ticket">
  <h2><?= $ticket['client_name'] ?></h2>
    <p id = "message"><?= $ticket['message'] ?></p>
    <div class = "ticket-info">
      <p>Department: <?= $ticket['dep'] ?></p>
      <p><?= $ticket['priority']. " Priority" ?></p>
      <p>Status: <?= $ticket['status'] ?></p>
      <?php if(!empty($ticket['assigned_to_name'])): ?>
        <p>Assigned to: <?= $ticket['assigned_to_name'] ?></p>
      <?php else: ?>
        <p>Assigned to: None</p>
      <?php endif; ?>
    </div>

    <form action="" method="post" class = "assign">
      <label for="assign">Assign Agent:</label>
      <select name="agent">
        <option value = "none"> None </option>
        <?php foreach ($agents as $agent): ?>
          <option value="<?php echo $agent['name']; ?>"> <?php echo $agent['name']; ?> </option>
        <?php endforeach; ?>
      </select>
      <input type = "hidden" name = "id_t" value = <?= $ticket['ticket_id']?>>
      <input type="submit" name = "assign" value="Assign">
    </form>

    <form action = "" method = "post" class = "change">
      <label for ="change"> Change department: </label>
      <select name = "depChange">
        <option value = "none"> None </option>
        <?php foreach ($departments as $department):?>
          <option value = "<?php echo $department['name']; ?>"> <?php echo $department['name']; ?> </option>
        <?php endforeach; ?>
      </select>
      <input type = "hidden" name = "id_ttt" value = <?= $ticket['ticket_id']?>>
      <input type = "submit" name = "change" value = "Change">
    </form>

    <form action = "" method = "POST" class = "close">
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