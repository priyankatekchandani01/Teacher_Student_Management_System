<?php
// Path to the file where uploaded file data is stored
$fileRecordPath = 'uploaded_files.json';

// Read the existing records
$fileList = [];
if (file_exists($fileRecordPath)) {
    $fileList = json_decode(file_get_contents($fileRecordPath), true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<head>
  <link rel="stylesheet" href="css/style.css"/>
  <title>Student Attendance</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <link rel="stylesheet" href="css/c3.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/highcharts.js"></script>
  <script src="js/highcharts-exporting.js"></script>
  <script src="js/jquery.knob.js"></script>
  <script src="js/student.js"></script>
  <!-- Custom styles for this template -->
    <link href="navbar-fixed-top.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        td a {
            color: #007bff;
            text-decoration: none;
        }
        td a:hover {
            text-decoration: underline;
        }
    </style>
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
            <li><a href="index.php">Home</a></li>
            <li><a href="display_uploaded_files.php">Uploaded Files</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav><br><br><br><br>
    <h1>UPLOADED FILES</h1>
    <?php if (!empty($fileList)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Teacher Name</th>
                    <th>Uploaded On</th>
                    <th>File</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fileList as $file): ?>
                    <tr>
                        <td><?= htmlspecialchars($file['title']) ?></td>
                        <td><?= htmlspecialchars($file['teacher_name']) ?></td>
                        <td><?= htmlspecialchars($file['upload_time']) ?></td>
                        <td><a href="<?= htmlspecialchars($file['file_path']) ?>" target="_blank" download>Download</a></td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p style="text-align: center; color: red;">No files have been uploaded yet.</p>
    <?php endif; ?>
</body>
</html>
