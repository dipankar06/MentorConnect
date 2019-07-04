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
 * @CreatedOn: 30-03-2018
 * @Description: To filter Bad words from Forum Post.
*/

require_once(__DIR__ . '/../config.php');

define('AJAX_SCRIPT', true);

if(isset($_POST) && isset($_POST['name'])){
	//Called from Forum New Post
	$outcome = new stdClass();
	$outcome->success = 0;
	$outcome->msg = "no";
	$outcome->replyhtml = 'no badwords found';

	$data = (object) $_POST;
	$message = $data->message;
	$name = $data->name;
	$wordsarray = array();
	$wordsarray = filterbad_words($name,$wordsarray);
	$wordsarray = filterbad_words($message,$wordsarray);
	
	if(count($wordsarray)>0){
		$wordsstr = implode(",",$wordsarray);
		$html = "Inappropriate words are not allowed: ".$wordsstr ;
		$outcome->success = 1;
		$outcome->msg = "badwords found !";
		$outcome->replyhtml = $html;
	}

	echo json_encode($outcome);
	die();
}

//This function called from Ajax Reply Post
function filter_postreply($replycontent){
	$wordsarray = array();
	$html = "";
	$wordsarray = filterbad_words($replycontent, $wordsarray);
	if(count($wordsarray)>0){
		$wordsstr = implode(", ",$wordsarray);
		$html = "Inappropriate words are not allowed:  ".$wordsstr ;	
	}
	return $html;
}

function filterbad_words($name, $wordsarray){
	$badWords = getbadwords();
	$name = strtolower($name);
	$name = preg_replace('/[^A-Za-z0-9\ ]/', '', $name); // Removes special chars.
	$words = "";
	$matches = array();
	$matchFound = preg_match_all("/\b(" . implode($badWords,"|") . ")\b/i", $name, $matches );
	if($matchFound) {
		$words = array_unique($matches[0]);
		foreach($words as $word) {
			$wordsarray[]= $word;
		}
		$wordsarray = array_unique($wordsarray);
	}
	return $wordsarray;
}

//Badwords Dictionary
function getbadwords(){
	return array("amateur","anal","anal impaler","anal leakage","anilingus","anus","arrse","ass","arsehole","asshole","assfucker","asses","assfukka",
	"bitch","ballbag","ballsack","bareback","bangbros","bastard","beastial","bellend","bimbos","bestiality","bimbos","birdlock","bloody","blowjob","blumpkin","bollock","buttocks",
	"boner","boob","boobs","booobs","boooobs","booooobs","booooooobs","breasts","bugger","bum","busty","butt","butthole","buthole","cock","cocksucker","chink","choade","cipa","chick","chicks",
	"clit","clusterfuck","fuckyou","fucky","cockhead","cocks","cocksuck","cocksucker","cocksucked","cooksucks","cok","coon","cocksuka","cornhole","cox","crap","crrap","craap",
	"cum","cunt","damm","dick","dickhead","dildo","dink","doggie","donkey","doosh","dog","doosh","dyke","ejaculate","ejaculates","ejaculating","ejaculated","erotic","fuck","fucker",
	"fuckyou","facial","fag","fagging","faggot","faggs","fanny","fatass","fcuk","fcuker","fcuking","fucking","fingering","fingerfuck","fingerfucked","fistfuck","fucks","fux","fuk","gangbang",
	"gangrape","gay","gays","gaysex","gaylord","goddam","goddamn","hell","horsesit","hoar","hoare","homo","homosex","hotsex","horny","jap","jackass","jiz","jerk","jeark","jism","jiism","jissm","jigolo",
	"jizm","knob","kum","krums","l3itch","lust","lmao","lusting","masterbat","masterba8","masterbation","masterbations","masterbate","motherfucker","motherfuck","mothafucka","mothafuck","motherfucking",
	"motherfuckers","mothafucks","muff","nazi","negro","nigger","mutherfucker","nuts","omg","nutsack","orgasm","orgasim","orgasims","orgasms","porn","p0rn","pawn","pecker","penis","pennis","peenis","phonesex","punk",
	"piss","pighead","phukking","phuking","phuck","pimps","pimp","pissed","pissedoff","ptube","pussy","pussi","pussies","queer","retard","shit","sex","sexy","seex","sexx","screwing","semen","sht","shag","shagger","shagging","shaggin",
	"shemale","shithead","shitdick","shitings","shitfull","shitey","slut","smut","sonofabitch","spunk","teets","tit","testicle","testical","titfuck","tits","titt","titties","tittie5","trud","vagina","vulgaur","virgin","whore","vulva","wang",
	"whoar","wtf","xrated","xxx","2g1c","hhhindigaliiii","gandu","saala","ganduu","kamine","kaminey","kaminaa","kutta","kuttaa");
}
?>
