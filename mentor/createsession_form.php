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

/* @package: core_mentor
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 14-11-2018
 * @Description: createsession form
*/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

class session_create_form extends moodleform {

    /**
    * Define the form.
    */
    public function definition() {
        global $CFG, $USER, $OUTPUT;

        $mform = $this->_form; // Don't forget the underscore!
		$sessionid = $this->_customdata['id'];
		$schoollist = $this->_customdata['schoollist'];
		
		$entry = new stdClass;
		$entry->id = null;		
		$st_time = (isset($this->_customdata['sttime']) && !empty($this->_customdata['sttime']))?$this->_customdata['sttime']:'';
		$ed_time = (isset($this->_customdata['edtime']) && !empty($this->_customdata['edtime']))?$this->_customdata['edtime']:'';
		$function_detail = (isset($this->_customdata['fundetail']) && !empty($this->_customdata['fundetail']))?$this->_customdata['fundetail']:'';
		$tmpsesstype = (isset($this->_customdata['sesstype']) && !empty($this->_customdata['sesstype']))?$this->_customdata['sesstype']:'';		

		$attributes=array('size'=>'10','maxlength'=>'3');
		$divdisplay = (empty($function_detail))?"display:none;":"display:block;";
		if($tmpsesstype=='c'){
			$divdisplay = "display:block;"; //attend school function.
		}
		$list = getSessionType();
		$elementname = get_file_elementname();
		$filemanageroptions = get_session_filemanageroptions();
		$selected = ""; 
		if(!empty($st_time))
			$st_time = explode("-",$st_time);
		if(!empty($ed_time))
			$ed_time = explode("-",$ed_time);
		//Time options..
		$endtimevalue = 13;
		$endtimematch = 13; //value be always equal to $endtimevalue
		for($i=1;$i<13;$i++){
			$selected = ($st_time[0]==$i)?'selected':"";
			$timeoptionstart.= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
		}
		$edtimeiso = ($sessionid===-1)?$ed_time[0]:get_iso_convertion($ed_time[0],'e');
		for($i=1;$i<13;$i++){			
			$selected = ($edtimeiso==$endtimematch++)?'selected':"";
			$timeoptionend.= '<option value="'.$endtimevalue++.'" '.$selected.'>'.$i.'</option>';
		}
		for($i=0;$i<60;$i++){
			$selected = ($st_time[1]==$i)?'selected':"";
			$timemnoptionmstart.= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
		}
		for($i=0;$i<60;$i++){
			$selected = ($ed_time[1]==$i)?'selected':"";
			$timemnoptionmend.= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
		}
		
        //Normal fields .....
		
		//Select a School Option
		$mform->addElement('select', 'schoolid', 'Select School', $schoollist);
		
		//Date of Session
		$mform->addElement('date_selector', 'dateofsession', "Date of Session");
	
		//Start time of session
		$amoption = (!empty($st_time) && $st_time[2]=='AM')?"selected":"";
		$pmoption = (!empty($st_time) && $st_time[2]=='PM')?"selected":"";
		$html='<div class="form-group row  fitem   " data-groupname="starttime">
		<div class="col-md-3">
		<span class="pull-xs-right text-nowrap">
		</span>
		<label class="col-form-label d-inline " for="id_type">
		Start Time
		</label>
		</div>
		<div class="col-md-9 form-inline felement" data-fieldtype="select">
		<select class="custom-select  " name="starttimea" id="id_starttime1" float="left" width="10%">'
		.$timeoptionstart.'
		</select> &nbsp;Hr&nbsp;&nbsp;
		<select class="custom-select  " name="starttimemn" id="id_starttime2" float="left" width="10%">
		'.$timemnoptionmstart.'
		</select> &nbsp;Min&nbsp;&nbsp;
		<select class="custom-select  " name="starttimeam" id="id_starttime3" float="left" width="10%">
		<option '.$amoption.'>AM</option>
		<option '.$pmoption.'>PM</option>
		</select>		
		</div>
		</div></div>';
		$mform->addElement('html', $html);

		//End time of session
		$amoption = (!empty($ed_time) && $ed_time[2]=='AM')?"selected":"";
		$pmoption = (!empty($ed_time) && $ed_time[2]=='PM')?"selected":"";
		$html='<div class="form-group row  fitem   " data-groupname="endtime">
		<div class="col-md-3">
		<span class="pull-xs-right text-nowrap">
		</span>
		<label class="col-form-label d-inline " for="id_type">
		End Time
		</label>
		</div>
		<div class="col-md-9 form-inline felement" data-fieldtype="select">
		<select class="custom-select  " name="endtimea" id="id_endtime1" float="left" width="10%">'
		.$timeoptionend.'
		</select> &nbsp;Hr&nbsp;&nbsp;
		<select class="custom-select  " name="endtimemn" id="id_endtime2" float="left" width="10%">
		'.$timemnoptionmend.'
		</select> &nbsp;Min&nbsp;&nbsp;
		<select class="custom-select  " name="endtimeam" id="id_endtime3" float="left" width="10%">
		<option '.$amoption.'>AM</option>
		<option '.$pmoption.'>PM</option>
		</select>
		<div class="form-control-feedback" id="id_error_starttime" style="display:none;color:red;">      
		</div>
		</div>
		</div></div>';
		$mform->addElement('html', $html);
		
		//Total Number of Student
		$mform->addElement('text', 'totalstudents', 'Total Number Of Student',$attributes);
		$mform->addRule('totalstudents', 'Enter Numeric value', 'numeric', null, 'client');
		$mform->setDefault('totalstudents', 0);
		$mentormsg = '<h6> Enter number between 1 to 150</h6>';
		$mform->addElement('static', 'message','', $mentormsg);
		
		//Session Type
		$mform->addElement('select', 'sessiontype', 'Type of Session', $list);
		$mform->addRule('sessiontype', 'Select Session Type', 'required', null, 'client');
		
		//Detail of School function , Show If Type of session selected = c "In person visit to school function"
		$html='<div class="form-group row  fitem" id="functiondetaildiv" style="'.$divdisplay.'">
		<div class="col-md-3">		
		<label class="col-form-label d-inline " for="id_functiondetails">
		Description Of School Function (Max 100 words)
		</label>
		</div>
		<div class="col-md-9 form-inline felement" data-fieldtype="textarea">
		<textarea name="functiondetails" id="id_functiondetails" class="form-control " rows="4" cols="40" maxlength="100" wrap="virtual">'.$function_detail.'</textarea>
		<div class="form-control-feedback" id="id_error_functiondetails" style="display: none;">
		</div>
		</div>
		</div>';
		$mform->addElement('html', $html);		
		
		$mform->addElement('textarea', 'details', 'Description Of Session (1000 Characters Max)', 'wrap="virtual" rows="14" cols="60" maxlength=1000');
		$mform->addRule('details', 'Enter session details', 'required', null, 'client');
		
		//File manager
		$mform->addElement('html','<div class="form-group row"></div>');
		$mform->addElement('filemanager', $elementname, 'Upload Pictures (in case of an online session please upload a screenshot of your video conference session)', null, $filemanageroptions);
		$mform->addRule($elementname, 'You must upload 1 file', 'required', null, 'client');
		
		$mentormsgg = 'I, hereby declare that the session details mentioned above are true and that the session took place. I understand that if the above information is found incorrect, AIM, NITI Aayog can remove me as a Mentor of Change.';
		$mform->addElement('static', 'messageg','Declaration', $mentormsgg);
		$mform->addElement('checkbox', 'declarationagree', '');
		
		$mform->addElement('html','<div class="form-group row"></div>'); 
		
		if ($sessionid!==-1) {
			$btnstring = get_string('update');
			$mform->addElement('hidden', 'flag', 'edit');
			$entry->id = $sessionid;
        } else {
            $btnstring = "Submit";
			$mform->addElement('hidden', 'flag', 'add');
        }
		//Start & End Time Actual Values.
		$mform->addElement('hidden', 'starttime','',array('id'=>'starttime'));
		$mform->addElement('hidden', 'endtime','',array('id'=>'endtime'));
		$mform->addElement('hidden', 'schoolfunction','',array('id'=>'schoolfunction'));		
		$mform->addElement('hidden', 'id', $sessionid);
		$mform->addElement('hidden', 'isrptmentorsession', 'y');
		$mform->addElement('hidden', 'dateofsession_date');

        $this->add_action_buttons(true, $btnstring);
		//$this->add_action_buttons(false, $btnstring); //without cancel button		
    }

	/**
    * Extend the form definition after the data has been parsed.
    */
    public function definition_after_data() {
        global $CFG, $DB, $OUTPUT, $USER;
        $mform = $this->_form;
        $sessionid = $mform->getElementValue('id');
		$elementname = get_file_elementname();
		$context = context_user::instance($USER->id, MUST_EXIST);
		//$fs = get_file_storage();
		$draftitemid = file_get_submitted_draft_itemid($elementname);
		$fid = 1;
		file_prepare_draft_area($draftitemid, $context->id, 'mentorsession_file' , "files_{$fid}", $sessionid, get_session_filemanageroptions());
		$mform->setDefault($elementname, $draftitemid);
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
		
		if(empty($data['schoolid'])){
			$errors['schoolid'] = 'Please Select a School';
		}
		$starttimeerror = validateFormSessionDate($data);
		if ($starttimeerror[0]=="error") {
			$errors['dateofsession'] = $starttimeerror[1];
		}
		if($data['totalstudents']>150){
			$errors['totalstudents'] = 'Cannot add more then 150 students';
		}
		if(!isset($data['declarationagree'])){
			$errors['declarationagree'] = 'Please tick Declaration';
		}
		if($data['sessiontype']=='b' || $data['sessiontype']=='d'){
			if($data['totalstudents']==0){
				$errors['totalstudents'] = 'Enter Student Count';
			}
		}
		return $errors;
	}
}
