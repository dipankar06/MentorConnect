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

/* @package: core_nitiadmin
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 07-02-2018
 * @Description: To Sent WelcomeMail for Launch, called in mail.php
*/

defined('MOODLE_INTERNAL') || die();


//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');
class CreateSettingsForm extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG, $USER, $OUTPUT,$DB;
        $mform = $this->_form; // Don't forget the underscore!
		$keyid = $this->_customdata['id'];
		// Mail to Address
		//Subject
		
		if($keyid)
			$mform->addElement('text', 'atl_key', 'Settings Key', 'readonly size="50",maxlength="200"');	
		else
			$mform->addElement('text', 'atl_key', 'Settings Key', 'size="50",maxlength="200"');	
		$mform->addElement('text', 'atl_value', 'Settings Value', 'size="50",maxlength="200"');	
		if(!$keyid)
		{
			$this->add_action_buttons(false, 'Add New Settings');     
			$mform->addElement('hidden', 'flag', 'add');
		}
		else
		{
			$this->add_action_buttons(false, 'Update Settings');  
			$mform->addElement('hidden', 'flag', 'edit');	
			$mform->addElement('hidden', 'id', $keyid);					
		}
		$mform->addRule('atl_key', get_string('required'), 'required');
		$mform->addRule('atl_value', get_string('required'), 'required');
    }

    /**
     * Validate the form data.
    *
	*/
    function validation($data, $files) {
		global $DB;
       $errors = parent::validation($data, $files);
	   if(!empty($data['atl_key']) && $data['flag']=='add')
	   {
		$result = $DB->get_record("custom_settings",array('atl_key'=>$data['atl_key']));
		if($result)
			$errors['atl_key'] = "Key Already Exists!";
	   }
        return $errors;
    }
}
if($id==0){
	$heading = 'Add New Key';
	$PAGE->set_title('ATL Innonet : Add New Key');
	$action_url = $CFG->wwwroot.'/nitiadmin/settings.php';
} else{
	$PAGE->set_title('ATL Innonet : Update Key');
	$heading = 'Update Key';
	$action_url = $CFG->wwwroot.'/nitiadmin/settings.php?id='.$id;
}
$formobj = new CreateSettingsForm($action_url,array('id' => $id));
$custom_settings = getCustomSettings();
if($id)
{
	$populateform_obj = new StdClass();
	$result = $DB->get_record("custom_settings",array('id'=>$id));
	if($result)
	{
		$populateform_obj->atl_key =$result->atl_key;
		$populateform_obj->atl_value =$result->atl_value;
		$populateform_obj->id =$result->id;
		
	}
	$formobj->set_data($populateform_obj);
}

if ($formobj->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
	// redirect($returnurl);
} 
else if ($data = $formobj->get_data()) {
		try
		{
			if($data->flag=="add")
				$status = insert_settings($data);
			else
				$status = update_settings($data);
		if($status)
			$show_form_status=true;
		}
		catch(Exception $e)
		{
			echo $e->getMessage();die;
		}
} 
function insert_settings($data)
{
	global $DB;
	$status = false;
	$dataObj = new StdClass();
	$dataObj->atl_key = $data->atl_key;
	$dataObj->atl_value = $data->atl_value;
	$result = $DB->insert_record("custom_settings",$dataObj);
	if($result)
		$status = true;
	return $status;
}
function update_settings($data)
{
	global $DB;
	$status = false;
	$dataObj = new StdClass();
	$dataObj->id = $data->id;
	$dataObj->atl_value = $data->atl_value;
	$result = $DB->update_record("custom_settings",$dataObj);
	if($result)
		$status = true;
	return $status;
}