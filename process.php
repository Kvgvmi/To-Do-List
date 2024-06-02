<?php
try {
    $con = new PDO("mysql:host=localhost;dbname=To_Do", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$update = false;
$id = 0;
$name = '';

if (isset($_POST['save'])) {
    $task = $_POST['task'];

    $stmt = $con->prepare("INSERT INTO task (name) VALUES (:task)");
    $stmt->bindParam(':task', $task);
    $stmt->execute();

    header("location: index.php?result=true");
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $con->prepare("DELETE FROM task WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header("location: index.php");
}

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update = true;

    $stmt = $con->prepare("SELECT * FROM task WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $name = $row['name'];
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $task = $_POST['task'];

    $stmt = $con->prepare("UPDATE task SET name = :task WHERE id = :id");
    $stmt->bindParam(':task', $task);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header("location: index.php");
}

if (isset($_POST['toggleComplete'])) {
    $id = $_POST['id'];
    $completed = $_POST['completed'];

    $stmt = $con->prepare("UPDATE task SET completed = :completed WHERE id = :id");
    $stmt->bindParam(':completed', $completed, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>
