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

/*
 * Atal Dashboard Block caps.
 * @package: block_atl_dashboard
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 12-12-2017
 * @Description: Plugin main file
*/

defined('MOODLE_INTERNAL') || die();

include_once(__DIR__ .'/lib.php');

class block_atl_dashboard extends block_base {

    /**
    * Initialises the block.
    *
    * @return void
    */
    protected $properties = null;

    public function init() {
        $this->title = ''; //get_string('pluginname', 'block_atl_dashboard'); //This will get display in Dashboard Page
		$this->add_infotosession();
    }

    /**
    * Gets the block contents.
    *
    * If we can avoid it better not check the server status here as connecting
    * to the server will slow down the whole page load.
    *
    * @return string The block HTML.     
	*/
    public function get_content() {
        global $OUTPUT;
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = ''; //Footer here...
		
		if (isloggedin() && !is_siteadmin()) {
			// Show data only to atal users.
			$this->content->text = showatlforumfeed();
		}
        return $this->content;
    }
	
	//As Dashboard Loads First, so Add some important session values, for this Platform
	private function add_infotosession(){
		global $DB, $USER, $SESSION;
		$atalvariable = get_atalvariables();
		$role = get_atalrolenamebyid($USER->msn);
		$showschool = ($role=='admin' || $role=='mentor')?false:true;
		if(!empty($USER->msn) && !isset($SESSION->sitecourseid)){
			$data = $DB->get_record('course', array('idnumber'=>$atalvariable['sitecourse_idnumber']), $fields='id');
			$SESSION->sitecourseid = $data->id;
			if($showschool===true){				
				$schooldata = $DB->get_record('user_school', array('userid'=>$USER->id), '*');
				$schoolid = (isset($schooldata->schoolid))?$schooldata->schoolid:0;
				$SESSION->schoolid = $schoolid;
			}
		}
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
		//Session to check for Atal incharge student post lists.
		$SESSION->studentpostforapproval = 0;
		//Session to check for NitiAdmin Misuse Report Tab
		$SESSION->misusepostlist = 0;
	}
	
}
