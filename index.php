<?php require_once 'process.php'; ?>
<?php
try {
    $con = new PDO("mysql:host=localhost;dbname=To_Do", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .completed {
            text-decoration: line-through;
        }
    </style>
</head>
<body>
    <br><br>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h2 class="text-center">To-Do List</h2>
                <hr><br>
                <form action="process.php" method="POST">
                    <div class="form-group">
                        <label for="tasktitle">Name Your Task:</label>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="text" name="task" class="form-control" id="tasktitle" placeholder="Enter Your Task" required autocomplete="off" value="<?php echo $name; ?>">
                    </div>
                    <?php if($update == true): ?>
                    <button type="submit" name="update" class="btn btn-success btn-block">Update</button>
                    <?php else: ?>
                    <button type="submit" name="save" class="btn btn-primary btn-block">Save</button>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-8">
                <br>
                <hr>
                <br>
                <?php
                $stmt = $con->query("SELECT * FROM task ORDER BY completed ASC, id DESC");
                ?>
                <div class="row justify-content-center">
                    <table class="table" id="taskTable">
                        <thead>
                            <tr>
                                <th>Completed</th>
                                <th>List</th>
                                <th colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="task-row <?php echo $row['completed'] ? 'completed-row' : ''; ?>" data-id="<?php echo $row['id']; ?>">
                                <td>
                                    <input type="checkbox" class="toggle-complete" data-id="<?php echo $row['id']; ?>" <?php echo $row['completed'] ? 'checked' : ''; ?>>
                                </td>
                                <td class="<?php echo $row['completed'] ? 'completed' : ''; ?>">
                                    <?php echo $row['name']; ?>
                                </td>
                                <td>
                                    <a href="index.php?edit=<?php echo $row['id']; ?>" class="btn btn-success">Edit</a>
                                    <a href="process.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
       <script>
    $(document).ready(function() {
        $('.toggle-complete').on('change', function() {
            var id = $(this).data('id');
            var completed = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: 'process.php',
                type: 'POST',
                data: {
                    toggleComplete: true,
                    id: id,
                    completed: completed
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        console.log('Task completion status updated successfully');
                        var row = $('tr[data-id="' + id + '"]');
                        row.find('td:nth-child(2)').toggleClass('completed', completed);
                        row.toggleClass('completed-row', completed);

                        // Re-sort the task list
                        var tableBody = $('#taskTable tbody');
                        var rows = tableBody.find('tr.task-row').sort(function(a, b) {
                            var aCompleted = $(a).find('.toggle-complete').is(':checked');
                            var bCompleted = $(b).find('.toggle-complete').is(':checked');

                            // Sort completed tasks to the bottom
                            if (aCompleted !== bCompleted) {
                                return aCompleted - bCompleted;
                            } else {
                                // Sort by task ID if completion status is the same
                                return $(a).data('id') - $(b).data('id');
                            }
                        });
                        tableBody.html(rows);
                    } else {
                        console.log('Failed to update task completion status');
                    }
                }
            });

            // Remove line-through styling when checkbox is unchecked
            if (!completed) {
                $(this).closest('tr').find('td:nth-child(2)').removeClass('completed');
            }
        });
    });
</script>



</body>
</html>
