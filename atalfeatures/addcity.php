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

/* @package core_project
 * @CreatedBy:Jothi (IBM)
 * @CreatedOn:23-04-2018
 * @Description: add city for schools.
*/

require_once('../config.php');
require_once('render.php');
require_once($CFG->dirroot.'/lib/formslib.php');
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
$PAGE->set_url('/atalfeatures/addcity.php');
$show_form_status=false;
//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Add New City ";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("Add New City");
echo $OUTPUT->header();
//$formobj = new AddNewCity();
class AddNewCity extends moodleform {
    /**
    * The form definition
    */
    public function definition() {
        global $CFG, $USER, $OUTPUT,$DB;
		$mform = $this->_form; // Don't forget the underscore!
		$state = array();
		$state['select_state']='Select State';
		$state_records = get_atal_allstates();
		foreach($state_records as $key=>$values)
		{
			$state[$key] = $values->name;
		}
		$mform->addElement('select', 'state', 'State', $state);
		$mform->addRule('state', get_string('required'), 'required', null, 'client');
		$mform->addElement('text', 'city', 'City', 'size="30",maxlength="50"');	
		$mform->addRule('city', get_string('required'), 'required', null, 'client');
		$this->add_action_buttons(false, 'Add City');
    }
	function validation($data, $files) {
		global $DB,$USER;
		$errors = parent::validation($data, $files);
		if(empty($data['state'])||$data['state']=='select_state'){
			$errors['state'] = "Select State";
		}
		if(!empty($data['city'])){
					$result = $DB->get_record('city',array('name'=>$data['city'],'stateid'=>$data['state']),'id');
					if($result)
						$errors['city'] = "City Already Exists!";
		}
		return $errors;
    }
}
$formobj = new AddNewCity();
if ($formobj->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
	// redirect($returnurl);
} else if ($data = $formobj->get_data()) {
	$citydata = new StdClass();
	$citydata->name= ucwords(strtolower($data->city));
	$citydata->stateid= $data->state;
	$insertid = $DB->insert_record('city',$citydata);
	$show_form_status=true;
} 
$alert_box='';
if($show_form_status)
{
	$alert_box='<div class="alert alert-success">
				  <strong>Added Successfully! </strong><button class="close" type="button" data-dismiss="alert">×</button></a>
		</div>';
}
echo $alert_box;

echo $content = $formobj->render();

echo $OUTPUT->footer();
?>



<script type="text/javascript">
require(['jquery'], function($) {
});
</script>

