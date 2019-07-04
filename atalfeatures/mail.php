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

/* @package: core_project
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 23-04-2018
 * @Description: Sending an Invite Mail during a Launch of Portal , Phase-I
*/

require_once('../config.php');
require_once('render.php');
include_once(__DIR__ .'/mailform.php');
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
$PAGE->set_url('/atalfeatures/mail.php');
$show_form_status=false;
//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Send Mail ";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("Send Mail");
echo $OUTPUT->header();
$formobj = new CreateMailForm();
if ($formobj->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
	// redirect($returnurl);
} else if ($data = $formobj->get_data()) {
		try
		{
			$subject=  ucfirst($data->subject);			
			$roleid = atal_get_roleidbyname($data->role);
			if($data->role=='all'){
				$sql = "select id,firstname,lastname,email,passraw,msn,username from {user} where deleted=0";
			}
			elseif($data->role=='mentor'){
				// Phase 1
				//$sql ="SELECT u.id,u.firstname,u.lastname,u.email,u.passraw,username FROM {user_school} us join {user} u on u.id=us.userid where u.deleted=0 and us.role='mentor'";
				// Phase 2
				$sql ="select mu.id,mu.firstname,mu.lastname,mu.email,mu.passraw,mu.username from mdl_user mu join mdl_user_school mus on mu.id=mus.userid where mu.msn=4 and mu.deleted=0 and mu.welcomemail=0 and mu.passraw!=''";
				$subject= "Beginning your mentoring sessions: ATL Innonet Portal";
			}
			elseif($data->role=='incharge')
			{
				//$sql ="select mus.*,ms.atl_id,mu.firstname,mu.lastname,mu.email,mu.passraw mu.username from mdl_user_school mus join mdl_school ms on ms.id=mus.schoolid join mdl_user mu on ms.atl_id=mu.username where mus.role="mentor" GROUP by ms.id "
				// Phase 1
				//$sql ="select mu.id,ms.atl_id,ms.name as schoolname,mu.firstname,mu.lastname,mu.email,mu.passraw,mu.username,mu.profilestatus from mdl_user_school mus join mdl_school ms on ms.id=mus.schoolid join mdl_user mu on ms.atl_id=mu.username where mus.role='mentor' and mu.deleted=0 and mu.profilestatus IS NULL GROUP by ms.id";
				// Phase 2
				$sql ="select mu.id,ms.atl_id,ms.name as schoolname,mu.firstname,mu.lastname,mu.email,mu.passraw,mu.username,mu.profilestatus from mdl_user_school mus join mdl_school ms on ms.id=mus.schoolid join mdl_user mu on ms.atl_id=mu.username where mus.role='mentor' and mu.deleted=0 and mu.welcomemail=0 and mu.timecreated >=unix_timestamp('2018-08-20') and mu.profilestatus IS NULL GROUP by ms.id";
				$subject= "Beginning mentor sessions in your ATL: Welcome to ATL Innonet Portal";
			//$sql = "SELECT u.id,u.firstname,u.lastname,u.email,u.passraw FROM `mdl_user` u join mdl_school s on s.atl_id=u.username where u.msn=3 ORDER BY `id` DESC "; 
			}
			else
				$sql = "select id,firstname,lastname,email,passraw,username from {user} where deleted=0 and msn=".$roleid;
			$user = $DB->get_records_sql($sql);
			// Send Mail to Users
			$from = "atlinno.net-niti@gov.in";
			if(count($user)>0){
				$userid_update = array();
				foreach($user as $key=>$value)
				{
					$useremail = new StdClass();
					$useremail->email = 'jothip1@in.ibm.com' ;
					//$useremail->email = 'saksham.saxena@nic.in' ;
					//$useremail->email = 'dichak78@in.ibm.com' ;
					//$useremail->email = 'deepanshu.pathak@gov.in';
					//$useremail->email = $value->email;
					$useremail->id = $value->id;					
					$useremail->deleted =0;
					$value->firstname = ucwords(strtolower($value->firstname));
					$mailbody='';
					if($data->role=='mentor'){
						$mailbody = '';
						$mailbody.='<p> Dear '.$value->firstname.' '.$value->lastname.',';	
						$mailbody.='<p><div>By now you have been assigned an Atal Tinkering Lab in a school near you. Please refer to the mail sent to you with the subject title "Mentor of Change selection and school assignment" ';
						$mailbody.='in the month of March, 2018 for reference. In case you have problems regarding the the school assignment, please write to us on mentorindia-aim@gov.in regarding the same. </div></p>';
						$mailbody.='<p>This mail is to introduce you to the ATL Innonet Portal and help you with the next steps in your mentorship journey. ';
						$mailbody.='ATL Innonet Portal (http://atlinnonet.gov.in) is a website which has been created specially for you to help you get started and carry out different ';
						$mailbody.='activities of mentorship. Please use the following details to log in to the portal. </p>';
						$mailbody.='<p>Website Address: <a href="http://atlinnonet.gov.in/">http://atlinnonet.gov.in/</a></p>';
						$mailbody.='<p>Username: '.$value->username.'</p>';
						$mailbody.='<p>Password: '.$value->passraw.'</p>';
						$mailbody.='<p>Once you login, you are requested to carefully read the Terms and Conditions. After agreeing to them, you are requested to complete your registration by providing the information asked on the website. </p>';
						$mailbody.='<p>The "Role of a Mentor" tab in the website will help you understand your roles and responsibilities in detail. Please read through the document carefully. The document clearly details out each and every step of the mentorship journey for you.</p>';
						$mailbody.='<p>Once done, you are requested to immediately start and complete the "Mentor Training Tutorial" provided in the website, to gain the minimum required knowledge, skills, values and mindsets to be an effective mentor. NOTE: It is mandatory for each mentor to complete the tutorial.</p>';
						$mailbody.='<p>Please refer to the tab "How to Use the Portal" of the website to understand the detailed use of each and every feature of the website.</p>';				
						$mailbody.='<p>Write to us on mentorindia-aim@gov.in if you face problems accessing the portal. </p>';
						$mailbody.='<p>Happy Mentoring!</p>';
						$mailbody.='<p>Regards,<br>';
						$mailbody.='AIM Team</p>';
					}
					if($data->role=='incharge'){
						$mailbody = '';						
						$mailbody.='<p> Dear Principal,';
						$mailbody.='<p><div>Atal Innovation Mission is pleased to announce the launch of ATL InnoNet (Innovation Networking and Mentoring Platform), an endeavour to help the students and ATL schools to connect with mentors who will coach and motivate the next generation to tinker and innovate. The details of the mentors assigned to your school have been already sent in a previous mail. </div></p>
						<div><p>Please find the link to ATL InnoNet and the login details for your school <b>'.$value->schoolname.'</b> for the same below </div></p>';
						$mailbody.='<p>Platform link - <a href="http://atlinnonet.gov.in/">http://atlinnonet.gov.in/</a></p>';
						$mailbody.='<p>Login ID – '.$value->username.'</p>';
						$mailbody.='<p>Password – '.$value->passraw.'</p>'; 
						$mailbody.='<p>Please login and connect to the network to begin sessions by the mentors assigned to your school by the AIM team.</p>';
						$mailbody.='<p>Please note the following points as you start using the ATL InnoNet website:</p>';
						$mailbody.='<p>1. Make sure you note down your new password once you have changed it.</p>';
						$mailbody.='<p>2. Please make sure to fill in the correct details for the school, the Principal and the ATL In-charge at the time of first time login.</p>';
						$mailbody.='<p>3. Once you login, please click on "How to Use the Portal" and read the guidelines.</p>';
						$mailbody.='<p>4. After reading the guidelines, please click on "Schedule a Mentor Session" to schedule sessions with your mentors.</p>';
						$mailbody.='<br><p>Please write to mentorindia-aim@gov.in in case of any queries related to ATL Innonet website.</p>';
						$mailbody.='<br><p>Thank You,<br>';
						$mailbody.='AIM Team</p>';
					}
					if($data->role=='student' || $data->role=='all'){
						$mailbody = '';						
						$mailbody.='<p> Dear '.$value->firstname.',';
						$mailbody.='<p><div>Atal Innovation Mission is pleased to announce the launch of ATL InnoNET (Innovation Networking and Mentoring Platform), an endeavour to help the students and ATL schools to connect with mentors who will coach and motivate the next generation to tinker and innovate.
						Please find the link to ATL InnoNet and the login details. </div></p>';
						$mailbody.='<p>Platform link - <a href="http://atlinnonet.gov.in/">http://atlinnonet.gov.in/</a></p>';
						$mailbody.='<p>Login ID – '.$value->username.'</p>';
						$mailbody.='<p>Password – '.$value->passraw.'</p>';
						$mailbody.='<p>Please login and connect to the network to begin mentoring.</p>';
						$mailbody.='<p>Thank You,<br>';
						$mailbody.='AIM Team</p>';
					}
					$mailbody.='<br><p>DISCLAIMER: Please do not reply to this automated email.</p>';
					email_to_user($useremail, $from,$subject, $mailbody); 
					$userid_update[] = $value->id;	
					die;
				}
				//UPDATE `mdl_user` SET welcomemail=1 where id in (5066,5065)
				if(count($userid_update)>0)
				{
					$userid = implode(",",$userid_update);
					$sql = "UPDATE {user} SET welcomemail=1 where id in (".$userid.")";
					$DB->execute($sql);
				}
				$show_form_status=true;
			}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();die;
		}
} 
$alert_box='';
if($show_form_status)
{
	$alert_box='<div class="alert alert-success">
	<strong>Mail Sent Successfully! </strong><button class="close" type="button" data-dismiss="alert">×</button></a>
	</div>';
}
echo $alert_box;
echo "<p style='color:red;'>Note : Bulk Mailing Functionality is disabled . It can be enabled on request!</p>";
echo $content = $formobj->render();

echo $OUTPUT->footer();
?>
<script type="text/javascript">
require(['jquery'], function($) {
});
</script>

