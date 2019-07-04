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
 * @CreatedOn: 09-01-2018
 * @Description: DProject Module - Create a New Project.
*/

defined('MOODLE_INTERNAL') || die();


//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class add_projectform extends moodleform {
    
	protected $course;
    protected $context;
	
	/**
    * The form definition
    */
    public function definition() {
        global $CFG, $USER, $OUTPUT;

        $mform = $this->_form; // Don't forget the underscore!
		$course        = $this->_customdata['course']; // this contains the data of this form
        $category      = $this->_customdata['category'];
        $editoroptions = $this->_customdata['editoroptions'];
		$mentorlist = $this->_customdata['mentorlist'];
		$studentlist = $this->_customdata['studentlist'];
		if (!empty($course->id)) {
            $coursecontext = context_course::instance($course->id);
            $context = $coursecontext;
        } else {
            $coursecontext = null;
            $context = $categorycontext;
        }

        $courseconfig = get_config('moodlecourse');

        $this->course  = $course;
        $this->context = $context;
		
        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->setDefault('userid', $USER->id);

        // Normal fields
        $mform->addElement('text', 'fullname', 'Title','maxlength="100" size="50"');
        $mform->addRule('fullname', get_string('required'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_TEXT);
		
		$mform->addElement('text', 'shortname', 'ShortName', 'maxlength="40" size="20"');       
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_TEXT);        
		
        $mform->addElement('textarea','summary_editor', 'Details', 'wrap="virtual" rows="6" cols="50" maxlength="400"');        
        $mform->setType('summary_editor', PARAM_TEXT);
        
		//$mform->addElement('date_selector', 'startdate', 'Start date');        
        //$mform->setDefault('startdate', time() + 3600 * 1);

        //$mform->addElement('filepicker', 'postfile','File', null,array('maxbytes' => $maxbytes, 'accepted_types' => 'jpg,png'));
		$summaryfields = 'summary_editor';
		if ($overviewfilesoptions = course_overviewfiles_options($course)) {
            $mform->addElement('filemanager', 'overviewfiles_filemanager', 'Innovation Image', null, $overviewfilesoptions);
            //$mform->addHelpButton('overviewfiles_filemanager', 'courseoverviewfiles');
            $summaryfields .= ',overviewfiles_filemanager';
        }
		
		$mform->addElement('select', 'selmentor', 'Mentor', $mentorlist);
		$mform->getElement('selmentor')->setMultiple(true);
		$mform->addElement('select', 'selstudent', 'Student', $studentlist);
		$mform->getElement('selstudent')->setMultiple(true);
		
		$mentormsg = '<h6> Press CTRL and click on student or Mentor name to select multiple students or mentors</h6>';
		$mform->addElement('static', 'message','', $mentormsg);
		
        $mform->addElement('text', 'tags', 'Tags', 'size="50" maxlength="80" placeholder="Example #Robotics #waterfilter .   Each tags separated by # "');        
		
        $mform->addElement('hidden', 'flag', 'add');
		$mform->addElement('hidden', 'courseid');
		$mform->setDefault('courseid', 0);
        $this->add_action_buttons(false, get_string('savechanges'));		

    }
	
	/**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        // Add field validation check for duplicate shortname.
        if ($course = $DB->get_record('course', array('shortname' => $data['shortname']), '*', IGNORE_MULTIPLE)) {
            if (empty($data['id']) || $course->id != $data['id']) {
                $errors['shortname'] = "Short name already exists";
            }
        }
        return $errors;
    }

}
