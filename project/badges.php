<?php
/*
Copyright (C) 2019  IBM Corporation 
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details at 
http://www.gnu.org/licenses/gpl-3.0.html
*/

/* @package: core_project
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 11-01-2018
 * @Description: Display Badges to Mentor. Mentor assign Badges to student
 This Page (Badges) will be accessable to Mentors. Only Assign Badges is done here.
*/

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');

redirect_if_major_upgrade_required();

require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}

$userrole = get_atalrolenamebyid($USER->msn);
if($userrole!="mentor"){
	//Only School-incharge can create/edit a project Info.
	redirect($CFG->wwwroot.'/my');
}
include_once(__DIR__ .'/libbadge.php');

$userid = $USER->id;  // Owner of the page
$context = context_user::instance($USER->id);
$PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
$header = fullname($USER);
$pagetitle = "Badges";

// Get the My Moodle page info.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page($userid, MY_PAGE_PRIVATE)) {
    print_error('mymoodlesetup');
}

// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/project/badges.php', $params);
$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('my-index');
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title("{$SITE->fullname}");
$PAGE->set_heading($header);


echo $OUTPUT->header();

//echo $OUTPUT->custom_block_region('content'); 
//Display Froum/Timeline/Project Tabs Like myDashboard
//Center Page Content/Badge Form

$output = "<br><br><h4>This Feature is coming soon !!</h4>"; //get_badgeform();
echo $output;

echo $OUTPUT->footer();

?>
<script type="text/javascript">
/*
@ Created by : Jothi , 09-05-18
* Javascript for Event SlideShow
*/
var slideIndex = 1;
//showSlides(slideIndex);

//showSlides(); //Uncomment it to view Event Slide show in this Page

function showSlides() {
    var i;
    var slides = document.getElementsByClassName("customslider");
	var dots = document.getElementsByClassName("dot");
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none"; 
    }
    slideIndex++;
    if (slideIndex > slides.length) {slideIndex = 1} 
    slides[slideIndex-1].style.display = "block"; 
    setTimeout(showSlides, 5000); // Change image every 2 seconds
	  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
	  }
	  dots[slideIndex-1].className += " active";
}

function plusSlides(n) {
  showSlides_next(slideIndex += n);
}

function currentSlide(n) {
  showSlides_next(slideIndex = n);
}

function showSlides_next(n) {
  var i;
  var slides = document.getElementsByClassName("customslider");
  var dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
}
</script>