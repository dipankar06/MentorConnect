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
 * @CreatedOn: 08-04-2018
 * @Description: Create Event Form
*/

defined('MOODLE_INTERNAL') || die();


//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class atalevent_form extends moodleform {
    /**
    * The form definition
    */
    public function definition() {
        global $CFG, $USER, $OUTPUT;
		$eventid = $this->_customdata['id'];
		$hasduration = false;
        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->setDefault('userid', $USER->id);
	
        // Normal fields
        $mform->addElement('text', 'name', get_string('eventname','calendar'), 'size="50" maxlength="30"');
        $mform->addRule('name', get_string('required'), 'required');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('textarea', 'description', get_string('eventdescription','calendar'), 'wrap="virtual" rows="6" cols="50" maxlength="100"');
        $mform->addRule('description', get_string('required'), 'required');
        $mform->setType('description', PARAM_TEXT);
		$curYear = date('Y'); 
		$timeperiod = array('startyear' =>  $curYear, 'stopyear'  =>  ($curYear+10));
        $mform->addElement('date_time_selector', 'timestart', get_string('date'),$timeperiod);        

        $group = array();
        $group[] =& $mform->createElement('radio', 'duration', null,' Without duration (1 Day)', 0);
        $group[] =& $mform->createElement('radio', 'duration', null, get_string('durationuntil', 'calendar'), 1);
       // $group[] =& $mform->createElement('date_time_selector', 'timedurationuntil', '');
        $mform->addGroup($group, 'durationgroup', '', '<br />', false);
		$timeuntilgroup = array();
		$timeuntilgroup[] = & $mform->createElement('date_time_selector','timedurationuntil', '',$timeperiod);
		$mform->addGroup($timeuntilgroup, 'timeuntilgroup', '', '<br />', false);
		
        $mform->disabledIf('timedurationuntil','duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[day]',    'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[month]',  'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[year]',   'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[hour]',   'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[minute]', 'duration', 'noteq', 1);

        $mform->setDefault('duration', ($hasduration)?1:0);

		$mform->addElement('header', 'addfile', 'Add Event Image');
		if(!$eventid){
			$mform->addElement('filepicker', 'postfile','Event Image', null, array('maxfiles'=>1,'maxbytes' => $maxbytes, 'accepted_types' => 'jpg,png,jpeg'));
		} else{
			$mform->addElement('filemanager', 'postfile','Event Image', null, array('maxfiles'=>1,'maxbytes' => $maxbytes, 'accepted_types' => 'jpg,png,jpeg'));
			//$mform->addElement('filepicker', 'postfile','Event Image', null, array('maxbytes' => $maxbytes, 'accepted_types' => 'jpg,png,jpeg'));
		}
		
		$mform->addElement('hidden', 'id');
		if ($eventid) {
			$mform->addElement('hidden', 'flag', 'edit');
			$mform->addElement('hidden', 'parentid', $this->_customdata['parentid']);
			//$mform->addElement('hidden', 'parentid');
			$mform->setType('parentid', PARAM_INT);
			$btnstring = 'Update Event';
        } else {
            $btnstring = 'Add Event';
			$mform->addElement('hidden', 'flag', 'add');
        }
		$buttonarray=array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', $btnstring);
		$buttonarray[] = &$mform->createElement('cancel');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		//$mform->addElement('button', 'savemyevent', get_string('savechanges'),array("style"=>"background-color: #007473; border-color: #007473;  color: #fff;"));
    }
	/*
	* Validate the form data.
	* @param array $eventdata
	* @return array|bool
	*
	*/
	function validation($data, $files) {		
		global $DB;
		$errors = parent::validation($data, $files);
		$timestamp = time() - (time() % 3600); //Time Without Minutes
		//Check only for Create Event
		if(!$data['id'] ){
			if ($data['timestart'] <= $timestamp) {
				$errors['timestart'] ='Event Start Date Must be in the Future/Today';
			}
		}
		// Check only if Event has the Duration
		if(!$data['duration'] ){ 
			if ($data['timestart'] <= $timestamp) {
				$errors['timestart'] ='Event Start Date Must be in the Future/Today';
			}
		}
		if($data['duration']){
			if(!empty($data['timedurationuntil'])){
				if ($data['timedurationuntil'] < $data['timestart']) {
					$errors['timeuntilgroup']='Event End Date Should be Greater than Event Start Date';
				}
			}
		}
		return $errors;		
	}
}
