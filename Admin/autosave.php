<?php
session_start();
$user = $_SESSION['admin'];
$content = $_POST['content'] ?? '';

$baseDir = "/var/www/html/TextFiles/autosave/";
$userDir = $baseDir . $user . "/";

if (!is_dir($userDir)) {
    mkdir($userDir, 0777, true);
}

file_put_contents($userDir . "autosave.txt", $content);
