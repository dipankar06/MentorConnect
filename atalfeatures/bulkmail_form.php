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
 * @CreatedOn: 07-02-2018
 * @Description: To Sent WelcomeMail for Launch, called in mail.php
*/

defined('MOODLE_INTERNAL') || die();


//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class CreateMailForm extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG, $USER, $OUTPUT,$DB;
		$criteria_mentor = getBulkMailReport_Dropdown('mentor');
		//$role=array('all'=>'All','mentor'=>'Mentors','incharge'=>'Schools','student'=>'Students');
		$role=array('mentor'=>'Mentors','incharge'=>'Schools','aschool'=>'Schools with Assigned Mentors');
        $mform = $this->_form; // Don't forget the underscore!
        // Normal fields
		//Role
		$mform->addElement('select', 'role', 'Send To ', $role);
		$mform->addRule('role', get_string('required'), 'required', null, 'client');
		
		$mform->addElement('checkbox', 'ccyes', 'Add Assigned Mentor\'s in Mail CC ');
		$mform->setDefault('ccyes', '0');
		$mform->disabledIf('ccyes','role', 'noteq', 'aschool');
		//Criteria
		$mform->addElement('select', 'criteria', 'Criteria ', $criteria_mentor);
		$mform->addRule('criteria', get_string('required'), 'required', null, 'client');
		
		//Subject
		$mform->addElement('text', 'subject', 'Subject', 'size="50",maxlength="200"');	
		$mform->setDefault('subject', 'Welcome To ATLInnonet');
		$mform->addRule('subject', get_string('required'), 'required', null, 'client');

		$mform->addElement('html', '<div style=" font-size: 0.85rem !important; margin-left: 26%; padding: 5px 0px 0px 0;">Please use only the below placeholders in Mail Body </br>Mentor:  <span style="font-style: italic;">&lt;username&gt; &lt;password&gt; &lt;firstname&gt; &lt;lastname&gt; &lt;allotedschoolname&gt; &lt;allotedschoolcode&gt; </span></br>School: <span style="font-style: italic;">&lt;username&gt; &lt;password&gt; &lt;schoolname&gt; &lt;schoolcode&gt; </span></br>Student : <span style="font-style: italic;">&lt;username&gt; &lt;password&gt; &lt;firstname&gt; &lt;lastname&gt; &lt;schoolname&gt; </span></div>');
		//Mail Body
		$mform->addElement('editor', 'mailbody', get_string('mailbody'), 'size="50"');	
		$mform->addRule('mailbody', get_string('required'), 'required', null, 'client');
		$mform->addElement('hidden', 'criteria_text');
		$mform->addHelpButton('mailbody', 'mailbody');
		
		// Registered As
		$radioarray=array();
		$radioarray[] = $mform->createElement('radio', 'mailmode', '','Test Mail', 'test');
		$radioarray[] = $mform->createElement('radio', 'mailmode', '', 'Bulk Mail', 'bulk');
		$mform->setDefault('mailmode', 'test');
		$mform->addGroup($radioarray, 'radioar', '', array(''), false);
		
		$mform->addElement('text', 'email_test', '', 'size="50",maxlength="200",placeholder="Email Address to Trigger Test Mail"');	
		$mform->disabledIf('email_test','mailmode', 'noteq', 'test');
        $this->add_action_buttons(false, 'Send Mail');       
    }

    /**
     * Validate the form data.
    *
	*/
    function validation($data, $files) {
      $errors = parent::validation($data, $files);
	  if($data['mailmode']=='test' && trim($data['email_test'])=='')
		  $errors['email_test'] = "Please Provide Email id to which the Test Mail should be Triggerd.";
		/*if($data['criteria_text']=='no_mentor' || $data['criteria_text']=='no_school'  ){
				$errors['criteria'] = "Please Select the Criteria";
			}*/
        return $errors;
    }
}
