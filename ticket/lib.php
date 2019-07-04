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
 * @CreatedOn: 18-07-2018
 * @Description: Library functions For Ticketing system
*/

require_once('../create/externallib.php');
//Shows Main Listing of Tickets at main page.
function get_ticketlist(techticket_render $renderObj){
	$content = "";	
	$content.= $renderObj->show_searchbox();
	$content.= "<div id='table-content-wrapper' style='margin-top: 6%;'>";
	$content.= show_ticketlist($renderObj);
	$content.="</div>";	
	return $content;
}

function show_ticketlist($renderObj,$page=1,$condition=''){
	global $DB,$USER;
	$recordsperpage = $renderObj->recordsperpage;
	$temp = ($renderObj->userrole=='admin')?null:'WHERE createdby='.$USER->id;
	$condition = (empty($condition))?$temp:$condition;
	$total_tickets = totalticketcount($condition);
	$content ="<div class='width100'><div class='pull-left'><b>Total : ".$total_tickets."</b></div><br><br>";
	//$content.="<div class='pull-right'><a class='btn btn-primary' href='downloadexcel.php'> Download in Excel Fomat </a></div></div><br><br>";
	//$start_from = ($page-1) * $limit;	

	$start_from = ($page-1) * $recordsperpage;
	$list = get_dbrecords($recordsperpage,$start_from,$condition);
	$content.=$renderObj->get_tablerows($list,$page,$recordsperpage);
	$content.='<div class="pagination-wrapper" align="center">';
	($total_tickets>1)?$total_pages =ceil(($total_tickets)/$recordsperpage):$total_pages =1;
	$content.= paginate_newfunction($recordsperpage,$page,$total_tickets,$total_pages,'ticket-report');
	$content.='</div>';
	return $content;
}

function totalticketcount($condition){
	global $DB;
	$sql="SELECT count(id) as cnt FROM {tech_ticket} t $condition";
	$data = $DB->get_record_sql($sql);
	return $data->cnt;
}

function get_dbrecords($limit='',$start=0,$condition=''){
	global $DB;
	$sql = "SELECT t.id,t.name,t.description,u.firstname,u.lastname ,DATE_FORMAT( FROM_UNIXTIME(t.timecreated),'%Y %M %D') as createddate,s.name as status,t.latest_comment,t.category";
	$sql.=" FROM {tech_ticket} t JOIN {user} u ON t.createdby=u.id LEFT JOIN {tech_ticket_status} s ON t.statusid=s.id";
	$sql.=" $condition order by t.timemodified desc limit $start,$limit";
	$result = $DB->get_records_sql($sql);
	return $result;
}

function get_allstatus(){
	global $DB;
	$status = $DB->get_records('tech_ticket_status');
	return $status;
}

function get_contentwithfilter(techticket_render $renderObj,$page,$filterby,$filtervalue){
	global $USER;
	$condition ='';
	$content='';
	$temp = ($renderObj->userrole=='admin')?null:'WHERE t.createdby='.$USER->id;
	switch($filterby){
		case 'movetopage':
			if(is_array($filtervalue)){
				$value = preparecondition($filtervalue);
				if(!empty($value)){
					$condition = (empty($temp)) ? " WHERE t.id>0 ".$value : $temp.$value;
				}
			}
			$content = show_ticketlist($renderObj,$page,$condition);
			break;
		case 'filterbystatus':
			if(is_array($filtervalue)){
				$value = preparecondition($filtervalue);
				if(!empty($value)){
					$condition = (empty($temp)) ? " WHERE t.id>0 ".$value : $temp.$value;
				}
			}
			$content = show_ticketlist($renderObj,$page,$condition);
			break;
		case 'searchfilter':
			$condition = (empty($temp)) ? " WHERE t.name LIKE'%".$filtervalue."%'" : $temp." AND t.name LIKE'%".$filtervalue."%'";
			$content = show_ticketlist($renderObj,$page,$condition);
			break;
		case 'resetfilters':
			$condition = (empty($temp)) ? " WHERE t.id>0 ": $temp." AND t.id>0";
			$content = show_ticketlist($renderObj,$page,$condition);
			break;
		case 'filterbycategory':
			if(is_array($filtervalue)){
				$value = preparecondition($filtervalue);		
				if(!empty($value)){
					$condition = (empty($temp)) ? " WHERE t.id>0 ".$value : $temp.$value;
				}
			}
			$content = show_ticketlist($renderObj,$page,$condition);
			break;
	}
	return $content;
}

function preparecondition($filtervalue){
	$condition = "";
	if (array_key_exists("dropdown1",$filtervalue)) {
		if($filtervalue['dropdown1']>0){
			$condition.= " AND t.statusid=".$filtervalue['dropdown1'];
		}
	}
	if (array_key_exists("searchbox1",$filtervalue)) {
		if($filtervalue['searchbox1']!=""){
			$condition.= " AND t.name LIKE'%".$filtervalue['searchbox1']."%'";
		}
	}
	if (array_key_exists("dropdown2",$filtervalue)) {
		if($filtervalue['dropdown2']>0){
			$condition.= " AND t.category=".$filtervalue['dropdown2'];
		}
	}
	return $condition;
}

function getnitiadminid(){
	global $DB;
	$user = $DB->get_record('user', array('username'=>'nitiadmin'),'id');
	return $user->id;
}

function is_ticketcreatedbyuser($id){
	global $DB,$USER;
	$data = $DB->get_record('tech_ticket',array('id'=>$id),'createdby');
	$status = ($data->createdby==$USER->id)?true:false;
	return $status;
}

function get_ticketdetail($id,$renderObj){
	global $DB,$USER;
	$userrole = get_atalrolenamebyid($USER->msn);
	$data = $DB->get_record('tech_ticket',array('id'=>$id));
	if(count($data)>0){
		$sql="SELECT r.id,r.reply,DATE_FORMAT( FROM_UNIXTIME(r.timecreated),'%D %M %Y') as createdate, ";
		$sql.="u.id as userid,u.firstname,u.lastname,u.username,u.picture,u.auth";
		$sql.=" FROM {tech_ticket_reply} r JOIN {user} u ON r.createdby=u.id WHERE r.ticketid=?";
		$replys = $DB->get_records_sql($sql,array($id));
		$statusdata = $DB->get_records("tech_ticket_status");
		$content = $renderObj->detailpage($id,$statusdata,$replys,$data,$userrole);		
	} else{ $content="No Records Found!"; }
	return $content;
}

function insert_newreply($ticketid,$data){
	global $DB,$USER;
	if(!empty($data) && $ticketid>0){
		$cm = new stdClass();
		$cm->ticketid = $ticketid;
		$cm->reply = $data;
		$cm->createdby = $USER->id;
		$cm->timecreated = time();
		$DB->insert_record('tech_ticket_reply', $cm);
		unset($cm);
		$cm = new stdClass();
		$cm->id = $ticketid;
		$cm->timemodified = time();
		$cm->isread = 0;
		$cm->latest_comment = $data;
		$DB->update_record('tech_ticket', $cm);
	}
}

function delete_ticket($id,$msg=''){
	global $DB;
	$sql="SELECT u.id,u.auth,u.username,u.firstname,u.lastname,u.deleted,u.email,t.name,t.id as ticketid ";
	$sql.="FROM {user} u JOIN {tech_ticket} t ON u.id=t.createdby WHERE t.id=".$id;
	$user = $DB->get_record_sql($sql);
	try {
		$transaction = $DB->start_delegated_transaction();
		// Delete a record
		$DB->delete_records("tech_ticket_reply", array('ticketid'=>$id));
		$DB->delete_records("tech_ticket", array('id'=>$id));
		$transaction->allow_commit();
		delticket_sentmail($user,$msg);
		return "success";
	} catch(Exception $e) {
		$transaction->rollback($e);
		return "error";
	}
}

function change_status($id,$status){
	global $DB;
	try {
		$transaction = $DB->start_delegated_transaction();
		// Update a record
		$cm = new stdClass();
		$cm->id = $id;
		$cm->statusid = $status;
		if(!empty($status)){
			$DB->update_record("tech_ticket", $cm);
		}
		$transaction->allow_commit();
		return "success";
	} catch(Exception $e) {
		$transaction->rollback($e);
		return "error";
	}
}

function get_ticketcategory(){
	/*$category_array = array('0'=>'Select Category','1'=>'Technical issue in Badges and Awards feature','2'=>'Technical issue in Blogs and Forums feature',
	'3'=>'Technical issue in Innovations feature','4'=>'Technical issue in Mentor Modules/Tutorials feature',
	'5'=>'Technical issue in Mentor Meeting feature','6'=>'Technical issue in Mentor School of Choice','7'=>'Technical issue in User Profile feature','10'=>'Others');*/
	//**12-Dec-2018,portal-goingforward
	$category_array = array('0'=>'Select Category','1'=>'Technical issues with the ATL InnoNet Portal','2'=>'Queries Regarding the Mentor of Change program');
	return $category_array;
}

function get_categoryname($categoryid){
	if(empty($categoryid)){
		return "";
	}
	$search_array = get_ticketcategory();
	if (array_key_exists($categoryid, $search_array)) {
		return $search_array[$categoryid];
	} else{
		return "";
	}
}

//To check which category Belongs to AIM, rest will be Handle by IBM.
function is_aim_relatedcategory($categoryid){
	//$search_array = get_ticketcategory();
	//if($search_array[$categoryid]=='Others')
	if($categoryid==1){
		return false;
	} else{ return true; }
}

//Email sent to AIM/IBM, Once StakeHolder raise a ticket.
function ticket_sentmail($ticketobj){
	global $USER,$DB;
	$from = $USER->email;
	$settingsdata = $DB->get_records_sql("SELECT * FROM {custom_settings} WHERE atl_key IN('emailmentorindia','emailibm')");
	if(count($settingsdata)>1){
		foreach($settingsdata as $k=>$v){
			$settingarray[$v->atl_key] = $v->atl_value;
		}
		$useremail = $DB->get_record('user', array('username'=>'nitiadmin'));
		$subject = "Ticket Atltk0".$ticketobj->id.": ".$ticketobj->name;
		$useremail->email = (is_aim_relatedcategory($ticketobj->category)) ? $settingarray['emailmentorindia'] : $settingarray['emailibm'];
		$mailbody = $ticketobj->description;
		$mailbody = "Hi,<br>".$mailbody."</br></br>"."Best Regards, </br>".$USER->firstname." ".$USER->lastname;		
		email_to_user($useremail, $from,$subject,$mailbody);
	}
}

//Email sent to StakeHolder, once a ticket been deleted by admin
function delticket_sentmail($userobj,$msg){
	global $DB;
	$settingsdata = $DB->get_records_sql("SELECT * FROM {custom_settings} WHERE atl_key IN('atl_noreply')");
	if(count($settingsdata)>0){
		foreach($settingsdata as $k=>$v){
			$settingarray[$v->atl_key] = $v->atl_value;
		}
		$subject = "Ticket Deleted No: Atltk0".$userobj->ticketid;
		$from = $settingarray['atl_noreply'];
		$mailbody = "<p> Your Technical Ticket (".$userobj->name.") is deleted for following reason</p><p>".$msg."</p>";
		$mailbody = "Hello ".$userobj->firstname.",<br>".$mailbody."</br></br>"."Best Regards, </br> AIM </br> Disclaimer: Its an autogenrated mail, Please do not reply";
		email_to_user($userobj, $from,$subject,$mailbody);
	}
}
	