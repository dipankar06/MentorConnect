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
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 24-07-2018
 * @Description: Ticketing System Create New Ticket Page
*/

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class ticketform extends moodleform {
    
	protected $course;
    protected $context;
	
	/**
    * The form definition
    */
    public function definition() {
        global $CFG, $USER, $OUTPUT;

        $mform = $this->_form; // Don't forget the underscore!
		$id = $this->_customdata['ticketid']; // this contains the data of this form
		$ticketcategory = $this->_customdata['category'];

        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->setDefault('userid', $USER->id);

        // Normal fields
		$mform->addElement('select', 'category', 'Problem Category', $ticketcategory);
		$mform->addRule('category', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'name', 'Title','maxlength="100" size="51"');
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

		$mform->addElement('textarea','description', 'Details', 'wrap="virtual" rows="6" cols="50" maxlength="250"');
		$mform->addRule('description', get_string('required'), 'required', null, 'client');
        $mform->setType('description', PARAM_TEXT);

        $mform->addElement('hidden', 'id', $id);
		$mform->addElement('hidden', 'isticketflag', 'y');
        $this->add_action_buttons(false, get_string('submit'));

		$msg = '<br><h6> Submit your query, problems or issue your are facing in this portal as a Ticket.</h6>';
		$mform->addElement('static', 'message','', $msg);

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
		// Ticket Category Validation
		if (empty($data['category'])) {
			$errors['category'] = "Please Select a Ticket Category";
		}
        return $errors;
    }
}
?>