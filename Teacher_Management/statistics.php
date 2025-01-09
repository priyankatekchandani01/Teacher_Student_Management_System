<?php
session_start();
$isIndex = 0;

if (!(array_key_exists('teacher_id', $_SESSION) && isset($_SESSION['teacher_id']))) {
    session_destroy();
    if (!$isIndex) header('Location: index.php');
}
?>
<?php include 'php/node_class.php'; ?>
<html>
<head>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/teacher.css"/>
    <link rel="stylesheet" href="css/statistics.css"/>
    <title>Statistics</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <link href="navbar-fixed-top.css" rel="stylesheet">
    <style>
        .attendance-table {
            margin-top: 30px;
            border-collapse: collapse;
            width: 100%;
        }

        .attendance-table th, .attendance-table td {
            padding: 15px;
            text-align: center;
            border: 2px solid #333;
        }

        .attendance-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .attendance-table td {
            background-color: #fafafa;
        }

        .attendance-table td a {
            color: #28A745;
            text-decoration: none;
        }

        .attendance-table td a:hover {
            text-decoration: underline;
        }

        .attendance-table td ul {
            padding-left: 0;
            list-style-type: none;
        }

        .attendance-table td ul li {
            margin: 5px 0;
        }

        .class-summary {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .class-summary h4 {
            color: #333;
        }

        .class-summary .class-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .class-summary .class-info div {
            font-size: 16px;
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
                <li><a href="teacher.php">Dashboard</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li class="active"><a href="statistics.php">Statistics</a></li>
                <li><a href="upload_files.php">Upload Files</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
<br><br><br><br>

<div class="container">
    <div class="wrapper">
      
        <?php
        $classes = $_SESSION['classes'];
        $teacher_id = $_SESSION['teacher_id'];
        $n = new Node;
        if ($classes != 0) {
            $data = array();
            $studentAttendance = array(); // For storing student attendance

            foreach ($classes as $c) {
                $node = $n->retrieveObjecti($c, $teacher_id) or die("No such record");

                $key = $node->getCode() . ' (' . $node->getSection() . '), ' . $node->getYear();
                $total_days = $node->getDays();

                $data[$key] = array(
                    "present" => 0,
                    "absent" => 0
                );
                
                $studentAttendance[$key] = array("present" => [], "absent" => []);

                if ($total_days) {
                    $total_present = 0;
                    $total_absent = 0;

                    foreach ($node->getRecords() as $roll => $rec) {
                        if ($rec['present'] == 1) {
                            $studentAttendance[$key]['present'][] = $roll;  // Add present roll number
                        } else {
                            $studentAttendance[$key]['absent'][] = $roll;   // Add absent roll number
                        }

                        $total_present += $rec['present'];
                        $total_absent += $total_days - $rec['present'];
                    }

                    $data[$key]['present'] = $total_present;
                    $data[$key]['absent'] = $total_absent;
                }
            }

            echo '<script> var data = ' . json_encode($data) . '; var studentAttendance = ' . json_encode($studentAttendance) . '; </script>';
            echo '<ul class="nav nav-tabs">
                    <li class="active"><a href="#graph" data-toggle="tab">Attendance Graph</a></li>
                    <li><a href="#detained" data-toggle="tab">Short Attendance</a></li>
                  </ul>
                  <div class="tab-content">
                    <div id="graph" class="tab-pane fade in active" style="width:100%; height:400px;"></div>
                    <div id="detained" class="tab-pane fade">';

            // Display attendance details in a more structured and visually appealing way
            echo '<table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($data as $classKey => $d) {
                $total_students = $d['present'] + $d['absent'];
                echo '<tr>
                        <td>' . $classKey . '</td>
                        <td><button class="btn btn-info toggle-btn" data-class="' . $classKey . '">Show Attendance</button></td>
                    </tr>';
            }

            echo '</tbody></table>';

            echo '</div>
                  </div>';
        } else {
            echo "<h3>You have no classes added yet</h3>";
        }
        ?>
    </div>
</div>

<!-- Modal for displaying attendance details -->
<div id="attendance-details-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="attendance-details-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="attendance-details-label">Attendance Details</h4>
            </div>
            <div class="modal-body">
                <div id="attendance-details-content"></div>
            </div>
        </div>
    </div>
</div>

<script>
   $(document).ready(function () {
    // Initialize chart data
    var categories = []; // Store the class numbers (1, 2, 3, etc.)
    var seriesData = {}; // Object to store series data for each class

    // Initialize a separate chart for each class
    for (var classKey in data) {
        if (data.hasOwnProperty(classKey)) {
            var classGraphContainer = 'graph-' + classKey.replace(/\s+/g, '-').toLowerCase();
            $('#graph').append('<div id="' + classGraphContainer + '" style="width:100%; height:400px; margin-bottom: 50px;"></div>');

            // Initialize series data for each class
            seriesData[classKey] = {
                present: [],
                absent: [],
                totalClasses: 0 // Track the number of classes conducted
            };

            Highcharts.chart(classGraphContainer, {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Attendance for ' + classKey
                },
                xAxis: {
                    categories: categories, // Class numbers as categories
                    title: {
                        text: 'Classes Conducted'
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Number of Students'
                    }
                },
                series: [{
                    name: 'Present',
                    data: seriesData[classKey].present,
                    color: 'green'
                }, {
                    name: 'Absent',
                    data: seriesData[classKey].absent,
                    color: 'orange'
                }]
            });
        }
    }

    // Simulate class attendance update dynamically
    for (var classKey in data) {
        if (data.hasOwnProperty(classKey)) {
            // Append data points incrementally for each class conducted
            var presentCount = data[classKey].present;
            var absentCount = data[classKey].absent;
            var classGraphContainer = 'graph-' + classKey.replace(/\s+/g, '-').toLowerCase();

            // Increment total classes conducted
            seriesData[classKey].totalClasses++;

            // Add data for the current class
            categories.push('Class ' + seriesData[classKey].totalClasses);
            seriesData[classKey].present.push(presentCount);
            seriesData[classKey].absent.push(absentCount);

            // Update the chart with new data
            Highcharts.chart(classGraphContainer, {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Attendance for ' + classKey
                },
                xAxis: {
                    categories: categories // Update categories dynamically
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Number of Students'
                    }
                },
                series: [{
                    name: 'Present',
                    data: seriesData[classKey].present,
                    color: 'green'
                }, {
                    name: 'Absent',
                    data: seriesData[classKey].absent,
                    color: 'orange'
                }]
            });
        }
    }

        // Show attendance details in modal with roll number, status, and date
        $('.toggle-btn').on('click', function () {
            var classKey = $(this).data('class');
            var presentStudents = studentAttendance[classKey].present;
            var absentStudents = studentAttendance[classKey].absent;

            // Get the current date when attendance is taken
            var currentDate = new Date();
            var formattedDate = currentDate.getDate() + '/' + (currentDate.getMonth() + 1) + '/' + currentDate.getFullYear().toString().slice(-2);  // Date in DD/MM/YY format

            // Combine present and absent students into one array
            var allStudents = [];

            presentStudents.forEach(function (student) {
                allStudents.push({ rollNo: student, status: 'P' });
            });

            absentStudents.forEach(function (student) {
                allStudents.push({ rollNo: student, status: 'A' });
            });

            // Sort the combined array by roll number (numeric part before /)
            allStudents.sort(function (a, b) {
                return parseInt(a.rollNo.split('/')[0]) - parseInt(b.rollNo.split('/')[0]);  // Sorting by numeric part
            });

            var content = '<table class="attendance-table" style="width:100%;">' +
                '<thead>' +
                    '<tr>' +
                        '<th>Date</th>' +
                        '<th>' + formattedDate + '</th>' +  // Display the date dynamically
                    '</tr>' +
                    '<tr>' +
                        '<th>Roll Number</th>' +
                        '<th>Status</th>' +
                    '</tr>' +
                '</thead>' +
                '<tbody>';
            
            // Display all students in order (sorted)
            allStudents.forEach(function (student) {
                content += '<tr><td>' + student.rollNo + '</td><td>' + student.status + '</td></tr>';
            });

            content += '</tbody></table>';

            // Set the content inside the modal
            $('#attendance-details-content').html(content);

            // Open the modal
            $('#attendance-details-modal').modal('show');
        });
    });
</script>
</body>
</html>
