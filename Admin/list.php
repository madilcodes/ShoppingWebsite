<?php
session_start();
$session_name = $_SESSION['admin'];
if ($session_name == "") {
  header("Location: admin.php");
}
$folder = '/var/www/html/TextFiles/';
$fileCount = count(glob($folder . '*'));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oldName = $_POST['old_name'];
            $newName = trim($_POST['new_name']) . '.' . $_POST['extension'];
            if (rename($folder . '/' . $oldName, $folder . '/' . $newName)) {
                echo '<span style="color: green;">' . $oldName . ' RENAMED AS ' . $newName . '</span>';
            } else {
                echo '<span style="color: red;">Error Renaming file.</span>';
            }

        }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Files List</title>
    <link rel="icon" href="favicon.jpg" type="image/jpeg">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <style>

    table {
        width: 100%;
        border-collapse: collapse;

    }
    th, td {
        padding: 8px;
        border-bottom: 1px solid #ddd;
        text-align: center;
    }
    th {
        background-color: #f2f2f2;
    }

    .table-container {
        max-height: 550px;
        overflow-y: auto;
    }

    .fixed-header {
        position: sticky;
        top: 0;
        background-color: #f2f2f2;
    }
</style>
</head>

<body class='text-center'>
<div class="d-flex justify-content-between p-2">
    <span class="text-center">MY NOTES { <?= $fileCount; ?> }</span>

    <input class="form-control w-25"
           type="search"
           id="search-input"
           autocomplete="off"
           placeholder="🔎 Search files..." />
</div>

<div class="table-container">

    <table class=" table table-striped table-hover table-bordered ">
    <thead class="fixed-header">
        <tr >
            <th>S.no</th>
            <th>File Name</th>
            <th>Actions</th>
        </tr>
    </thead>
        <?php
        $files = scandir($folder);
        $serialNumber = 1;

        foreach ($files as $file) {
             if (is_file($folder . $file)) {
                if ($file !== '.' && $file !== '..') {
                    echo '<form method="post">';
                echo '<tr>';
                echo '<td>' . $serialNumber . '</td>';
                echo '<td>';
                echo '<label><input title="Rename-file" type="checkbox" name="file[]" value="' . $file . '"> ' . $file . '</label>';
                echo '<div style="display:none;" id="' . $file . '_rename">';
                echo '<input title="Enter  New-Name" type="text" name="new_name" value="' . pathinfo($file, PATHINFO_FILENAME) . '">';
                echo '<select   name="extension">';
                echo '<option value="txt">txt</option>';
                echo '<option value="html">html</option>';
                echo '<option value="css">css</option>';
                echo '<option value="js">js</option>';
                echo '<option value="php">php</option>';
                echo '<option value="py">py</option>';
                echo '<option value="pl">pl</option>';
                echo '<option value="agi">agi</option>';
                echo '<option value="sql">sql</option>';
                echo '<option value="pdf">pdf</option>';
                echo '<option value="doc">doc</option>';
                echo '<option value="xlsx">XLSX</option>';
                echo '</select>';
                echo '<input type="hidden" name="old_name" value="' . $file . '">';
                echo '<button class="bg-success p-0" type="submit" title="Save file"><i class="fa fa-check text-light"></i></button>';
                echo '</div>';
                echo '</td>';
                echo '<td>';
                echo '<a class="btn btn-secondary fa fa-eye" title="view-file" href="/mdadil/curdyt/TextFiles/' . urlencode($file) . '"></a>&nbsp;';
                echo '<a class="btn btn-primary fa fa-edit" title="Edit-file" href="list_edit.php?file=' . urlencode($file) . '"></a>&nbsp;';
                echo '<a class="btn btn-success fa fa-download" title="Download-file" href="/mdadil/curdyt/TextFiles/' . urlencode($file) . '" download></a>&nbsp;';
                echo '<a class="btn btn-danger fa fa-trash" title="Delete-file" href="delete_list.php?file=' . urlencode($file) . '" onclick="confirmDeletion(event, \'' . addslashes(urlencode($file)) . '\')"></a>';

                echo '</tr>';
                $serialNumber++;

                echo '</form>';
                echo '<script>
                      document.querySelector(\'input[name="file[]"][value="' . $file . '"]\').addEventListener("click", function() {
                        var renameDiv = document.getElementById("' . $file . '_rename");
                        if (this.checked) {
                          renameDiv.style.display = "block";
                        } else {
                          renameDiv.style.display = "none";
                        }
                      });
                      </script>';
            }
        }
     }
        ?>
    </table>
    </div>

    <div class="btn-group" role="group" aria-label="Basic example">
        <a class="btn btn-warning" href='admin_panel.php' title='Back to Dashbord'>Home</a>
        <a class='btn btn-secondary' title='Back to My-Files' href="files.php">Back</a>
        <a class='btn btn-primary' title='Refresh' href="list.php">Refresh</a>
        <div>
        <script>

$(document).ready(function(){

       $("tbody").before(headerClone);
   });
function confirmDeletion(event, file) {
    event.preventDefault();
    swal({
        title: "Delete file?",
        text: "Are you sure you want to delete this file?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {

            window.location.href = 'delete_list.php?file=' + encodeURIComponent(file);
        }
    });
}
$(document).ready(function () {

    $("#search-input").on("keyup", function () {
        let value = $(this).val().toLowerCase();

        $("table tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

});

</script>

</body>

</html>
