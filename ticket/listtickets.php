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

/* @package: core_ticket
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 18-07-2018
 * @Description: list of tickets
*/

require_once('../config.php'); //Include Global Cofiguration file
require_once('render.php'); //include create folder render file
require_once('lib.php'); //include create folder library file
require_once($CFG->libdir.'/filelib.php'); //Include global library
require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}
$userrole = get_atalrolenamebyid($USER->msn);
$PAGE->set_url('/create/listtickets.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Mentor Connect : List Support');
$strmessages = "List ATAL Schools";
echo $OUTPUT->header();
//echo $OUTPUT->heading($strmessages);
if (!is_siteadmin()){
	if($userrole!=="admin"){
		redirect($CFG->wwwroot.'/my');
	}
}
$renderobj =  new CreateStackholders();
$rendercontent = $renderobj->rendercreatetab_school();
echo $rendercontent;
echo $OUTPUT->footer();
?>
<script type="text/javascript">
require(['jquery'], function($) {

});
</script>
