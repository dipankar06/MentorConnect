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

/* @package: core_create
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 18-07-2018
 * @Description: External Library functions for Create Module.
 This File is used in other Modules outside Project folder.
*/

require_once($CFG->libdir.'/filelib.php');

/*
 * Function to return pagination
 * Returns HTML content
*/
function paginate_function($item_per_page, $current_page, $total_records, $total_pages,$class)
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ 
		//verify total pages and current page number
        $pagination .= '<ul class="forum-pagination">';

        $right_links    = $current_page + 3;
		$previous       = $current_page - 3; //previous link
		 if($current_page<=2)
			 $previous  =1; 
        $next           = $current_page + 1; //next link
        $first_link     = true; //boolean var to decide our first link

        if($current_page > 1){
            $previous_link = ($previous==0)?1:$previous;
            $pagination .= "<li class='first'><a  class='movetopage-$class'  id='1' title='First'>&laquo;</a></li>"; //first link
            $pagination .= "<li><a class='movetopage-$class'  id='$previous_link' title='Previous'><</a></li>"; //previous link
                for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
                    if($i > 0){
                        $pagination .= "<li><a class='movetopage-$class'  id='$i'>$i</a></li>";
                    }
                }  
            $first_link = false; //set first link to false
        }

        if($first_link){ //if current active page is first link
            $pagination .= '<li class="first active">'.$current_page.'</li>';
        }elseif($current_page == $total_pages){ //if it's the last active link
            $pagination .= '<li class="last active">'.$current_page.'</li>';
        }else{ //regular current link
            $pagination .= '<li class="active">'.$current_page.'</li>';
        }

        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .=  "<li><a class='movetopage-$class'  id='$i'>$i</a></li>";
            }
        }
        if($current_page < $total_pages){
                $next_link = ($i > $total_pages)? $total_pages : $i;
                $pagination .= "<li><a class='movetopage-$class'  id='$next_link' title='Next'>></a></li>"; //next link
                $pagination .= "<li class='last'><a class='movetopage-$class' id='$total_pages' title='Last'>&raquo;</a></li>"; //last link
        }

        $pagination .= '</ul>';
    }
    return $pagination; //return pagination links
}


/*
 * New Function to return pagination
 * Returns HTML content
*/
function paginate_newfunction($item_per_page, $current_page, $total_records, $total_pages,$class='')
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ 
		//verify total pages and current page number
        $pagination .= '<ul class="forum-pagination">';

        $right_links    = $current_page + 3;
		$previous       = $current_page - 3; //previous link
		 if($current_page<=2)
			 $previous  =1; 
        $next           = $current_page + 1; //next link
        $first_link     = true; //boolean var to decide our first link

        if($current_page > 1){
            $previous_link = ($previous==0)?1:$previous;
            $pagination .= "<li class='first'><a  class='movetopage'  id='1' title='First'>&laquo;</a></li>"; //first link
            $pagination .= "<li><a class='movetopage'  id='$previous_link' title='Previous'><</a></li>"; //previous link
                for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
                    if($i > 0){
                        $pagination .= "<li><a class='movetopage'  id='$i'>$i</a></li>";
                    }
                }  
            $first_link = false; //set first link to false
        }

        if($first_link){ //if current active page is first link
            $pagination .= '<li class="first active">'.$current_page.'</li>';
        }elseif($current_page == $total_pages){ //if it's the last active link
            $pagination .= '<li class="last active">'.$current_page.'</li>';
        }else{ //regular current link
            $pagination .= '<li class="active">'.$current_page.'</li>';
        }

        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .=  "<li><a class='movetopage'  id='$i'>$i</a></li>";
            }
        }
        if($current_page < $total_pages){
                $next_link = ($i > $total_pages)? $total_pages : $i;
                $pagination .= "<li><a class='movetopage'  id='$next_link' title='Next'>></a></li>"; //next link
                $pagination .= "<li class='last'><a class='movetopage' id='$total_pages' title='Last'>&raquo;</a></li>"; //last link
        }

        $pagination .= '</ul>';
    }
	//return pagination links
    return $pagination;
}

?>