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

/* @package: block_atl_dashboard
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 12-12-2017
 * @Description: dashboard block
*/

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class newpost_form extends moodleform {
    /**
    * The form definition
    */
    public function definition() {
        global $CFG, $USER, $OUTPUT, $SESSION;

        $mform = $this->_form; // Don't forget the underscore!
		$category = array();
		$forumcatgeory = array();
		$atalArray = get_atalvariables();
		//Student cannot have forum for this category
		$mentorschool_forumid = $atalArray['mentorschool_postcatgeoryid'];
		//Ongoing project discussion can be add only from project discussion page
		$ongoingproject_forumid = $atalArray['ongoingproject_postcatgeoryid'];
		$userrole = get_atalrolenamebyid($USER->msn);
		if(count($SESSION->forumcategoryarray)>0){
			$category = $SESSION->forumcategoryarray;
			foreach($category as $key=>$values){
				if($key==$ongoingproject_forumid){
					continue;
				}
				if($key==$mentorschool_forumid){
					if($userrole!="student"){
						//Student cannot view mentor school category post
						$forumcatgeory[$key] = $values;
					}
				} else{
					$forumcatgeory[$key] = $values;
				}
			}
		}
		unset($category);
		$url = new moodle_url('/forum/wordfilter.php');
		
        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->setDefault('userid', $USER->id);

        // Normal fields
		$mform->addElement('select', 'frmcategory', 'Category', $forumcatgeory);
		$mform->addRule('frmcategory', get_string('required'), 'required', null, 'client');
		
        $mform->addElement('text', 'title', 'Post Title', 'size="50" maxlength="280"');
        $mform->addRule('title', get_string('required'), 'required');
        $mform->setType('title', PARAM_TEXT);

        $mform->addElement('textarea', 'detail', 'Description', 'wrap="virtual" rows="6" cols="50" maxlength="400"');
        $mform->addRule('detail', get_string('required'), 'required');
        $mform->setType('detail', PARAM_TEXT);

		
		$mform->addElement('filepicker', 'postfile','File', null,array('maxbytes' => $maxbytes, 'accepted_types' => 'jpg,png'));
		
        $mform->addElement('hidden', 'flag', 'post');
        $mform->addElement('hidden', 'isforumflag', 'y');
		$mform->addElement('hidden', 'forumlocation', 'dashboard');
		$mform->addElement('hidden', 'forumfilterpath', $url);
        //$this->add_action_buttons(false, get_string('savechanges'));
		$mform->addElement('button', 'saveforum', get_string('savechanges'),array("style"=>"background-color: #007473; border-color: #007473;  color: #fff;"));

        $mform->addElement('html', '<div>Post can be any text content posted to Forum. It can be an Project idea or some important information etc.</div>');
    }

}
