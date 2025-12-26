<?php
session_start();
include 'con.php';

$session_name = $_SESSION['admin'] ?? '';
if ($session_name == "") {
    header("Location: admin.php");
    exit;
}

date_default_timezone_set('Asia/Kolkata');
$time = date("Y-m-d-H-i-s");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Files</title>
    <link rel="icon" href="favicon.jpg" type="image/jpeg">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        p { color: green; font-weight: bold; }

        .dark {
            background: #121212;
            color: #e0e0e0;
        }
        .dark textarea,
        .dark input {
            background: #1e1e1e;
            color: #fff;
            border: 1px solid #444;
        }
    </style>
</head>

<body>
<div id="main" class="text-center">
    <h2>KEEP NOTES</h2>

    <form method="post">

            <p>Files will be saved with a .txt extension by default.</p>
      <div class="d-flex align-items-center justify-content-between mb-2"
     style="margin-left:2.0cm; margin-right:2.0cm;">

    <input type="text"
           class="form-control"
           name="custom_filename"
           placeholder="Enter file name (optional)"
           style="max-width:300px;">

    <button type="button"
        id="themeToggle"
        class="btn btn-dark ms-3"
        onclick="toggleDark()">
    🌙
</button>
</div>

        <!-- Notes -->
        <textarea id="notes" name="comments" rows="15" cols="162" required></textarea><br>

        <div class="btn-group">
            <button class="btn btn-danger" type="submit" name="submit">Submit</button>
            <a class="btn btn-warning" href="admin_panel.php">Home</a>
            <a class="btn btn-primary" href="files.php">Refresh</a>
            <button type="button" class="btn btn-secondary" onclick="clearNotes()">Clear</button>
        </div>
    </form>
</div>

<?php
if (isset($_POST['submit'])) {

    $comments = $_POST['comments'];
    $customName = $_POST['custom_filename'] ?? '';
    $customName = preg_replace("/[^a-zA-Z0-9_-]/", "", $customName);

    $baseDir = "/var/www/html/TextFiles/";
    $userDir = $baseDir."/";

    if (!is_dir($userDir)) {
        mkdir($userDir, 0777, true);
    }

    $filename = $customName
        ? $customName . ".txt"
        : "New-" . $time . ".txt";

    $filePath = $userDir . $filename;
    file_put_contents($filePath, $comments);

    $relativeURL = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath);

    echo "<p class='text-center'>
            File created successfully :
            <a target='_blank' href='$relativeURL'>" . basename($filePath) . "</a>
          </p>";

    echo "<script>
            localStorage.removeItem('autosave_{$session_name}');
          </script>";
}
?>

<!-- ================= JS ================= -->
<script>
const USER = "<?php echo $session_name; ?>";
const STORAGE_KEY = "autosave_" + USER;
const MODE_KEY = "dark_mode";
const textarea = document.getElementById("notes");

let debounceTimer = null;
let lastSaved = "";
function toggleDark() {
    document.body.classList.toggle("dark");

    const btn = document.getElementById("themeToggle");
    const isDark = document.body.classList.contains("dark");

    btn.textContent = isDark ? "☀️" : "🌙";
    localStorage.setItem("dark_mode", isDark ? "on" : "off");
}
/* Restore */
window.onload = () => {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
        textarea.value = saved;
        lastSaved = saved;
    }
 const isDark = localStorage.getItem("dark_mode") === "on";
    const btn = document.getElementById("themeToggle");

    if (isDark) {
        document.body.classList.add("dark");
        btn.textContent = "☀️";
    } else {
        btn.textContent = "🌙";
    }
};

/* Debounce local autosave */
textarea.addEventListener("input", () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(saveLocal, 2000);
});

/* Periodic local + server autosave */
setInterval(() => {
    if (textarea.value !== lastSaved) {
        saveLocal();
        serverAutoSave();
    }
}, 10000);

function saveLocal() {
    localStorage.setItem(STORAGE_KEY, textarea.value);
    lastSaved = textarea.value;
}

function serverAutoSave() {
    fetch("autosave.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "content=" + encodeURIComponent(textarea.value)
    });
}

function clearNotes() {
    if (confirm("Clear notes permanently?")) {
        textarea.value = "";
        localStorage.removeItem(STORAGE_KEY);
        lastSaved = "";
    }
}

</script>

</body>
</html>
