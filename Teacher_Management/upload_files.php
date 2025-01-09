<?php
// Path to the file where uploaded file data is stored
$fileRecordPath = 'uploaded_files.json';

// Initialize an empty error and success message
$error = '';
$success = '';

date_default_timezone_set("Asia/Kolkata");
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $teacherName = $_POST['teacher_name'] ?? '';
    $uploadTime = date('d-m-Y h:i:s');

    // Ensure file is not re-uploaded on refresh by checking file name
    if (isset($_FILES['uploaded_file']['name']) && !empty($_FILES['uploaded_file']['name'])) {
        $fileName = $_FILES['uploaded_file']['name'];
        $fileTmp = $_FILES['uploaded_file']['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Check if file already exists before uploading again
        $uploadDir = 'uploads/';
        $filePath = $uploadDir . basename($fileName);
        if (file_exists($filePath)) {
            $error = 'File already uploaded.';
        } else {
            if (!empty($fileExtension)) {
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                if (move_uploaded_file($fileTmp, $filePath)) {
                    // Save file details to the JSON file
                    $fileList = [];
                    if (file_exists($fileRecordPath)) {
                        $fileList = json_decode(file_get_contents($fileRecordPath), true);
                    }

                    $fileList[] = [
                        'title' => $title,
                        'teacher_name' => $teacherName,
                        'upload_time' => $uploadTime,
                        'file_path' => $filePath,
                    ];

                    file_put_contents($fileRecordPath, json_encode($fileList, JSON_PRETTY_PRINT));
                    $success = 'File uploaded successfully!';
                } else {
                    $error = 'Failed to upload the file.';
                }
            } else {
                $error = 'Invalid file type.';
            }
        }
    } else {
        $error = 'No file selected.';
    }
}

// Read existing file data
$fileList = [];
if (file_exists($fileRecordPath)) {
    $fileList = json_decode(file_get_contents($fileRecordPath), true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="css/teacher.css"/>
  <link rel="stylesheet" href="css/upload_files.css"/>
  <title>Profile</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/profile.js"></script>
  <!-- Custom styles for this template -->
    <link href="navbar-fixed-top.css" rel="stylesheet">
 </head>
 <body>
   <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">Teacher Management</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li class="active"><a href="teacher.php">Dashboard</a></li>
            <li><a href="profile.php">Profile</a></li> 
            <!-- <li><a href="generate_weekly_report.php">Generate Report</a></li>  -->
			<li><a href="statistics.php">Statistics</a></li>
            <li><a href="upload_files.php">Upload Files</a></li>
			<li><a href="about.html">About</a></li>
            <li><a href="#contact">Contact</a></li>
			<li><a href="logout.php">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav></br></br></br></br>
 
    <h2>FILE UPLOADING</h2>
<div class="container">
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br>

            <label for="teacher_name">Teacher Name:</label>
            <input type="text" id="teacher_name" name="teacher_name" required><br>

            <label for="uploaded_file">Choose File:</label>
            <input type="file" id="uploaded_file" name="uploaded_file" required><br>

            <button type="submit">Upload</button>
        </form>
        </div>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="file-list-container">
        <h2>Uploaded Files</h2>

        <?php if (!empty($fileList)): ?>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Teacher Name</th>
                    <th>Uploaded On</th>
                    <th>File</th>
                </tr>
                <?php foreach ($fileList as $file): ?>
                    <tr>
                        <td><?= htmlspecialchars($file['title']) ?></td>
                        <td><?= htmlspecialchars($file['teacher_name']) ?></td>
                        <td><?= htmlspecialchars($file['upload_time']) ?></td>
                        <td><a href="<?= htmlspecialchars($file['file_path']) ?>" target="_blank">View File</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No files uploaded yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
