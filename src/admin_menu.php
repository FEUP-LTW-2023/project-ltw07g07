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

$stmt = $db->prepare("SELECT name FROM department ORDER BY id ASC");
$stmt->execute();
$departments = $stmt->fetchAll();

if ($user['status'] != 'Admin'){
  header('Location: login.php');
  exit();
}

$stmt = $db->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $stmt = $db->prepare("INSERT INTO department (name) VALUES (:name)");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
  } 
  
  elseif (isset($_POST['remove'])) {
    $name = $_POST['name'];
    $stmt = $db->prepare("DELETE FROM department WHERE name = :name");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
  }

  elseif (isset($_POST['assign'])) {
    $department = $_POST['department'];
    $name = $_POST['name'];
    $stmt = $db->prepare("UPDATE user SET department = :department WHERE id = :name");
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    header('Location: admin_menu.php');
    exit();
    
  }
  elseif (isset($_POST['upgrade'])) {
    $role = $_POST['role'];
    $name = $_POST['name'];
    $stmt = $db->prepare("UPDATE user SET status = :status WHERE id = :name");
    $stmt->bindParam(':status', $role);
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    header('Location: admin_menu.php');
    exit();
  }
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

<?php foreach ($users as $user): ?>
  <div id = "user_prof">
      <div id = "user">
        <h3><?= $user['name'] ?></h3>
        <p><?= $user['status'] ?></p>
        <p><?= $user['department'] ?></p>
      </div>
      <?php if($user['status']== 'Agent' or $user['status']== 'Admin'){ ?>
      <div id = "assign-agent">
      
        <form action="" method="post">
          <label for="department">Department:</label>
          <select name="department">
            <option value = "none"> None </option>
            <?php foreach ($departments as $department): ?>
              <option value="<?php echo $department['name']; ?>"> <?php echo $department['name']; ?> </option>
            <?php endforeach; ?>
          </select>
          <input type = "hidden" name = "name" value = <?= $user['id']?>>
          <input type="submit" name="assign" value="Assign">
        </form>

      <?php } ?>
      <form action="" method="post">
          <label for="role">Role:</label>
          <select name="role">
            <?php foreach (array('Client', 'Agent', 'Admin') as $role): ?>
              <option value="<?php echo $role; ?>"> <?php echo $role; ?> </option>
            <?php endforeach; ?>
          </select>
          <input type = "hidden" name = "name" value = <?= $user['id']?>>
          <input type="submit" name="upgrade" value="Upgrade">
      </form>

      
      </div>
  </div>
    <?php
  endforeach; ?>

<a href="#" class = "dep-ar" onclick="showFormDep()">Add Department</a>
<form id="myForm" style="display: none;" action="" method="post">
        <label for="name">Department:</label>
        <input type="text" id="name" name="name"><br>
        <input type="submit" name="add" value="Add">
        
</form>

<a href="#" class = "dep-ar" onclick="showFormRem()">Remove Department</a>
<form id="myForm2" style="display: none;" action="" method="post">
        <label for="name">Department:</label>
        <input type="text" id="name" name="name"><br>
        <input type="submit" name="remove" value="Remove">
        
</form>

<footer>
&copy; Trouble Ticket
</footer>
</body>
</html>