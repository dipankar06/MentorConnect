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

/* @package: core_create
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 12-03-2018
 * @Description:tmp file
*/
require_once('../config.php'); //Include Global Cofiguration file
include_once(__DIR__ .'/lib.php');
require_once($CFG->libdir.'/filelib.php'); //Include global library
$PAGE->set_url('/create/view_temp_mentor.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('View Temp Mentor');
echo $OUTPUT->header();
// Query to remove test from email
//UPDATE some_table SET some_field = REPLACE(some_field, '&lt;', '<') ;
// echo substr($email, 0, -4); // Remove last 4 characters from email

$start = $_REQUEST['start'];
$end = $_REQUEST['end'];
if($_REQUEST['mod']=='viewlimit'){
$sql = "Select * from {temp_mentor_ph2_dec} limit $start,$end";
echo $sql."</br>";
$result = $DB->get_records_sql($sql);
echo "Temp Mentor From Excel With Limit";
echo "<table class='table table-striped' style='width:40%;margin-right: 20px;width: 40%;float:left;'>";
foreach($result as $key => $value )
{
	echo '<tr><td>'.$value->id.'</td><td>'.$value->email.'</td></tr>';
}
echo "</table>";
}
echo "Temp Mentor From Excel";
$result =$DB->get_records('temp_mentor_ph2_dec');
echo "<table class='table table-striped' style='width:50%;'>";
foreach($result as $key => $value )
{
	echo '<tr><td>'.$value->id.'</td><td>'.$value->email.'</td></tr>';
}
echo "</table>";
echo $OUTPUT->footer();
?>    
