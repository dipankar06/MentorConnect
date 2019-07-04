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
 * @CreatedOn: 03-06-2018
 * @Description: Role of Mentor Page
*/

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');

redirect_if_major_upgrade_required();

// TODO Add sesskey check to edit
$edit   = optional_param('edit', null, PARAM_BOOL);    // Turn editing on and off
$reset  = optional_param('reset', null, PARAM_BOOL);

require_login();

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}
$userrole = get_atalrolenamebyid($USER->msn);
$strmymoodle = get_string('myhome');
// Check the User Has Filled the Updated the Form
$firstlogin = false;
$user = $DB->get_record('user', array('id'=>$USER->id),'profilestatus');
if(!$user->profilestatus){
	$firstlogin = true;
}
if($firstlogin){
	if($userrole == 'mentor'){
		$url = $CFG->wwwroot.'/atalfeatures/editmentor.php?firstlogin=1&key='.encryptdecrypt_userid($USER->id,"en");
		redirect($url);
	}
}
$userid = $USER->id;  // Owner of the page
$context = context_user::instance($USER->id);
$PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
$header = fullname($USER);
$pagetitle = $strmymoodle;

// Get the My Moodle page info.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page($userid, MY_PAGE_PRIVATE)) {
    print_error('mymoodlesetup');
}

// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/atalfeatures/roleofmentor.php', $params);
$PAGE->set_pagelayout('roleofmentor');
$PAGE->set_pagetype('my-index');
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
//$PAGE->set_title($pagetitle);
$PAGE->set_title("{$SITE->fullname}");
$PAGE->set_heading($header);

$SESSION->roleofmentorpage = 1;
// Toggle the editing state and switches
$USER->editing = $edit = 0;

echo $OUTPUT->header();

echo $OUTPUT->heading("Role of a Mentor");

$pdflink = get_atalvariables('roleofmentor');
$pdflink = $CFG->wwwroot.$pdflink;
$test = '<input type="hidden" id="id_frmcategory"><form id="mform2"></form>';

$content = '<br><embed src="'.$pdflink.'" width="100%" height="580px" />'.$test;

echo $content;

echo $OUTPUT->footer();

?>
<script type="text/javascript">
var slideIndex = 1;
//showSlides(slideIndex);

//showSlides();  //Uncomment it to view Event Slide show in this Page

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

