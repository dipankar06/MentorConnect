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
 * @Description: To Create Template of Emails, called in mailconfig.php
*/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

class MailTemplateForm extends moodleform {

    /**
    * Define the form.
    */
    public function definition() {
        global $CFG, $USER, $OUTPUT,$DB;
		$role=array('all'=>'All','mentor'=>'Mentors','incharge'=>'Schools','student'=>'Students');
        $mform = $this->_form; // Don't forget the underscore!
        // Normal fields
	
		//Subject
		$mform->addElement('editor', 'mentor_email_template', 'Mentor (use &lt;name&gt;&lt;username&gt;&lt;password&gt;) ', 'size="20"');	
		$mform->setDefault('mentor_email_template', 'Mentor Mailing Template');
		
		$mform->addElement('editor', 'school_email_template', 'School (use &lt;schoolname&gt;&lt;username&gt;&lt;password&gt;)', 'size="50"');	
		$mform->setDefault('school_email_template', 'School Mailing Template');
		
		$mform->addElement('editor', 'student_email_template', 'Student (use &lt;name&gt;&lt;username&gt;&lt;password&gt;)', 'size="50"');	
		$mform->setDefault('student_email_template', 'Student Mailing Template');
		
		$mform->addElement('editor', 'guest_email_template', 'Guest (use &lt;name&gt;&lt;username&gt;&lt;password&gt;)', 'size="50"');	
		$mform->setDefault('guest_email_template', 'Student Mailing Template');
		//$mform->addRule('guest_email_template', get_string('required'), 'required', null, 'client');
		
		$this->add_action_buttons(false, 'Save');       
    }

    /**
     * Validate the form data.
    *
	*/
    function validation($data, $files) {
        /* global $DB;
        $errors = parent::validation($data, $files);
		if(!empty($data['school_emailid'])){
			if (!filter_var($data['school_emailid'], FILTER_VALIDATE_EMAIL)) {
				$errors['school_emailid'] = "Invalid email format";
			}
		}
        return $errors; */
    }
}
