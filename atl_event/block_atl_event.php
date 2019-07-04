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
 * Single Event Block caps. 
 * @package:  block_atl_event
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 08-12-2017
 * @Description: Plugin main file. Will show latest single event image & info at Dashboard Right side menu.
 * Show Out Of The World - a kid picture with a Cup in .psd design.
*/

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/forum/lib.php');

class block_atl_event extends block_base {

    /**
     * Initialises the block.
     *
     * @return void
     */
    public function init() {
        $this->title = 'Event'; //get_string('pluginname', 'block_atl_dashboard'); //This will get display in Dashboard Page
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

        $this->content->text   = ""; //$this->eventcontent(); commented on 4-June-2018
        $this->content->footer = ''; //Footer here...

        return $this->content;
    }

    public function eventcontent(){
        global $DB, $CFG;
        $currenttime = time();
        $query = "SELECT id,name,description,parentid FROM {event} WHERE timestart<=".$currenttime." AND eventtype='site' ORDER By id DESC LIMIT 0,3";
        $data = $DB->get_records_sql($query);		
        $title = "Show out of the World";
        $description = "Showcase your best project ideas to the world and a great chance to win cash prize";
		$eventid = 0;
		$eventpostid = 0;
        //if(count($data)>0){
           /*  foreach($data as $keys=>$values){
                $title = $values->name;
                $description = $values->description;
				$eventid = $values->id;
				$eventpostid = $values->parentid;
            } */
        //}
		//$eventimgpath = $CFG->wwwroot.'/theme/image.php/moove/theme/1512715784/competition';
		//if(!empty($eventpostid)){
			//This Event Have an Image, get that image..			
			//$eventimgpath = getevent_imagepath($eventpostid);
		//}
		$content='';
		$content.='<div class="slideshow-container" style="max-width:200px;height:200px;">';
		$i=1;
		$total=count($data);
		if($total>0){
			foreach($data as $keys=>$values){
				if(strlen($values->name)>15){
					$name = substr($values->name,0,15);
					$name.= '...';
				}
				else
					$name = $values->name;
				if(strlen($values->description)>30){
					$description = substr($values->description,0,30);
					$description.= '...';
				}
				else
					$description = $values->description;
				//$eventimgpath = $CFG->wwwroot.'/theme/image.php/moove/theme/1512715784/competition';
				if($values->parentid==0)
					$eventimgpath = $CFG->wwwroot.'/theme/image.php/moove/theme/1512715784/competition';
				else
					$eventimgpath = getevent_imagepath($values->parentid);
				$content.= '<div id="block-atalevent" class="customslider fade block-atalevent" data-region="myoverview">			
					<div class="clearfix">
						
						<div class="eventblockleft ">
						 <div class="numbertext">'.$i.'/'.$total.'</div>
						  <img src="'.$eventimgpath.'" alt="image" width="200px" height="200px">
						</div>
						<div class="smallblockright text">
							<div class="title">'.$name.'</div>
						  <div class="card-text">'.$description.'</div>
						</div>
					</div>
				</div>';
				$i++;
			}
			//Slide show JS is written in /my/index.php
			$content.='<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
			<a class="next" onclick="plusSlides(1)">&#10095;</a></div>
			<div style="text-align:center" class="customslider-dot">
			  <span class="dot" onclick="currentSlide(1)"></span> 
			  <span class="dot" onclick="currentSlide(2)"></span> 
			  <span class="dot" onclick="currentSlide(3)"></span> 
			</div>';
			
			return $content;
		}
		else
		{
			$content = '<div id="block-atalevent" class="block-atalevent">			
					<div class="clearfix"> No Events Found!</div>
					</div>';
			return $content;
		}
	}


}
