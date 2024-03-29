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

if (isset($_POST['submit_faq'])) {
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $title = $_POST['title'];

    $stmt = $db->prepare("INSERT INTO faq (title, question, answer) VALUES (:title, :question, :answer)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':answer', $answer);
        $stmt->execute();  
}

$stmt = $db->prepare("SELECT * FROM faq");
$stmt->execute();
$faq = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en-US">
  <head>
    <title> FAQ </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style2.css">
    <script src = "script.js"></script>
  </head>

  <header>
    <?php  if ($user['status'] == 'Admin'): ?>
        <h1><a href="admin.php">Trouble Ticket Handler</a></h1>
    <?php  elseif ($user['status'] == 'Agent'): ?>
        <h1><a href="agent.php">Trouble Ticket Handler</a></h1>
    <?php endif; ?>
  </header>

  <a href="#" class = "a-prof" onclick="showFormFaq()">Create new FAQ</a>
  <form id="faq" style="display: none;" action="#" method="POST">
    <label for="title">Title:</label>
    <input type="text" name="title" required>
    <label for="question">Question:</label>
    <textarea name="question" rows="5" required></textarea>
    <label for="answer">Answer:</label>
    <textarea name="answer" rows="5" required></textarea>

    <input type="submit" name="submit_faq" value="Submit">
  </form>

  <?php foreach ($faq as $f): ?>
  <div id="faq-display">
    <h3 id = "title"><?= $f['title'] ?></h3>
    <p id = "question"><?= $f['question'] ?></p>
    <p id = "answer" ><?= $f['answer'] ?></p>
  </div>
  <?php endforeach; ?>


  <footer>
    &copy; Trouble Ticket
  </footer>
</html>