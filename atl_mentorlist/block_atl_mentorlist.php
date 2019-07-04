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
 * Mentor List Block caps. 
 * @package:  block_atl_mentorlist
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 6-12-2017
 * @Description: main plugin file.
	This Block will Display List of Mentors To Niti Admin dashboard
	Right-side pane. Also will display List of students under a Mentor
	in Mentor Dashboard. And for Students it will show recomended projects.
*/

defined('MOODLE_INTERNAL') || die();

class block_atl_mentorlist extends block_base {

    /**
    * Initialises the block.
    *
    * @return void
    */
	
	public $schoolid = 0;
	
    public function init() {
        $title = $this->get_blocktitle();
        $this->title = $title; //get_string('pluginname', 'block_atl_mentorlist'); //This will get display in Dashboard Page
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

        $this->content->text   = $this->userscontent();
        $this->content->footer = ''; //Footer here...

        return $this->content;
    }

    private function userscontent(){
        global $USER ;
        $current_userroleid = $USER->msn;
        $content = '<div id="block-mentorlist" class="block-mentorlist" data-region="mentorlist">';
        if (function_exists('theme_get_rolenamebyid')) {
            $rolename = theme_get_rolenamebyid($current_userroleid);
        } else {
            $rolename = 'guest';
        }
        if($rolename=='mentor'){
            //Show List of Students
            $content = $content.$this->showstudentlist();
			$content = $content.$this->showassignschool();
        } elseif($rolename=='admin'){
            //Show list of all Mentors, as its a Niti-admin
            $content = $content.$this->showmentorlist();
        } elseif($rolename=='incharge'){
			//Show list of Students under a school, as its a School Incharge
			$content = $content.$this->studentlistinschool();
        } else{
            //Show Recommended projects
            //$content = $content.$this->showprojects();
		    //Show School InCharge Details..for students
		    $content = $content.$this->showschool_chiefdetail();
        }

        $content = $content.'</div>';

        return $content;
    }

    private function showmentorlist(){
        global $DB, $USER,$OUTPUT, $CFG ;
        require_once($CFG->libdir.'/filelib.php');
        $mid = theme_get_roleidbyname('mentor');
        $content = '';
        $data = $DB->get_records('user', array('msn' => $mid,'deleted'=>0),'id desc','*',0,10);
        if(count($data)) {
            foreach($data as $key=>$values){
                $name = $values->firstname.' '.$values->lastname;
                //get User Profile pic.
				$userlink = userpicbyobject($values);
				$content = $content.'
                <div class="mentorrow clearfix">
                    <div class="left picture">'.$userlink.'
                    </div>
                    <div class="topic">'
                      .$name.' , '.$values->city.'<br>'.$values->department.'
                    </div>
                </div>';
          }
       }
        return $content;
    }

    private function showstudentlist()
    {
        global $DB, $USER,$OUTPUT, $CFG ;
        require_once($CFG->libdir.'/filelib.php');

        $content = "";
        $uid = $USER->id;
        $studentroleid = theme_get_roleidbyname('student');
        
		$sql="SELECT u.id as userid,u.auth,u.username,u.firstname,u.lastname,u.msn,u.picture,s.name as school,s.id as schoolid,c.name as city 
		FROM {user} u JOIN {user_school} us ON u.id=us.userid JOIN {school} s ON us.schoolid=s.id LEFT JOIN mdl_city c ON s.cityid=c.id 
		JOIN( SELECT distinct(ue.userid) as userid FROM {user_enrolments} ue JOIN {enrol} e ON ue.enrolid=e.id JOIN (SELECT e.id as enrolid FROM {user_enrolments} ue		 
		JOIN {enrol} e ON ue.enrolid=e.id JOIN {course} c ON e.courseid=c.id WHERE e.enrol='manual' AND ue.userid=".$uid.") AS mentorenrol on e.id=mentorenrol.enrolid ) As mentor ON u.id=mentor.userid 
		WHERE u.msn=".$studentroleid." AND u.deleted=0 Limit 0,10";
	
        $data = $DB->get_records_sql($sql);
        if(count($data))
        {
            foreach($data as $key=>$values){
				$name = $values->firstname.' '.$values->lastname;
				//get User Profile pic.
				$userobject = (object) array('id'=>$values->userid,'auth'=>$values->auth,'username'=>$values->username,
				'firstname'=>$values->firstname,'lastname'=>$values->lastname,'picture'=>$values->picture);
				$userlink = userpicbyobject($userobject);
				$content = $content.'
				<div class="mentorrow clearfix">
				  <div class="left picture">'.$userlink.'
				  </div>
				  <div class="topic">'
					.$name.'<br>'.$values->school.',<br>'.$values->city.'<br>'.$values->project.'
				  </div>
				</div>';
            }
        }
        return $content;
    }

	//Show Title/Heading in dashboard according to Role
	private function get_blocktitle() {
		global $USER;
		$current_userroleid = $USER->msn;
		$role = get_atalrolenamebyid($current_userroleid);
		if($role=='mentor'){
			$cnt = $this->frmgetcount('studentundermentor');
		    return 'Students ('.$cnt.')';
		} elseif($role=='student'){
			return 'ATAL InCharge';
		} elseif($role=='incharge'){
			$cnt = $this->frmgetcount('studentinschool');
			return 'Students ('.$cnt.')';
		} elseif($role=='admin'){
			$cnt = $this->frmgetcount('totalmentors');
			return 'Mentors ('.$cnt.')';
		} else{
			return 'Mentors';
		}
	}

	//Show recomended projects if student logs in.
	private function showprojects(){
		global $DB, $USER,$OUTPUT, $CFG ;
		require_once($CFG->libdir.'/filelib.php');
		$content = '';
		$mid = theme_get_roleidbyname('student');
		
		$content = $content.'
		<div class="mentorrow clearfix">
			<div class="recomendproj">
				<div class="projimage"><img src="'.$OUTPUT->image_url('folder', 'theme').'"></div>
				<div class="sugprojecttitle">Design a Unique Rain Water harvesting System</div>
			</div>
			<div class="recomendproj">
				<div class="projimage"><img src="'.$OUTPUT->image_url('folder', 'theme').'"></div>
				<div class="sugprojecttitle">Making an Intelligent Robot</div>
			</div>
			<div class="watsonlist">
				<div style="float:left;">
				<input type="text" name="wastsonsearch" size="10">
				</div>
				<div style="float:left;"><a href="javascript:void(0);"><img src="'.$OUTPUT->image_url('watson', 'theme').'"></a>
			</div>
		</div>';
		
		//}
		//}
		return $content;
	}
	
	//Display List of Students of school in LHS (mentors/incharge/student)
	function studentlistinschool(){
		global $DB, $USER,$OUTPUT, $CFG ;
		$rolename = get_atalrolenamebyid($USER->msn);
		if($rolename=='incharge'){
			//Show students of his school;
			$schoolid = $this->schoolid;
			if($schoolid==0){
				$data = $DB->get_record('user_school', array('userid'=>$USER->id));
				$schoolid = $data->schoolid;
			}
		}
		$mid = atal_get_roleidbyname('student');
		$content = '';
		$sql="SELECT u.id,u.username,u.firstname,u.auth,u.lastname,u.lastname,u.msn,u.city FROM {user} u JOIN {user_school} us ON u.id=us.userid WHERE us.schoolid= ?";
		$sql.=" AND u.msn = ? ORDER BY u.id desc LIMIT 0,10";
		$data = $DB->get_records_sql($sql, array($schoolid,$mid));
		if(count($data)) {
			foreach($data as $key=>$values){
				$name = $values->firstname.' '.$values->lastname;
				//get User Profile pic.
				$userlink = userpicbyobject($values);
				$content = $content.'
				<div class="mentorrow clearfix">
					<div class="left picture">'.$userlink.'
					</div>
					<div class="topic">'
					  .$name.' , '.$values->city.'<br>
					</div>
				</div>';
		    }
	    }
		return $content;
	}
	
	//SnapShot of Count.
	private function frmgetcount($flag){
		global $DB, $USER;
		$cnt = 0;
		if($flag=='studentinschool'){
			//Show count of student in a School.
			$data = $DB->get_record('user_school', array('userid'=>$USER->id));
			$schoolid = $data->schoolid;
			$this->schoolid = $schoolid;
			$mid = atal_get_roleidbyname('student');
			$sql="SELECT count(u.id) as cnt FROM {user} u JOIN {user_school} us ON u.id=us.userid WHERE us.schoolid= ?";
			$sql.=" AND u.msn = ?";
			$data = $DB->get_record_sql($sql, array($schoolid,$mid));
			$cnt = $data->cnt;
		} else if($flag=='studentundermentor'){
			$uid = $USER->id;
			$studentroleid = theme_get_roleidbyname('student');
			$sql="SELECT u.id,u.username FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid JOIN {enrol} e ON ue.enrolid=e.id 		
			JOIN (SELECT c.id as courseid FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid JOIN {enrol} e ON ue.enrolid=e.id 
			JOIN {course} c ON e.courseid=c.id WHERE e.enrol='manual' AND u.id=".$uid." ) As mentorcourse ON e.courseid=mentorcourse.courseid 
			WHERE u.msn=".$studentroleid." AND u.deleted=0 GROUP BY u.username";
			$data = $DB->get_records_sql($sql);
			$cnt = count($data);
		} else if($flag=='totalmentors'){
			$mid = atal_get_roleidbyname('mentor');
			$cnt = $DB->count_records("user",array('msn'=>$mid,'deleted'=>0));
		}
		return $cnt;
	}
	
	//Show School Chief Details in Student LoggedIn.
	private function showschool_chiefdetail()
	{
		global $DB, $USER, $SESSION ;
        $content = "";
        $uid = $USER->id;
		if(isset($SESSION->schoolid) && $SESSION->schoolid>0){
			$schoolid = $SESSION->schoolid;
		} else{
			$schooldata = $DB->get_record('user_school', array('userid'=>$USER->id), '*');
			$schoolid = (isset($schooldata->schoolid))?$schooldata->schoolid:0;
		}
		$sql = "SELECT u.id,u.firstname,u.lastname,u.auth,u.username,u.phone1,u.phone2,u.email,u.picture FROM {user} u JOIN {user_school} us ON u.id=us.userid ";
		$sql.= " WHERE us.schoolid = ? AND us.role='incharge'";
		$data = $DB->get_records_sql($sql,array($schoolid));
        if(count($data))
		{
            foreach($data as $key=>$values){
				$name = $values->firstname.' '.$values->lastname;
				//get User Profile pic.
				$userobject = (object) array('id'=>$values->id,'auth'=>$values->auth,'username'=>$values->username,
				'firstname'=>$values->firstname,'lastname'=>$values->lastname,'picture'=>$values->picture);
				$userlink = userpicbyobject($userobject);
				$content = $content.'
				<div class="mentorrow clearfix">
				  <div class="left picture">'.$userlink.'
				  </div>
				  <div class="topic">'
					.$name.'<br>'.$values->email.'<br>'.$values->phone1.'<br>
				  </div>
				</div>';
            }
        }
        return $content;
	}

	//Show Assigned List of Schools For Mentor
	private function showassignschool()
	{
		global $DB, $USER;
		$content = "";
		$sql = "SELECT s.id,s.name,s.address FROM {school} s JOIN {user_school} us ON s.id=us.schoolid WHERE us.userid=? ANd us.role=?";
		$myschool = $DB->get_records_sql($sql,array($USER->id,'mentor'));
		if(count($myschool)>0){
			$content = '<div class="card-block"><h3 id="instance-47-header" class="card-title">Assign School ('.count($myschool).')</h3>
			<div class="card-text content">';
			foreach($myschool as $keys=>$values){
				$content.='<div class="topic"><h6>'.$values->name.' '.$values->address.'</h6>';
				$content.='</div>';
			}
			$content.= '</div><div>';
		}
		return $content;
	}
}
