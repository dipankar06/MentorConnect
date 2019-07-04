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

/* @package: core_moodle
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 30-11-2017
 * @Description: Terms & Conditions Page.
*/

require_once('config.php');
$userrole = get_atalrolenamebyid($USER->msn);
$termsandcondition = "";

$termsandcondition = "<p>The 'ATL InnoNet' portal (henceforth referred to as 'the portal') has been developed by Atal Innovation Mission(henceforth referred to as AIM), 
NITI Aayog and IBM India Private Limited (henceforth referred to as “IBM”)  to facilitate the communication between Mentors of Change, selected in the Mentor 
India initiative and the Atal Tinkering Laboratory schools and their students. I understand and accept that Atal Innovation Mission (AIM) or NITI Aayog or IBM can in no way be 
held responsible in any way whatsoever, either jointly or severally for any misconduct done by any third party i.e. a Mentor, Student, School representative or any other 
representative of any third party thereof through the portal.  </p>";
$termsandcondition.= "<p><h4><b>GENERAL</b></h4></p>";
	
$termsandcondition.="<p>1.1. User privacy is considered paramount and I understand that all attempts are made to ensure that my data is kept adequately secure and is not shared with unauthorized personnel. However, this does not guarantee my privacy as an absolute and Atal Innovation Mission or NITI Aayog or IBM can in no way be held responsible for any invasion of my privacy in any manner whatsoever, through the portal.</p> 
<p>1.2. I understand and accept that when I publish content or information on the portal, it means that I am allowing everyone associated with the portal to access and use that information, and to associate it with me. </p> 
<p>1.3. I understand that my feedback or suggestions may or may not be used without any obligation to compensate me for them. I am also aware that I have no obligation to offer my feedback or suggestions regarding the portal unless requested to provide feedback by the operators of the portal    </p> 
<p>1.4. I understand that all efforts will be made to keep the portal safe, but this cannot be guaranteed. Atal Innovation Mission or NITI Aayog or IBM can in no way be held responsible for any misconduct done by any party through this portal.  </p> 
<p>1.5.  My help will be needed to keep the portal safe, which includes the following commitments by me: </p> 
<p>1.5.1. I will not post unauthorized commercial communications (including spam) on the portal.</p> 
<p>1.5.2. I will not collect other users' content or information, or otherwise access the portal, using automated means (such as harvesting bots, robots, spiders, or scrapers) without the prior written permission of Atal Innovation Mission, NITI Aayog and IBM.</p> 
<p>1.5.3. I will not upload viruses or other malicious code to the portal.</p> 
<p>1.5.4. I will not solicit login information or access an account belonging to someone else.</p> 
<p>1.5.5. I will not bully, intimidate, or harass any other user.</p> 
<p>1.5.6. I will not post content that is or may be reasonably considered to be hate speech, threatening, or pornographic; incites violence; or contains nudity or graphic or gratuitous violence.</p> 
<p>1.5.7. I will not use the portal to do anything unlawful, misleading, malicious, or discriminatory.</p> 
<p>1.5.8. I will not do anything that could disable, overburden, or impair the proper working or appearance of the portal, such as a denial of service attack or interference with page rendering or other portal functionality.</p> 
<p>1.5.9. I will not facilitate or encourage any violations of these Terms and Conditions.</p> 
<p>1.6. I will provide my real name and information, and the following commitments are being made by me relating to registering and maintaining the security of my account – </p> 
<p>1.6.1. I will not provide any false personal information on the portal or create an account for anyone other than myself (or the respective ATL students if, I am operating the portal on behalf of a school). </p> 
<p>1.6.2. I will not use the portal for any kind of direct or indirect commercial gain. </p> 
<p>1.6.3. I am at least 18 years old </p> 
<p>1.6.4. I will keep my contact details and other information accurate and up-to-date. </p> 
<p>1.6.5. I will not share my password, let anyone else access my account, or do anything else that might jeopardize the security of my account. </p> 
<p>1.6.6. I will not transfer my account to anyone. </p> 
<p>1.7. I will respect other user’s rights and abide by the following- </p> 
<p>1.7.1. I will not post content or take any action on the portal that infringes or violates anyone else's rights or otherwise violates the law. </p> 
<p>1.7.2. The portal can at its sole discretion remove any content or information that I post on the portal if it violates these terms and conditions. </p> 
<p>1.7.3. I will not use AIM, NITI Aayog’s or IBM’s Intellectual Property including copyrights, trademarks, etc. or any confusingly similar marks. </p> 
<p>1.7.4. I will not post anyone's identification documents or sensitive financial information on the portal. </p> 
<p>1.7.5. I agree that from time to time AIM, NITI Aayog or IBM may add upgrades, updates and additional features to improve, enhance, and further develop the portal. </p> 
<p>1.7.6. I will not modify, create derivative works of, decompile, or otherwise attempt to extract source code from the portal. </p> 
<p>1.8. I will be made aware of changes to these terms and conditions and be given the opportunity to review and accept the revised terms before continuing to use the portal. 
<p>1.9. My continued use of the portal, following notice of the changes to the terms and conditions constitutes my acceptance of the amended terms and conditions. </p> 
<p>1.10. Third-Party Material: Under no circumstances will AIM, NITI Aayog or IBM be liable in any way for any content or materials posted by me on the portal. I acknowledge and accept that AIM, NITI Aayog or IBM does not pre-screen content, and that AIM, NITI Aayog or IBM will have the right (but not the obligation) in their sole discretion to refuse, remove, or allow any content that is available via the portal. Without limiting the foregoing, AIM, NITI Aayog or IBM will have the right to remove any content that violates these terms of service or is deemed by AIM, NITI Aayog or IBM, in their sole discretion, to be otherwise objectionable. </p> 
<p>1.11. If I violate the letter or spirit of these terms and conditions, or otherwise create risk or possible legal exposure for AIM, NITI Aayog or IBM, all or part of the portal may be blocked from me. </p> 
<p>1.12. I agree to release, indemnify on demand AIM, NITI Aayog or IBM and hold their officers, employees, directors and agents harmless from any and all losses, damages, expenses, including reasonable attorneys' fees, costs, awards, fines, damages, rights, claims, actions of any kind and injury (including death) arising out of or relating to my use of the portal, my violation of these terms of service or my violation of any rights of another. </p> 
";

$termsandcondition.= "<p><h4><b>INTELLECTUAL PROPERTY RIGHTS</b></h4></p>";
$termsandcondition.="
<p>2.1. I grant to AIM and NITI Aayog, the worldwide, non-exclusive, perpetual, irrevocable, royalty-free, sublicensable, transferable right to use, exercise, commercialize, and exploit the copyright, publicity, trademark, and database rights with respect to any content that I publish or upload or otherwise share on the portal or in my role as a mentor. My content will generally just be used to but not limited to promote and showcase the Atal Innovation Mission, NITI Aayog, Atal Tinkering Laboratory & Mentor India community. I am responsible for the content I post, and I am representing to AIM and NITI Aayog that the content is my intellectual property and I have not infringed on anyone else’s intellectual property rights whatsoever. </p>
<p>2.2. I understand that whatever content I am posting on the portal is accessible to other users and similarly the content posted by other users is accessible to me. 
I agree to respect the copyright of material not belonging to me and not copy for my own use, content (including but not limited to images, videos, ideas, etc.) that does not belong to me. Furthermore, I agree that I release, indemnify on demand AIM, NITI Aayog or IBM and hold their officers, employees, directors and agents harmless from any and all losses, damages, expenses, including reasonable attorneys' fees, costs, awards, fines, damages, rights, claims, actions of any kind and injury (including death) arising out of or relating to any dispute over ownership of ideas/projects/innovations mentioned on the portal. </p>
";

if($userrole=="mentor"){
	//Terms & Conditions: Mentor;

$termsandcondition.="
FOR MENTORS

<p>3.1. I, as a mentor have been given access to this portal and am responsible for my own behaviour and conduct. I am expected to conduct myself professionally always and take extra care when interacting with students on the portal to ensure the students’ safety. If I am found to endanger any child or violate these terms and conditions in letter or spirit, I will be blocked from using the portal and appropriate legal action will be taken against me. Atal Innovation Mission or NITI Aayog or IBM can in no way be held responsible for any misconduct done through this portal by me.</p>

<p><h4><b>Terms of Engagement</b></h4></p>

<p>3.2.1. Atal Innovation Mission reserves the right to terminate my selection under the Mentor India initiative at its sole discretion.</p>
<p>3.2.2. I accept that the allocation of Atal Tinkering Labs for mentoring to selected mentors will be done strictly by Atal Innovation Mission. I also understand that while reasonable endeavours will be made to assist me in receiving the Atal Tinkering Labs of my choice, such a choice is in no way assured or guaranteed. Such a confirmed allocation once made will be considered final and there is no imperative on AIM, NITI Aayog to accommodate modifications subsequently.</p>
<p>3.2.3. I undertake that my engagement as a Mentor is on a pro-bono basis (i.e. without any remuneration, monetary or otherwise) including but not limited to the ATL school. I am aware that if I am found engaging in this type of activity, AIM reserves the right to terminate me from the Mentor India program immediately without providing any kind of justification. </p>
<p>3.2.4. I will not promote my products and / or services inside the school.</p>
<p>3.2.5. I will support and follow the school’s directives regarding protocol pertaining to interactions with the students. </p>
<p>3.2.6. I will not meet or take any school students outside the school premises without the explicit written permission of the school principal and the Atal Tinkering Lab In-Charge.</p>
<p>3.2.7. I am further aware that the interaction between students and the mentor through the portal developed for Mentor India will be moderated / monitored for suitability of content and language. I undertake that I shall maintain a strictly and thoroughly professional decorum while using the portal and interacting with students.</p>
<p>3.2.8. I will work with the respective Atal Tinkering Lab In-Charge to coordinate on the mentoring sessions, using this portal. I shall ensure that the coordination for these session takes place smoothly and that the school is not inconvenienced unduly.</p>
<p>3.2.9. I will dedicate time on a regular basis for the ATL. I understand that I am expected to spend 1 -2 hours per month during / after school hours. Furthermore, an ongoing commitment for at least one year with the school is expected of me (will typically involve ~40 weeks of academic operations in a year). </p>
<p>3.2.10. I am also aware that I am expected to interact with the other selected mentors through the portal developed for Mentor India. These interactions could include supporting and reviewing specific innovation projects, support in innovation competitions, and community outreach. The expected time commitment for such interactions is ~30 – 40 hours in a year.</p>
<p>3.2.11. I shall spread awareness about the program and provide regular feedback to increase the impact of the program as may be requested of me from time to time.</p>
<p>3.2.12. I understand that my performance and contributions will be monitored on a regular basis by Atal Innovation Mission. Atal Innovation Mission reserves the right to terminate my services as a mentor at any point of time without any justification. Atal Innovation Mission and NITI Aayog will not be responsible for my behaviour as a mentor. </p>
<p>3.2.13. I accept and agree that safety and security of school students is of utmost importance to Atal Innovation Mission, and hence Atal Innovation Mission reserves the right to take appropriate safety measures in coordination with Atal Tinkering Labs Advisory Committee, local administrative and law enforcement agencies etc.</p>
<p>3.2.14. In case of any dispute, the same shall be subject to the jurisdiction of the court of Delhi.</p>

<p><h4><b>Proposed Areas of Mentorship</b></h4></p> 

<p>3.3.1. The contributions expected of me are, but not limited to, one or more of the following areas:</p>
<p>3.3.1.1. Technical Know-How </p>
<p>3.3.1.1.1. Utilize available equipment to build products in various fields, including IoT, electronics, mechatronics, biomedical engineering, 3D printing, robotics, etc.</p>
<p>3.3.1.1.2. Help to build prototypes using the latest technologies.</p>
<p>3.3.1.2. Innovation and Design</p>
<p>3.3.1.2.1. Focus on problem solving including aspects of design thinking.</p>
<p>3.3.1.2.2. Inculcating a solution-oriented approach.</p>
<p>3.3.1.3. Inspirational</p>
<p>3.3.1.3.1. Emphasis on my personal journey and holistic soft skill development, learning from failures, and becoming self-motivated to strive for excellence.</p>
<p>3.3.1.3.2. Inculcate leadership skills, self-motivation and reflection on oneself</p>
<p>3.3.1.4. Business and Entrepreneurship</p>
<p>3.3.1.4.1. Encourage students to create a business model to form sustainable and successful enterprises</p>
<p>3.3.1.4.2. Instil and inspire students to become entrepreneurial leaders and encourage team building</p>
<p>3.3.1.4.3. Break stereotypes and bias to bring about mind set and behavioural change</p>

<p><h4><b>Roles and Responsibilities for the mentor </b></h4></p> 
<p>3.4.1. I agree to the following roles, obligations and responsibilities as a mentor for the students of an ATL - </p>
<p>3.4.1.1. In-person mentoring session at least once a month for 1 – 2 hours on an ongoing basis for at least 1 year (typically 40 academic weeks). </p>
<p>3.4.1.2. Such sessions will be focused on guiding and nurturing students in Atal Tinkering Labs for problem formulation, finding solutions to common problems faced in day to day life, and finally tinkering on ideas converting them into scalable solutions.</p>
<p>3.4.1.3. Reasonable availability to help students on the portal developed for Mentor India, with typical expected response time of 72 hours from the selected mentors.</p>
<p>3.4.1.4. Provide support and review specific innovation projects. Give constructive feedback to school management/ individual students to allow further improvement at regular intervals.</p>
<p>3.4.1.5. Introduce students to relevant extended community / networks. </p>
<p>3.4.1.6. Reasonable participation in social media forums organized by Atal Tinkering Labs. </p>
<p>3.4.1.7. Support the school in reaching out to communities in neighbouring areas. </p>
<p>3.4.1.8. Help the schools to arrange for sessions in the lab in after school hours or in camp mode or during vacations or on weekends (based on mutual availability of selected mentors and students).</p>

<p><h4><b>Special Intellectual Property Rights Consideration </b></h4></p> 
<p>3.5.1. I will not plagiarize, use, copy or reuse any idea, concept, innovation or intellectual property, protected or not, that is or may be created by any student(s) and is discussed, comes into my possession, or is shared with me by any means of communication whatsoever. I further acknowledge that the intellectual property right for the ideas/projects/innovations created during this process belongs solely to the students.</p> 
<p>3.5.2. I agree to maintain confidentiality about the student’s ideas/projects/innovations that I am mentoring and agree that the intellectual property created out of this interaction belongs to the students only.</p> 
 

<p><h4><b>Child Protection Policy </b></h4></p>
<p>3.6.1. I agree to follow this child protection policy which contains guidelines to ensure the safety of the students under my mentorship</p>

<p><h4><b>Appropriate Standards of Behaviour </b></h4></p>

<p>3.6.2.1. I will – </p>
<p>3.6.2.1.1. Provide an enabling environment for children’s personal, physical, social, emotional, moral and intellectual development.</p>
<p>3.6.2.1.2. Encourage and respect children’s voices and views.</p>
<p>3.6.2.1.3. Be inclusive and involve all children without selection or exclusion based on gender, disability, ethnicity, religion or any other status.</p>
<p>3.6.2.1.4. Be aware of the potential for peer abuse (e.g. children bullying, discriminating against, victimizing or abusing children).</p>
<p>3.6.2.1.5. Develop special measures/supervision to protect younger and especially vulnerable children from peer and adult abuse.</p>
<p>3.6.2.1.6. Be aware of high-risk peer situations (e.g. unsupervised mixing of older and younger children and possibilities of discrimination against minors).</p>
<p>3.6.2.1.7. Develop clear rules to address specific physical safety issues relative to the local physical environment of a project (e.g. for projects based near water, heavy road traffic, railway lines).</p>
<p>3.6.2.1.8. Avoid placing myself in a compromising or vulnerable position when meeting with children (e.g. being alone with a child in any circumstances which might potentially be questioned by others).</p>
<p>3.6.2.1.9. Meet with a child in a central, public location whenever possible.</p>
<p>3.6.2.1.10. Immediately report the circumstances of any situation which occurs which may be subject to misinterpretation to the appropriate authority.</p>
<p>3.6.2.1.11. Report suspected or alleged abuse to the appropriate authority.</p>

<p><h4><b>Inappropriate Standards of Behaviour</b></h4></p>


<p>3.6.3.1. I will not – </p>
<p>3.6.3.1.1. Hit or otherwise physically assault a student that I am mentoring or who otherwise belongs to an ATL or AIM school.</p>
<p>3.6.3.1.2. Use language that will mentally or emotionally abuse or adversely impact any student that I am mentoring or who otherwise belongs to an ATL or AIM school.</p>
<p>3.6.3.1.3. Act in any way that intends to embarrass shame, humiliate, or degrade a student that I am mentoring or who otherwise belongs to an ATL or AIM school.</p>
<p>3.6.3.1.4. Show discrimination of race, culture, age, gender, disability, religion, sexuality, political persuasion or any other status towards a student that I am mentoring or who otherwise belongs to an ATL or AIM school.</p>
<p>3.6.3.1.5. Develop a sexual relationship with a student that I am mentoring or who otherwise belongs to an ATL or AIM school.</p>
<p>3.6.3.1.6. Kiss, hug, fondle, rub, or touch a child in an inappropriate or culturally insensitive way a student that I am mentoring or who otherwise belongs to an ATL or AIM school.</p>
<p>3.6.3.1.7. Do things of a personal nature that a child could do for him/herself, including dressing, bathing, and grooming, to a student that I am mentoring or who otherwise belongs to an ATL or AIM school.</p>
<p>3.6.3.1.8. Encourage any romantic engagements or feelings expressed by a student that I am mentoring or who otherwise belongs to an ATL or AIM school.</p>
<p>3.6.3.1.9. Initiate physical contact (e.g. holding hands) with a student that I am mentoring or who otherwise belongs to an ATL or AIM school.</p>
<p>3.6.3.1.10. Suggest inappropriate behaviour or relations of any kind with a student that I am mentoring or who otherwise belongs to an ATL or AIM school.</p>
<p>3.6.3.1.11. Allow children or students to engage in sexually provocative games with each other.</p>
<p>3.6.3.1.12. Stand aside when I see inappropriate actions inflicted by children on other children merely because it is frequent and commonplace.</p>

<p><h4><b>Ramifications of misconduct</b></h4></p>


<p>3.6.4.1. If I am found engaging in any kind of misconduct towards a student that I am mentoring or who otherwise belongs to an ATL or AIM school, I will be immediately removed from the Mentor of Change initiative and where applicable will be prosecuted to the full extent of the law.</p>

<p><h4><b>OTHER</b></h4></p>


<p>4.1. I agree that - </p>
<p>4.1.1. If any portion of these terms and conditions is found to be unenforceable, the remaining portion will remain in full force and effect.</p>
<p>4.1.2. If there is a failure to enforce any of these terms and conditions, it will not be considered a waiver.</p>
<p>4.1.3. Nothing in these terms and conditions shall prevent Atal Innovation Mission, NITI Aayog or IBM from complying with the law.</p>
<p>4.1.4. I will comply with all applicable laws when using or accessing the portal.</p>
";
	
} elseif($userrole=="incharge"){
	//Terms & Conditions: InCharge;
	$termsandcondition.="<p><h4><b>For Schools</b></h4></p> 

<p>3.1. I, as the authorized representative of my school, am responsible for the proper usage of the portal by my schools’ students. As I will be creating the logins for the students, I am expected to monitor the usage of the portal by my students and ensure if required that the student – mentor interactions on the portal are healthy and do not compromise the safety of the students in any way. I also acknowledge that Atal Innovation Mission or NITI Aayog are in no way responsible for any misconduct done through this portal.</p> 
<p>3.2. I will personally ensure that no student below the age of 15 years is given access to the portal after verifying the students’ Government issued identification. I will create the profile for my students on the portal and shall ensure that the students whose profiles I am creating are 15 years of age or more.</p> 

<p>3.3. Special Intellectual Property Rights Consideration</p> 
<p>3.3.1. I will not plagiarize, use, copy or reuse any idea, concept, innovation or intellectual property, protected or not, that is or may be created by any student(s) and is discussed, comes into my possession, or is shared with me by any means of communication whatsoever. I further acknowledge that the intellectual property right for the ideas/projects/innovations created during this process belongs solely to the students.</p> 
<p>3.3.2. I agree to maintain confidentiality about the student’s ideas/projects/innovations that I am overseeing on behalf of the students and school and agree that the intellectual property created out of this interaction belongs to the students only.</p> 
<p>3.3.3. . I agree to help and support the students in protecting this intellectual property.</p> 
<p>3.3.4. I agree that I release, indemnify on demand AIM, NITI Aayog or IBM and hold their officers, employees, directors and agents harmless from any and all losses, damages, expenses, including reasonable attorneys' fees, costs, awards, fines, damages, rights, claims, actions of any kind and injury (including death) arising out of or relating to any dispute over ownership of ideas/projects/innovations mentioned on the portal.</p> 

<p><h4><b>OTHER</b></h4></p> 

<p>4.1. If any portion of these terms and conditions is found to be unenforceable, the remaining portion will remain in full force and effect.</p> 
<p>4.2. If there is a failure to enforce any of these terms and conditions, it will not be considered a waiver.</p> 
<p>4.3. Nothing in these terms and conditions shall prevent Atal Innovation Mission, NITI Aayog or IBM from complying with the law.</p> 
<p>4.4. I will comply with all applicable laws when using or accessing the portal.</p> 
";

} else{
	//Terms & Conditions: Student;
$termsandcondition = "
 <p><h4><b>TERMS AND CONDITIONS FOR STUDENTS</b></h4></p> 

<p>The 'ATL InnoNet' portal (henceforth referred to as 'the portal') has been developed by Atal Innovation Mission(AIM), NITI Aayog and IBM India Private Limited (henceforth referred to as “IBM”) to facilitate the communication between the Mentors of Change, selected in the Mentor India initiative and the Atal Tinkering Laboratory (ATL) 
schools and their students. I understand and accept that AIM or NITI Aayog or IBM can in no way be held responsible in any way whatsoever either jointly or severally for any misconduct 
done by any third party i.e. a Mentor, Student, School representative or any other representative of any third party thereof through the portal.  </p>

<p><h4><b>GENERAL</b></h4></p> 

<p>1.1. I confirm that I am over 15 years of age.</p> 
<p>1.2. User privacy is considered paramount and I understand that all attempts are made to ensure that my data is kept adequately secure and is not shared with unauthorized personnel. However, this does not guarantee my privacy as an absolute and AIM or NITI Aayog or IBM can in no way be held responsible for any invasion of my privacy in any manner whatsoever through the portal. </p> 
<p>1.3. I understand and accept that when I publish content or information on the portal, it means that I am allowing everyone associated with the portal to access and use that information, and to associate it with me.</p> 
<p>1.4. I further understand that my feedback or suggestions regarding the portal or any of its functions may or may not be used without any obligation to compensate me for them. I am also aware that I have no obligation to offer my feedback or suggestions regarding the portal unless requested to provide feedback by the operators of the portal. </p> 
<p>1.5. I understand that all efforts will be made to keep the portal safe from misuse of any kind whatsoever, but this cannot be guaranteed. AIM or NITI Aayog or IBM can in no way be held responsible whatsoever for any misconduct by any party through this portal.  </p> 
<p>1.6.  Further, I hereby represent, undertake and warrant that I will:  </p> 
<p>1.6.1. not post unauthorized commercial communications (including spam) on the portal.</p> 
<p>1.6.2. not collect other users' content or information, or otherwise access the portal, using automated means (such as harvesting bots, robots, spiders, or scrapers) without the prior written permission of AIM, NITI Aayog and IBM.</p> 
<p>1.6.3.  not upload viruses or other malicious code to the portal.</p> 
<p>1.6.4.  not solicit login information or access an account belonging to someone else.</p> 
<p>1.6.5.  not bully, intimidate, or harass any other user.</p> 
<p>1.6.6.  not post content that is or may be reasonably considered to be hate speech, threatening, or pornographic; incites violence; or contains nudity or graphic or gratuitous violence.</p> 
<p>1.6.7.  not use the portal to do anything unlawful, misleading, malicious, or discriminatory.</p> 
<p>1.6.8.  not do anything that could disable, overburden, or impair the proper working or appearance of the portal, such as a denial of service attack or interference with page rendering or other portal functionality.</p> 
<p>1.6.9.  not facilitate or encourage any violations of these terms and conditions.</p> 
<p>1.7. I will provide my real name and information, and the following commitments are being made by me relating to registering and maintaining the security of my account –</p> 
<p>1.7.1. I will not provide any false personal information on the portal or create an account for anyone other than myself (or the respective ATL students if I am operating the portal on behalf of a school).</p> 
<p>1.7.2. I will not use the portal for any kind of direct commercial gain.</p> 
<p>1.7.3. I will keep my contact details and other information accurate and up-to-date.</p> 
<p>1.7.4. I will not share my password, let anyone else access my account, or do anything else that might jeopardize the security of my account.</p> 
<p>1.7.5. I will not transfer my account to anyone.</p> 
<p>1.8. I will respect other user’s rights and abide by the following-</p> 
<p>1.8.1. I will not post content or take any action on the portal that infringes or violates anyone else's rights or otherwise violates the law.</p> 
<p>1.8.2. The portal can at its sole discretion remove any content or information that I post if  it violates these terms and conditions.</p> 
<p>1.8.3. I will not use AIM, NITI Aayog’s or IBM’s Intellectual Property including copyrights, trademarks, etc. or any confusingly similar marks.</p> 
<p>1.8.4. I will not post anyone's identification documents or sensitive financial information on the portal.</p> 
<p>1.8.5. I agree that from time to time, AIM, NITI Aayog or IBM may add upgrades, updates and additional features to improve, enhance, and further develop the portal.</p> 
<p>1.8.6. I will not modify, create derivative works of, decompile, or otherwise attempt to extract source code from the portal.</p> 
<p>1.9. I will be made aware of changes to these terms and conditions and be given the opportunity to review and accept the revised terms before continuing to use the portal.</p> 
<p>1.10. My continued use of the portal, following notice of the changes to the terms and conditions constitutes my acceptance of the amended terms and conditions.</p> 
<p>1.11. Third-Party Material: Under no circumstances will AIM, NITI Aayog or IBM be liable in any way for any content or materials posted by me on the portal. I am aware that AIM, NITI Aayog or IBM does not pre-screen content, and that AIM, NITI Aayog or IBM will have the right (but not the obligation) in their sole discretion to refuse, remove, or allow any content that is available via the portal. Without limiting the foregoing, AIM, NITI Aayog or IBM will have the right to remove any content that violates these terms of service or is deemed by AIM, NITI Aayog or IBM, in their sole discretion, to be otherwise objectionable.</p> 
<p>1.12. If I violate the letter or spirit of these terms and conditions, or otherwise create risk or possible legal exposure for AIM, NITI Aayog or IBM, all or part of the portal may be blocked from me. </p> 
<p>1.13. I agree to release, indemnify on demand AIM, NITI Aayog or IBM and hold their officers, employees, directors and agents harmless from any and all losses, damages, expenses, including reasonable attorneys' fees, costs, awards, fines, damages, rights, claims, actions of any kind and injury (including death) arising out of or relating to my use of the portal, my violation of these terms of service or my violation of any rights of another. </p> 

<p><h4><b>INTELLECTUAL PROPERTY RIGHTS</b></h4></p> 

<p>2.1. I grant to AIM and NITI Aayog, the worldwide, non-exclusive, perpetual, irrevocable, royalty-free, sublicensable, transferable right to use, exercise, commercialize, and exploit the copyright, publicity, trademark, and database rights with respect to any content that I upload or publish on the portal. My content will generally just be used to but not limited to promote and showcase the AIM, NITI Aayog, ATL & Mentor India community. I am responsible for the content I post, and I am representing to AIM and NITI Aayog that the content is my intellectual property and I have not infringed on anyone else’s intellectual property rights whatsoever.</p> 
<p>2.2. I understand that whatever content I am posting on the portal is accessible to other users and similarly the content posted by other users is accessible to me. I agree to respect the copyright of material not belonging to me and not copy for my own use, content (including but not limited to images, videos, ideas, etc.) that does not belong to me.</p> 

<p><h4><b>For Students</b></h4></p> 
<p>3.1. I, as a student of an Atal Tinkering Laboratory have been given access to the portal by my school to facilitate my interaction with Mentors. I will conduct myself with the proper decorum and use this opportunity to learn under the guidance of my respective mentor(s). Atal Innovation Mission or NITI Aayog or IBM can in no way be held responsible for any misconduct done through this portal.</p> 
 
<p>3.2. Special Intellectual Property Rights Consideration
<p>3.2.1. I agree that the intellectual property created when working together using the individual project collaboration feature, under the guidance of the Mentor and the school belongs to all students involved in the project and I will not claim sole ownership over it. Furthermore, I agree that I release, indemnify on demand AIM, NITI Aayog or IBM and hold their officers, employees, directors and agents harmless from any and all losses, damages, expenses, including reasonable attorneys' fees, costs, awards, fines, damages, rights, claims, actions of any kind and injury (including death) arising out of or relating to any dispute over ownership of ideas/projects/innovations mentioned on the portal.</p> 

<p><h4><b>OTHER</b></h4></p> 

<p>4.1. If any portion of these terms and conditions is found to be unenforceable, the remaining portion will remain in full force and effect.</p> 
<p>4.2. If there is a failure to enforce any of these terms and conditions, it will not be considered a waiver.</p> 
<p>4.3. Nothing in these terms and conditions shall prevent Atal Innovation Mission, NITI Aayog or IBM from complying with the law.</p> 
<p>4.4. I will comply with all applicable laws when using or accessing the portal.</p> 
";

}

echo "<div style='width:90%'> ".$termsandcondition."</div>";