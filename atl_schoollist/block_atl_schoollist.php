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
 * Atal School List Block caps.
 * @package:  block_atl_schoollist
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 08-12-2017
 * @Description: main plugin file.
	will display List of schools rank wise at Niti-Admin Dashboard Right side.
*/

defined('MOODLE_INTERNAL') || die();

class block_atl_schoollist extends block_base {

    /**
     * Initialises the block.
     *
     * @return void
     */
    public function init() {
		$title = $this->get_blocktitle();
        $this->title = $title;  //This will get display in Dashboard Page
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
        $this->content->footer = '';

        $this->content->text   = $this->schoollist();
        $this->content->footer = ''; //Footer here...

        return $this->content;
    }

    private function schoollist(){
        global $DB ;
        $sql="SELECT s.id,s.name,s.address,c.name as city FROM {school} s JOIN {city} c ON s.cityid=c.id WHERE s.activestatus=1 ORDER BY s.rank LIMIT 0,10";
        $data = $DB->get_records_sql($sql);
        if(count($data)){
            $content = '<div id="block-atlschool" class="block-atlschool" data-region="atlschool">';
            foreach($data as $key=>$values){
              $content = $content.'<div class="schoolrow clearfix">
                  <div class="left">
                  '.$values->name.' - '.$values->city.'
                  </div>
              </div>';
            }
            $content = $content.'</div>';
        } else{
            $content = '<div id="block-atlschool" class="block-atlschool" data-region="atlschool">
              No Records of School
            </div>';
        }
        return $content;
    }
	
	//Show Title at Dashboard block for Atl School
	private function get_blocktitle() {		
		$cnt = $this->frmgetcount();
		return 'ATL Schools ('.$cnt.')';
	}

	//SnapShot of Count.
	private function frmgetcount(){
		global $DB;
		$cnt = 0;
		$cnt = $DB->count_records("school");
		return $cnt;
	}

}
