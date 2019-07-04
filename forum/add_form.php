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

/* @package: core_forum
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 15-12-2017
 * @Description: Collatoration Forum .
*/

defined('MOODLE_INTERNAL') || die();


//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class add_form extends moodleform {
    /**
    * The form definition
    */
    public function definition() {
        global $CFG, $USER, $OUTPUT, $SESSION, $DB;

        $mform = $this->_form; // Don't forget the underscore!
		$category = array();
		$forumcatgeory = array();
		$atalArray = get_atalvariables();
		//Student cannot have forum for this category
		$mentorschool_forumid = $atalArray['mentorschool_postcatgeoryid'];
		//Ongoing project discussion can be add only from project discussion page(Private-chat)
		$ongoingproject_forumid = $atalArray['ongoingproject_postcatgeoryid'];
		$userrole = get_atalrolenamebyid($USER->msn);		
		//add forum category in session
		if(!isset($SESSION->forumcategoryarray)){
			$category_array = array();
			$data = $DB->get_records('forum_category', array(), $fields='id,name');
			if(count($data)>0){
				foreach($data as $keys=>$values){
				$category_array[$values->id] = $values->name;
				}
			}
			$SESSION->forumcategoryarray = $category_array;
		}
		if(count($SESSION->forumcategoryarray)>0)
		{
			$category = $SESSION->forumcategoryarray;
			foreach($category as $key=>$values){
				if($key==$ongoingproject_forumid){
					continue;
				}
				if($key==$mentorschool_forumid){
					if($userrole!="student"){
						$forumcatgeory[$key] = $values;
					}
				} else{
					$forumcatgeory[$key] = $values;
				}
			}
		}
		unset($category);
        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->setDefault('userid', $USER->id);
		
        // Normal fields
		$mform->addElement('select', 'frmcategory', 'Category', $forumcatgeory);
		$mform->addRule('frmcategory', get_string('required'), 'required', null, 'client');
		
        $mform->addElement('text', 'name', 'Title', 'size="100" maxlength="280"');
        $mform->addRule('name', get_string('required'), 'required');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('textarea', 'message', 'Description', 'wrap="virtual" rows="6" cols="100" maxlength="5000"');
        $mform->addRule('message', get_string('required'), 'required');
        $mform->setType('message', PARAM_TEXT);

        $mform->addElement('filepicker', 'postfile','File', null, array('maxbytes' => $maxbytes, 'accepted_types' => 'jpg,png'));

        $mform->addElement('hidden', 'discussionid', '0');
		$mform->addElement('hidden', 'flag', 'post');
        $mform->addElement('hidden', 'isforumflag', 'y');
		$mform->addElement('hidden', 'forumlocation', 'forum');
        $this->add_action_buttons(false, get_string('savechanges'));

    }
	
	/**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     */
    function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);	
		if ($data['name']=="test") {
			$errors['name'] = "Please Enter name";
		}
        return $errors;
    }

}
