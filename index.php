<?php
/**
 * Simple To-Do Manager
 * Single file application
 * Stack: PHP + MariaDB / MySQL
 */

// ============================================
// DATABASE CONNECTION
// Change these values to match your VPS / hosting
// ============================================
$host     = 'localhost';
$dbname   = 'todo_app';
$username = 'root';          // change this
$password = '';              // change this

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ============================================
// HANDLE ACTIONS
// ============================================

// Add new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title'] ?? '');
    if ($title !== '') {
        $stmt = $pdo->prepare("INSERT INTO tasks (title) VALUES (?)");
        $stmt->execute([$title]);
    }
    header("Location: index.php");
    exit;
}

// Toggle complete / incomplete
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("UPDATE tasks SET is_completed = NOT is_completed WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}

// Delete task
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}

// ============================================
// FETCH TASKS
// ============================================
$filter = $_GET['filter'] ?? 'all';

if ($filter === 'pending') {
    $stmt = $pdo->query("SELECT * FROM tasks WHERE is_completed = 0 ORDER BY created_at DESC");
} elseif ($filter === 'completed') {
    $stmt = $pdo->query("SELECT * FROM tasks WHERE is_completed = 1 ORDER BY created_at DESC");
} else {
    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
}

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Counts
$total     = $pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn();
$pending   = $pdo->query("SELECT COUNT(*) FROM tasks WHERE is_completed = 0")->fetchColumn();
$completed = $pdo->query("SELECT COUNT(*) FROM tasks WHERE is_completed = 1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple To-Do Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 40px auto;
            padding: 0 15px;
            background: #f5f5f5;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #222;
        }
        .summary {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }
        form {
            display: flex;
            gap: 8px;
            margin-bottom: 25px;
        }
        input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 15px;
        }
        button {
            padding: 10px 16px;
            background: #2c7be5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 15px;
        }
        button:hover {
            background: #1a68d1;
        }
        .filters {
            margin-bottom: 15px;
            text-align: center;
        }
        .filters a {
            margin: 0 8px;
            text-decoration: none;
            color: #2c7be5;
            font-size: 14px;
        }
        .filters a.active {
            font-weight: bold;
            text-decoration: underline;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        li {
            background: white;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #ddd;
        }
        li.completed span {
            text-decoration: line-through;
            color: #888;
        }
        .actions a {
            margin-left: 10px;
            font-size: 13px;
            text-decoration: none;
        }
        .actions a.toggle {
            color: #28a745;
        }
        .actions a.delete {
            color: #dc3545;
        }
        .empty {
            text-align: center;
            color: #888;
            padding: 30px 0;
        }
    </style>
</head>
<body>

    <h1>Simple To-Do Manager</h1>

    <div class="summary">
        Total: <?= $total ?> &nbsp;|&nbsp;
        Pending: <?= $pending ?> &nbsp;|&nbsp;
        Completed: <?= $completed ?>
    </div>

    <form method="POST" action="index.php">
        <input type="hidden" name="action" value="add">
        <input type="text" name="title" placeholder="Enter a new task..." required autofocus>
        <button type="submit">Add</button>
    </form>

    <div class="filters">
        <a href="?filter=all" class="<?= $filter === 'all' ? 'active' : '' ?>">All</a>
        <a href="?filter=pending" class="<?= $filter === 'pending' ? 'active' : '' ?>">Pending</a>
        <a href="?filter=completed" class="<?= $filter === 'completed' ? 'active' : '' ?>">Completed</a>
    </div>

    <?php if (count($tasks) === 0): ?>
        <div class="empty">No tasks found.</div>
    <?php else: ?>
        <ul>
            <?php foreach ($tasks as $task): ?>
                <li class="<?= $task['is_completed'] ? 'completed' : '' ?>">
                    <span><?= htmlspecialchars($task['title']) ?></span>
                    <div class="actions">
                        <a class="toggle" href="?toggle=<?= $task['id'] ?>">
                            <?= $task['is_completed'] ? 'Undo' : 'Complete' ?>
                        </a>
                        <a class="delete" href="?delete=<?= $task['id'] ?>"
                           onclick="return confirm('Delete this task?')">Delete</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</body>
</html>
