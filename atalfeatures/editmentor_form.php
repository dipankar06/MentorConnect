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
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 02-03-2018
 * @Description: This form is use in nitiadmin mentor create and mentor first time Login
*/

defined('MOODLE_INTERNAL') || die();


//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class mentor_update_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG, $USER, $OUTPUT,$DB;
		$userrole = get_atalrolenamebyid($USER->msn);
        $mform = $this->_form; // Don't forget the underscore!
		//$this->_formname = "Mentor Update Profile Form";
		$status = $this->_customdata['status']; 
		$firstlogin = $this->_customdata['firstlogin']; 
		$state = $city = array();
		$city['select_city'] = "Select District";
		$state = stateForMoodleForm();	
		$yearofcomplete = array();
		$currentyear = date("Y")+5;
		for ($i=1970;$i<=$currentyear;$i++){
			$yearofcomplete[$i]=$i;
		}
		$language = get_languages();
		$gender = array(''=>'Select Gender','m'=>'Male','f'=>'Female','t'=>'Third_Gender/Transgender');
		$timecommit = get_timecommit();
				
		$filemanageroptions =  array('maxfiles' => 1,'maxbytes' => 1048576,'subdirs' => 0,'accepted_types' => 'jpg,jpeg,png');
		
		//HereAbout Mentor Select Option
		$hearaboutmentor = array('Twitter feed of one of the brand ambassadors'=>'Twitter feed of one of the brand ambassadors','Promotion by OLA Cabs'=>'Promotion by OLA Cabs','Friends / Colleagues / Relatives'=>'Friends / Colleagues / Relatives','Google Search'=>'Google Search','Word of Mouth'=>'Word of Mouth','Network Capital'=>'Network Capital','Newspaper / Online Media articles'=>'Newspaper / Online Media articles','tGELF'=>'tGELF','Other'=>'Other');
		// Moodle Form Starts Here
		$mform->addElement('header', 'moodle', 'Personal Information');
		//First Name
        $mform->addElement('text', 'name', 'First Name', 'size="50",maxlength="100"');
		$mform->addRule('name', get_string('required'), 'required', null, 'client');
		//Last Name
		$mform->addElement('text', 'lastname', 'Last Name', 'size="50",maxlength="100"');
		$mform->addRule('lastname', get_string('required'), 'required', null, 'client');
		//Email
		$mform->addElement('text', 'email', 'Email', 'size="50",maxlength="100"');
		$mform->addRule('email', get_string('required'), 'required', null, 'client');
		$mform->addElement('html', '<div style="margin-left: 26%; padding: 2px 0px 0px 0;color:red;">Your Email Address is the Username</div>');
		//DOB
		$mform->addElement('text', 'dob', 'Date Of Birth', 'size="25",maxlength="10"');
		if($userrole=='mentor')
			$mform->addRule('dob', get_string('required'), 'required', null, 'client');
		$mform->addElement('text', 'aadhar_no', 'Aadhar Number', 'size="25",maxlength="12"');
		//Gender
		$mform->addElement('select', 'gender', 'Gender', $gender);
		if($userrole=='mentor')
		$mform->addRule('gender', get_string('required'), 'required', null, 'client');
		//State
		$mform->addElement('select', 'state', 'State', $state);
		$mform->addRule('state', get_string('required'), 'required', null, 'client');
		//City
		$mform->addElement('select', 'cityid', 'District', $city);
		$mform->addRule('cityid', get_string('required'), 'required', null, 'client');
		//LinkedIn URL
		$mform->addElement('text', 'linkdlnurl', 'LinkedIn URL', 'size="50",maxlength="200"');
		//Facebook URL
		$mform->addElement('text', 'fburl', 'Facebook URL', 'size="50",maxlength="300"');
		//Contact Number
		$mform->addElement('text', 'mobileno', 'Mobile', 'size="50",maxlength="10"');
		if($userrole=='mentor')
			$mform->addRule('mobileno', get_string('required'), 'required', null, 'client');
		
		//Govt Photo ID
		if($userrole=='mentor'){
			$description = "Please upload a scanned image of any government issued ID (except PAN card).";
			$mform->addElement('filemanager', 'govtphotoid','Govt Photo ID', null, $filemanageroptions);
			$mform->addRule('govtphotoid', get_string('required'), 'required', null, 'client');
			$mform->addElement('static', 'descriptiontext', '',$description);
		}
		
		$mform->closeHeaderBefore('degree');
		$mform->addElement('header', 'moodle', 'Educational Background');
		//Highest Degree
		$mform->addElement('text', 'degree', 'Highest Degree', 'size="50",maxlength="200"');
		if($userrole=='mentor')
			$mform->addRule('degree', get_string('required'), 'required', null, 'client');
		//Area of Specialization
		$mform->addElement('text', 'specialization', 'Area of Specialization', 'size="50"');
		if($userrole=='mentor')
			$mform->addRule('specialization', get_string('required'), 'required', null, 'client');
		//Educational Institute
		$mform->addElement('text', 'educationinstitute', 'Educational Institute', 'size="50",maxlength="200"');
		if($userrole=='mentor')
			$mform->addRule('educationinstitute', get_string('required'), 'required', null, 'client');
		//Year Of Completion
		$mform->addElement('select', 'yearofcomplete', 'Year Of Completion', $yearofcomplete);
		if($userrole=='mentor')
			$mform->addRule('yearofcomplete', get_string('required'), 'required', null, 'client');
		//Languages Known
		$mform->addElement('select', 'language', 'Languages Known', $language);
		if($userrole=='mentor')
			$mform->addRule('language', get_string('required'), 'required', null, 'client');
		$mform->getElement('language')->setMultiple(true);
		$mform->closeHeaderBefore('professionalsummary');
		$mform->addElement('header', 'moodle', 'Professional Experience');
		//Professional Summary
		$mform->addElement('textarea', 'professionalsummary','Professional Summary', 'wrap="virtual" ,rows="5" cols="50"');
		// Registered As
		$radioarray=array();
		$radioarray[] = $mform->createElement('radio', 'registerstat', '','Individual', 'individual');
		$radioarray[] = $mform->createElement('radio', 'registerstat', '', 'Corporate', 'corporate');
		$mform->setDefault('registerstat', 'individual');
		$mform->addGroup($radioarray, 'radioar', 'Registered As', array(''), false);
		//Possible Areas of Interventation (Tags)
		$mform->addElement('textarea', 'areaofinterventation','Possible Areas of Interventation (Tags)', 'wrap="virtual" ,rows="5" cols="50"');
		if($userrole=='mentor')
			$mform->addRule('areaofinterventation', get_string('required'), 'required', null, 'client');
		$mform->closeHeaderBefore('timecommit');
		//Details For Mentor India
		$mform->addElement('header', 'moodle', 'Details For Mentor India');		
		$mform->addElement('select', 'timecommit', 'Time Commitment per Week', $timecommit);		
		if($firstlogin){
			$mform->addElement('header', 'moodle', 'School Details');
			$mform->addElement('text', 'schoolname', 'Name', '"readonly",size="50",maxlength="100"');
			$mform->addElement('text', 'schooladdress', 'Address', '"readonly",size="50",maxlength="100"');
			$mform->addElement('text', 'schoolcontactname', "Contact Person's Name", '"readonly",size="50",maxlength="100"');
			$mform->addElement('text', 'schoolcontactphone', "Contact Person's Phone Number", '"readonly",size="50",maxlength="100"');
			$mform->addElement('text', 'schoolcontactemail', "Contact Person's Email", '"readonly",size="50",maxlength="100"');
		}
		$mform->addElement('header', 'moodle', 'Reference Check');
		//Reference Name
		$mform->addElement('text', 'refree1_name', 'Reference1 Name', 'size="50",maxlength="100"');
		//Reference Contact
		$mform->addElement('text', 'refree1_contact', 'Reference1 Mobile', 'size="50",maxlength="10"');
		//Reference Email
		$mform->addElement('text', 'refree1_email', 'Reference1 Email', 'size="50",maxlength="200"');
		//Reference1 How do you know them ?
		$mform->addElement('text', 'refree1_know', 'How do you know them ?', 'size="50",maxlength="200"');
		//Reference2 Name
		$mform->addElement('text', 'refree2_name', 'Reference2 Name', 'size="50",maxlength="100"');
		//Reference Contact
		$mform->addElement('text', 'refree2_contact', 'Reference2 Mobile', 'size="50",maxlength="10"');
		//Reference Email
		$mform->addElement('text', 'refree2_email', 'Reference2 Email', 'size="50",maxlength="200"');
		//Reference How do you know them ?
		$mform->addElement('text', 'refree2_know', 'How do you know them ?', 'size="50",maxlength="200"');
		$mform->addElement('select', 'hearaboutmentor', 'Where did you first hear about Mentor India?', $hearaboutmentor,'max-width="50%"');
		if($userrole=='mentor')
		{
			//$mform->addRule('aadhar_no', get_string('required'), 'required', null, 'client');	
			$mform->addRule('refree1_name', get_string('required'), 'required', null, 'client');
			$mform->addRule('refree1_contact', get_string('required'), 'required', null, 'client');
			$mform->addRule('refree1_email', get_string('required'), 'required', null, 'client');
			$mform->addRule('refree1_know', get_string('required'), 'required', null, 'client');
			$mform->addRule('refree2_name', get_string('required'), 'required', null, 'client');
			$mform->addRule('refree2_contact', get_string('required'), 'required', null, 'client');
			$mform->addRule('refree2_email', get_string('required'), 'required', null, 'client');
			$mform->addRule('refree2_know', get_string('required'), 'required', null, 'client');
		}
		$mform->addElement('hidden', 'city');
		$mform->addElement('hidden', 'currentemail');
		$buttonarray=array();
		$btntxt='Update Profile';
		if($status=='new')
			$btntxt='Create Profile';
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', $btntxt);
		$buttonarray[] = &$mform->createElement('cancel');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		
    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     *
	*/
    function validation($data, $files) {
		global $DB,$USER;
		$errors = parent::validation($data, $files);
		if(empty($data['state'])||$data['state']=='select_state'){
			$errors['state'] = "Select State";
		}
		if(!check_only_characters($data['name']))
		{
			$errors['name'] = "Use Only Characters";
		}
		if(!check_only_characters($data['lastname']))
		{
			$errors['lastname'] = "Use Only Characters";
		}
		if(!empty($data['refree1_name'])){
			if(!check_only_characters($data['refree1_name']))
			{
				$errors['refree1_name'] = "Use Only Characters";
			}
		}
		if(!empty($data['refree2_name'])){
			if(!check_only_characters($data['refree2_name']))
			{
				$errors['refree2_name'] = "Use Only Characters";
			}
		}
		if(!empty($data['aadhar_no'])){
			if(!check_is_number($data['aadhar_no']))
			{
				$errors['aadhar_no'] = "Please Use only Numbers";
			}
			if(strlen(trim($data['aadhar_no']))!=12)
				$errors['aadhar_no'] = "Please Give 12 Digit Aadhar Number";
		}
		if(!empty($data['mobileno'])){
			if(!check_is_number($data['mobileno']))
			{
				$errors['mobileno'] = "Please Use only Numbers";
			}
			if(!mobile_number_length($data['mobileno']))
			{
				$errors['mobileno'] = "Please Give 10 Digit Mobile Number";
			}	
		}
		if(!empty($data['refree1_contact'])){
			if(!check_is_number($data['refree1_contact']))
			{
				$errors['refree1_contact'] = "Please Use only Numbers";
			}
			if(!mobile_number_length($data['refree1_contact']))
			{
				$errors['refree1_contact'] = "Please Give 10 Digit Mobile Number";
			}	
		}
		if(!empty($data['refree2_contact'])){
			if(!check_is_number($data['refree2_contact']))
			{
				$errors['refree2_contact'] = "Please Use only Numbers";
			}
			if(!mobile_number_length($data['refree2_contact']))
			{
				$errors['refree2_contact'] = "Please Give 10 Digit Mobile Number";
			}
		}
		if(empty($data['city'])){
			$errors['cityid'] = "Select District";
		}
		if(!empty($data['email'])){
			if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
				$errors['email'] = "Invalid email format";
			}
		}
		if(!empty($data['email'])){

			if($data['submitbutton']=='Update Profile')
			{
				if($data['email']!=$data['currentemail'])
				{
				$emailrecord = $DB->get_record('user',array('email'=>$data['email']),'email');
				if(isset($emailrecord->email))
					$errors['email'] = "Email Id Already Exists!";
				}
			}
			else
			{
				$emailrecord = $DB->get_record('user',array('email'=>$data['email']),'email,msn');
				if(isset($emailrecord->email) && $emailrecord->msn==4)
					$errors['email'] = "Email Id Already Exists!";
			}
		}
		if(!empty($data['refree1_email'])){
			if (!filter_var($data['refree1_email'], FILTER_VALIDATE_EMAIL)) {
				$errors['refree1_email'] = "Invalid email format";
			}
		}
		if(!empty($data['refree2_email'])){
			if (!filter_var($data['refree2_email'], FILTER_VALIDATE_EMAIL)) {
				$errors['refree2_email'] = "Invalid email format";
			}
		}
		  return $errors;
    }
}