$(document).ready(function () {
  // Ensure class_id and teacher_id are fetched dynamically
  // let class_id = $('#classId').val(); // Replace with the actual selector or variable initialization
  // let teacher_id = $('#teacherId').val();

  // Event handler for toggling individual attendance using checkbox
  $('.marker').on('change', function () {
    toggleAttendance($(this));
    updateMasterCheckbox(); // Ensure master checkbox updates based on state
  });

  // "All Present?" checkbox functionality
  $('#allAbsentCheckbox').on('change', function () {
    let markAsPresent = $(this).is(':checked'); // Check if the master checkbox is checked
    $('.student-record .marker').each(function () {
      $(this).prop('checked', markAsPresent); // Update all checkboxes
      toggleAttendance($(this)); // Update attendance status
    });
  });

  // Form submission handler
  $('#submit').on('click', function () {
    if (confirm('Are you sure you want to submit the attendance data?')) {
      submitData();
    }
  });
});

// Helper function to toggle attendance
function toggleAttendance(checkbox) {
  let isPresent = checkbox.prop('checked'); // Check checkbox state

  // Update the present count based on checkbox state
  if (isPresent) {
    checkbox.closest('.student-record').find('.present').text('1'); // Set present count to 1
  } else {
    checkbox.closest('.student-record').find('.present').text('0'); // Set present count to 0
  }
}

// Function to update the master checkbox state
function updateMasterCheckbox() {
  let allChecked = $('.marker').length === $('.marker:checked').length; // Check if all markers are checked
  $('#allAbsentCheckbox').prop('checked', allChecked); // Update master checkbox
}

// AJAX submission function
function submitData() {
  var data = [];
  var time = Math.round((new Date).getTime()/1000);
  $('.student-record').each(function(k,v) {
    var d = {
      roll:$(this).find('.roll').text(),
      newpresent:$(this).find('.present').text(),
      timestamp:time
    };
    data.push(d);
  });
  console.log(data);
  $.ajax({
    url : 'php/mark_attendance.php',
    type : 'post',
    data : {content:data,class_id:class_id,teacher_id:teacher_id},
    dataType : 'json',
    success : function(r) {
      console.log(r);
      if(r.error == 'none') {
        $('#submit').html('Saved!');
        $('#studentRecords').hide('slow',function() {
          $('#studentRecords').html('<h2> Saved! Redirecting you to home page </h2>');
        });
        $('#studentRecords').show('fast',function () {
          setTimeout(function() {
            window.location = "teacher.php";
          },1500);
        });
      }
    },
    error : function() {
      console.log('error');
    }
  });
}
