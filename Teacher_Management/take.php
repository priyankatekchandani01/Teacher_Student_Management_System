<?php
  $isIndex = 0;
  session_start();
  if(!(array_key_exists('teacher_id',$_SESSION) && isset($_SESSION['teacher_id']))) {
    session_destroy();
    if(!$isIndex) header('Location: index.php');
  }
?>
<?php include 'php/node_class.php'; ?>
<?php
  /*
  login -> session mai save hoga kuch , which will identify the teacher
  addClass -> we will get a link , which will have cN as an identifier of the class
  we use these to find the 'object' of this particular class
  then we show the list of students , with their attendance and stuff 
  then we have javascript which will function on the buttons next to each student
  then we have a save button
  */
  $teacher_id = $_SESSION['teacher_id'];
  $classes = $_SESSION['classes'];
  $name = $_SESSION['name'];
  
  if(!isset( $_GET['cN'] ) || empty( $_GET['cN'] )) {
    die('<h1>Invalid Request</h1>');
  }
  $class_id = $_GET['cN'];
  
  if(!in_array($class_id,$classes)) die( "No such record." );
  // Assuming that we have validated and thrown errors if any , we proceed 
  // By finding the particular object we are talking about 
  
  // Connecting to the database 
  $classNode = new Node;
  $node = $classNode->retrieveObjecti($class_id,$teacher_id) or die("No such record");

  // Intimating the teacher about Number of Classes , and student list
  // A foreach loop which will go on till all students are covered 
  $records = $node->getRecords();

  $todayDate = date("d/m/Y");
?>
<html>
 <head>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="css/teacher.css"/>
  <title><?php echo $name. ' - '.$node->getCode(). ' ('.$node->getSection().') '.$todayDate; ?></title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap.theme.min.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  
  <script>
    var numberOfDays = <?php echo $node->getDays(); ?>;
    var class_id = <?php echo $class_id;?>;
    var teacher_id = <?php echo $teacher_id; ?>;

    $(document).ready(function(){
      // All Present checkbox toggle functionality
      $('#allAbsentCheckbox').change(function() {
        $('.marker').prop('checked', $(this).prop('checked'));
      });

      // Change event to keep track of individual attendance checkboxes
      $('.marker').change(function() {
        if($('.marker:checked').length == $('.marker').length) {
          $('#allAbsentCheckbox').prop('checked', true);
        } else {
          $('#allAbsentCheckbox').prop('checked', false);
        }
      });
    });
  </script>
  <script src="js/take.js"></script>
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
			      <li><a href="statistics.php">Statistics</a></li>
            <li><a href="upload_files.php">Upload Files</a></li>
			      <li><a href="about.html">About</a></li>
            <li><a href="#contact">Contact</a></li>
			      <li><a href="logout.php">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav></br></br></br></br>
 
  <div class="container"> 
    <?php 
      echo '<h1>Welcome , '.$name.'</h1>';
      echo '<h3>Class : '.$node->getCode(). ' ('.$node->getSection().') '.$node->getYear().'</h3>';
      echo '<h3>Date: ' . $todayDate . '</h3>';
      echo '<h3>Number of Classes conducted : '.$node->getDays().'</h3>';
      echo '<button class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-sm">Help me!</button> <button id="submit" class="btn btn-success">Submit</button>';
      echo '<div class="form-check text-right" style="text-align: right; margin-right: 20px;">
            <input type="checkbox" id="allAbsentCheckbox" class="form-check-input">
            <label for="allAbsentCheckbox" class="form-check-label" style="font-size: 18px;">All Present?</label>
            </div> ';
    ?>
    <div id="studentRecords">
      <?php
        foreach($records as $roll => $data) {
          // Check if the student is present or absent
          $isChecked = $data['present'] > 0 ? 'checked' : '';
          echo '<div class="student-record">
            <span class="roll"><a href="student.php?roll='.str_replace("/", "-", $roll).'&code='.$node->getCode().'&year='.$node->getYear().'&section='.$node->getSection().'">'.$roll.'</a></span>:  
            <span class="present">'.$data['present'].'</span>&nbsp;&nbsp;
            <input type="checkbox" class="marker" '.$isChecked.'>
          </div>';
        }
      ?>
    </div>
    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <h2 class="text-center"> Instructions </h2>
          <hr>
          <ol class="text-left">
            <li>Click on any student's roll number to see his/her records, attendance percentage etc.</li>
            <li>The number next to any student shows the number of days he/she has attended your class</li>
            <li>Click the <button class="btn">Box</button> button next to that roll number to mark that student as present</li>
            <li>Click the <button class="btn btn-success">Box</button> button if you have accidentally marked that student as present</li>
            <li>Click the <button class="btn btn-success">Submit</button> button at top to save your attendance details</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <script>
  $('#add-class-btn').on('click', function () {
    const newClassData = {
        class_id: class_id,
        teacher_id: teacher_id,
        date: new Date().toLocaleDateString('en-GB'), // Get the current date (dd/mm/yyyy)
    };

    // AJAX request to add a new class to the database
    $.ajax({
        url: 'add_class.php', // Backend script to handle adding a class
        type: 'POST',
        data: newClassData,
        success: function (response) {
            if (response === 'success') {
                alert('Class added successfully!');
                location.reload(); // Reload the page to reflect the changes
            } else {
                alert('Error: ' + response);
            }
        },
        error: function () {
            alert('Failed to add the class. Please try again later.');
        },
    });
});

$('#studentRecords').append(`
    <div class="student-record">
        <span class="roll">${newClassData.class_id}</span>:  
        <span class="present">0</span>&nbsp;&nbsp;
        <input type="checkbox" class="marker">
    </div>
`);


  </script>
 </body>
</html>
