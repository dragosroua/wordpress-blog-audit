<?php
/*
Plugin Name: BlogAudit 
Plugin URI: http://www.edragonu.ro/wordpress-plugin-blog-audit
Description: Audit your blogging performance
Author: Dragos Roua
Version: 0.1 beta
Author URI: http://www.edragonu.ro/
*/ 

/*  Copyright 2009  Dragos Roua  (email : dragos@mirabilis.ro)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$plugin_info = array();
$plugin_info['version'] = '0.1 beta';
$plugin_info['name'] = 'Blog Audit';
$plugin_info['author'] = 'Dragos Roua';

add_action('admin_menu', 'blog_audit_menu_pages', 1);


function blog_audit_menu_pages(){
    add_menu_page('Blog Audit', 'Blog Audit', 8, 'blog-audit-menu', 'blog_audit_menu_page');
    add_options_page(__('Blog Audit Options', 'blog_audit'), __('Blog Audit', 'blog_audit'), 8, 'blog-audit', 'blog_audit_options_page');

    /*
    * initialization stuff, adding default options for weekly and daily goals in posting speed to 0
    */
    if(!get_option('audit_posts_per_day')) add_option('audit_posts_per_day', '0');
    if(!get_option('audit_posts_per_week')) add_option('audit_posts_per_week', '0');
    if(!get_option('audit_comments_per_post')) add_option('audit_comments_per_post', '0');
    if(!get_option('audit_comments_per_day')) add_option('audit_comments_per_day', '0');
    if(!get_option('audit_comments_per_week')) add_option('audit_comments_per_week', '0');
    if(!get_option('audit_pingbacks_per_post')) add_option('audit_pingbacks_per_post', '0');
    if(!get_option('audit_pingbacks_per_day')) add_option('audit_pingbacks_per_day', '0');
    if(!get_option('audit_pingbacks_per_week')) add_option('audit_pingbacks_per_week', '0');  
}

function blog_audit_admin_footer() {
	$output = array();
        $output[] = 'Blog Audit plugin';
        $output[] = 'Version 0.1 beta';
        $output[] = 'by Dragos Roua ';

	$donate_url = 'http://www.edragonu.ro/donate/';
        $colour = '#ff' . dechex(mt_rand(0, 255)) . '00';
        $output[] = '<a href="' . $donate_url . '" style="font-weight: bold;color: '.$colour.';" rel="nofollow" title="If you like blog audit plugin feel free to make a donation.">Donate</a>';
       echo implode(' | ', $output) . '<br />';
}

function blog_audit_options_page(){
        echo '<div class="wrap"><h2>';
        _e('Blog Audit ', 'blog_audit');
        echo '</h2>';
        
        echo '<form method="post" action="options.php">';
        wp_nonce_field('update-options');
        echo '
            <table class="form-table">
            
            <tr valign="top">
            <th scope="row">Posts Per Day</th>
            <td><input type="text" name="audit_posts_per_day" value="'.get_option('audit_posts_per_day').'" />&nbsp;0 for not set</td>
            </tr>
             
            <tr valign="top">
            <th scope="row">Posts Per Week</th>
            <td><input type="text" name="audit_posts_per_week" value="'.get_option('audit_posts_per_week').'" />&nbsp;0 for not set</td>
            </tr>

            <tr valign="top">
            <th scope="row">Comments Per Post</th>
            <td><input type="text" name="audit_comments_per_post" value="'.get_option('audit_comments_per_post').'" />&nbsp;0 for not set</td>
            </tr>
                        
            <tr valign="top">
            <th scope="row">Comments Per Day</th>
            <td><input type="text" name="audit_comments_per_day" value="'.get_option('audit_comments_per_day').'" />&nbsp;0 for not set</td>
            </tr>
             
            <tr valign="top">
            <th scope="row">Comments Per Week</th>
            <td><input type="text" name="audit_comments_per_week" value="'.get_option('audit_comments_per_week').'" />&nbsp;0 for not set</td>
            </tr>            

            <tr valign="top">
            <th scope="row">Pingbacks Per Post</th>
            <td><input type="text" name="audit_pingbacks_per_post" value="'.get_option('audit_pingbacks_per_post').'" />&nbsp;0 for not set</td>
            </tr>
                        
            <tr valign="top">
            <th scope="row">Pingbacks Per Day</th>
            <td><input type="text" name="audit_pingbacks_per_day" value="'.get_option('audit_pingbacks_per_day').'" />&nbsp;0 for not set</td>
            </tr>
             
            <tr valign="top">
            <th scope="row">Pingbacks Per Week</th>
            <td><input type="text" name="audit_pingbacks_per_week" value="'.get_option('audit_pingbacks_per_week').'" />&nbsp;0 for not set</td>
            </tr>            
            </table>
            
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="audit_posts_per_day,audit_posts_per_week,audit_comments_per_post,audit_comments_per_day,audit_comments_per_week,audit_pingbacks_per_post,audit_pingbacks_per_day,audit_pingbacks_per_week" />
            
            <p class="submit">
            <input type="submit" name="Submit" value="'.__('Save Changes').'" />
            </p>
            
            </form>
            </div>';

       
        add_action('in_admin_footer', 'blog_audit_admin_footer' );
}



function blog_audit_menu_page(){

$audit_months = array(
        '1' => 'January',
        '2' => 'February',
        '3' => 'March',
        '4' => 'April',
        '5' => 'May',
        '6' => 'June',
        '7' => 'July',
        '8' => 'August',
        '9' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December');


        echo '<div class="wrap"><h2>';
        _e('Blog Audit ', 'blog_audit');
        echo '</h2>';

        $posts=audit_posting_speed();
        $comments=audit_comments_density();
        $pingbacks=audit_pingback_volume();
        $category_distribution=audit_category_distribution();
        
        
        echo "<div id='dashboard-widgets'>";        
        echo "<div class='dashboard-widget-holder'>";
        echo "<table align='left' cellpadding='5' cellspacing='3'>";
        echo "<tr><td valign='top'><div class='dashboard-widget'>";
        echo "<h3 class='dashboard-widget-table'>Posting Speed</h3>";
            echo "<table border='0' cellpadding='3' cellspacing = '2'>";
            echo "<tr bgcolor='#cee'><th>Month</th><th>Total</th><th>Per Day</th><th>Per Week</th><th>Goal Matching</th></tr>";
            $i = 0;
            foreach($posts as $post){
                /**
                * setting the number of days in each month
                */
                if($post->month == date('m', mktime())){
                    $no_of_days = date('d', mktime());
                }
                else if($post->month != date('m', mktime())){
                    if($post->month == 1) $no_of_days = 31;
                    if($post->month == 2) $no_of_days = 28; // check for bisect years
                    if($post->month == 3) $no_of_days = 31;
                    if($post->month == 4) $no_of_days = 30;
                    if($post->month == 5) $no_of_days = 31;
                    if($post->month == 6) $no_of_days = 31;
                    if($post->month == 7) $no_of_days = 30;
                    if($post->month == 8) $no_of_days = 31;
                    if($post->month == 9) $no_of_days = 30;
                    if($post->month == 10) $no_of_days = 31;
                    if($post->month == 11) $no_of_days = 30;
                    if($post->month == 12) $no_of_days = 31;
                }
                
                $no_of_weeks = number_format($no_of_days / 7);
                $post_month = $post->month;
                /*
                * getting the average posts per day and week
                */
                $avg_posts_daily = number_format($post->posts / $no_of_days, 2);
                $avg_posts_weekly= number_format($post->posts / $no_of_weeks, 2);

                /*
                * calculate the goal matching
                */
                $weekly_goal = get_option('audit_posts_per_week');
                $daily_goal = get_option('audit_posts_per_day');      
                if($daily_goal != 0){
                    $daily_performance = number_format( - (100 - (($avg_posts_daily * 100) / $daily_goal)), 2)."%";
                    if($daily_performance > 0 ) $daily_performance = "+".$daily_performance;
                    if($avg_posts_daily < $daily_goal){
                        $daily_color = "red";
                    }
                    else 
                    {
                        $daily_color = "green";
                    }
                } else $daily_performance = " -- ";
                if($weekly_goal != 0){
                    $weekly_performance = number_format(- (100 - (($avg_posts_weekly * 100) / $weekly_goal)), 2)."%";
                    if($weekly_performance > 0) $weekly_performance = "+".$weekly_performance; 
                    if($avg_posts_weekly < $weekly_goal){
                        $weekly_color = "red";
                    }
                    else 
                    {
                        $weekly_color = "green";
                    }

                } else $weekly_performance = " -- ";
                if($i % 2) $bgcolor = "#fff";
                else $bgcolor = "#eef";
                echo "<tr bgcolor=$bgcolor>";
                echo "<td>".$audit_months[$post->month]."</td><td align='center'>".$post->posts."</td>";
                echo "<td align='center'>$avg_posts_daily</td>";
                echo "<td align='center'>$avg_posts_weekly</td>";                
                /*
                get the goal setting option for posting speed
                match it here and display it
                */
                echo "<td><span style='color: ".$daily_color."'>".$daily_performance."</span> / <span style='color: ".$weekly_color."'>".$weekly_performance."</span></td>";
                echo "</tr>";
                $table_posts[$i] = $post->posts; // we will use this later in the comments per post calculation, next dasboard object
                $i++; // alternate color hook
            }
            echo "</table>";
            
            
            

        echo "</div></td><td valign='top'>";
        echo "<h3>Comments Density</h3>";
        echo "<table border='0' cellpadding='3' cellspacing = '2'>";
            echo "<tr bgcolor='#cee'><th>Month</th><th>Total</th><th>Per Post</th><th>Per Day</th><th>Per Week</th><th>Goal Matching</th></tr>"; 
            $i = 0;
            foreach($comments as $comment){
                /**
                * setting the number of days in each month
                */
                if($comment->month == date('m', mktime())){
                    $no_of_days = date('d', mktime());
                }
                else if($comment->month != date('m', mktime())){
                    if($comment->month == 1) $no_of_days = 31;
                    if($comment->month == 2) $no_of_days = 28; // check for bisect years
                    if($comment->month == 3) $no_of_days = 31;
                    if($comment->month == 4) $no_of_days = 30;
                    if($comment->month == 5) $no_of_days = 31;
                    if($comment->month == 6) $no_of_days = 31;
                    if($comment->month == 7) $no_of_days = 30;
                    if($comment->month == 8) $no_of_days = 31;
                    if($comment->month == 9) $no_of_days = 30;
                    if($comment->month == 10) $no_of_days = 31;
                    if($comment->month == 11) $no_of_days = 30;
                    if($comment->month == 12) $no_of_days = 31;
                }
                
                $no_of_weeks = number_format($no_of_days / 7);
                /*
                * getting the average comments per day and week
                */
                $avg_comments_daily = number_format($comment->comments / $no_of_days, 2);
                $avg_comments_weekly= number_format($comment->comments / $no_of_weeks, 2);
                $avg_comments_per_post = number_format($comment->comments / $table_posts[$i], 2);
                /*
                * calculate the goal matching
                */
                $comments_weekly_goal = get_option('audit_comments_per_week');
                $comments_daily_goal = get_option('audit_comments_per_day');      
                $comments_per_post_goal = get_option('audit_comments_per_post');                      
                if($comments_daily_goal != 0){
                    $comments_daily_performance = number_format( - (100 - (($avg_comments_daily * 100) / $comments_daily_goal)), 2)."%";
                    if($comments_daily_performance > 0 ) $comments_daily_performance = "+".$comments_daily_performance;
                    if($avg_comments_daily < $comments_daily_goal){
                        $comments_daily_color = "red";
                    }
                    else 
                    {
                        $comments_daily_color = "green";
                    }
                } else $comments_daily_performance = " -- ";
                if($comments_weekly_goal != 0){
                    $comments_weekly_performance = number_format(- (100 - (($avg_comments_weekly * 100) / $comments_weekly_goal)), 2)."%";
                    if($comments_weekly_performance > 0) $comments_weekly_performance = "+".$comments_weekly_performance; 
                    if($avg_comments_weekly < $comments_weekly_goal){
                        $comments_weekly_color = "red";
                    }
                    else 
                    {
                        $comments_weekly_color = "green";
                    }

                } else $comments_weekly_performance = " -- ";
                if($comments_per_post_goal != 0){
                    $comments_per_post_performance = number_format(- (100 - (($avg_comments_per_post * 100) / $comments_per_post_goal)), 2)."%";
                    if($comments_per_post_performance > 0) $comments_per_post_performance = "+".$comments_per_post_performance; 
                    if($avg_comments_per_post < $comments_per_post_goal){
                        $comments_per_post_color = "red";
                    }
                    else 
                    {
                        $comments_per_post_color = "green";
                    }

                } else $comments_per_post_performance = " -- ";

                if($i % 2) $bgcolor = "#fff";
                else $bgcolor = "#eef";
                echo "<tr bgcolor=$bgcolor>";
                echo "<td>".$audit_months[$comment->month]."</td><td align='center'>".$comment->comments."</td>";
                echo "<td align='center'>$avg_comments_per_post</td>";
                echo "<td align='center'>$avg_comments_daily</td>";
                echo "<td align='center'>$avg_comments_weekly</td>";                
                /*
                get the goal setting option for posting speed
                match it here and display it
                */
                echo "<td><span style='color: ".$comments_per_post_color."'>".$comments_per_post_performance."</span> / <span style='color: ".$comments_daily_color."'>".$comments_daily_performance."</span> / <span style='color: ".$comments_weekly_color."'>".$comments_weekly_performance."</span></td>";
                echo "</tr>";
                $i++; // alternate color hook
            }
            echo "</table>";
        
        echo "</td></tr>";
        echo "<tr><td valign='top'>";
        echo "<h3>Category Distribution</h3>";
//        print_r($category_distribution);
        foreach($category_distribution as $key => $cat){
            echo $cat->name.' => '.$cat->count.'<br/>';
        }
        echo "</td><td valign='top'>";
        echo "<h3>Pingback Volume</h3>";        
        echo "<table border='0' cellpadding='3' cellspacing = '2'>";
            echo "<tr bgcolor='#cee'><th>Month</th><th>Total</th><th>Per Post</th><th>Per Day</th><th>Per Week</th><th>Goal Matching</th></tr>"; 
            $i = 0;
            foreach($pingbacks as $pingback){
                /**
                * setting the number of days in each month
                */
                if($pingback->month == date('m', mktime())){
                    $no_of_days = date('d', mktime());
                }
                else if($pingback->month != date('m', mktime())){
                    if($pingback->month == 1) $no_of_days = 31;
                    if($pingback->month == 2) $no_of_days = 28; // check for bisect years
                    if($pingback->month == 3) $no_of_days = 31;
                    if($pingback->month == 4) $no_of_days = 30;
                    if($pingback->month == 5) $no_of_days = 31;
                    if($pingback->month == 6) $no_of_days = 31;
                    if($pingback->month == 7) $no_of_days = 30;
                    if($pingback->month == 8) $no_of_days = 31;
                    if($pingback->month == 9) $no_of_days = 30;
                    if($pingback->month == 10) $no_of_days = 31;
                    if($pingback->month == 11) $no_of_days = 30;
                    if($pingback->month == 12) $no_of_days = 31;
                }
                
                $no_of_weeks = number_format($no_of_days / 7);
                /*
                * getting the average pingbacks per day and week
                */
                $avg_pingbacks_daily = number_format($pingback->pingbacks / $no_of_days, 2);
                $avg_pingbacks_weekly= number_format($pingback->pingbacks / $no_of_weeks, 2);
                $avg_pingbacks_per_post = number_format($pingback->pingbacks / $table_posts[$i], 2);
                /*
                * calculate the goal matching
                */
                $pingbacks_weekly_goal = get_option('audit_pingbacks_per_week');
                $pingbacks_daily_goal = get_option('audit_pingbacks_per_day');      
                $pingbacks_per_post_goal = get_option('audit_pingbacks_per_post');                      
                if($pingbacks_daily_goal != 0){
                    $pingbacks_daily_performance = number_format( - (100 - (($avg_pingbacks_daily * 100) / $pingbacks_daily_goal)), 2)."%";
                    if($pingbacks_daily_performance > 0 ) $pingbacks_daily_performance = "+".$pingbacks_daily_performance;
                    if($avg_pingbacks_daily < $pingbacks_daily_goal){
                        $pingbacks_daily_color = "red";
                    }
                    else 
                    {
                        $pingbacks_daily_color = "green";
                    }
                } else $pingbacks_daily_performance = " -- ";
                if($pingbakcs_weekly_goal != 0){
                    $pingbakcs_weekly_performance = number_format(- (100 - (($avg_pingbacks_weekly * 100) / $pingbacks_weekly_goal)), 2)."%";
                    if($pingbacks_weekly_performance > 0) $pingbacks_weekly_performance = "+".$pingbacks_weekly_performance; 
                    if($avg_pingbacks_weekly < $pingbacks_weekly_goal){
                        $pingbacks_weekly_color = "red";
                    }
                    else 
                    {
                        $pingbacks_weekly_color = "green";
                    }

                } else $pingbacks_weekly_performance = " -- ";
                if($pingbacks_per_post_goal != 0){
                    $pingbacks_per_post_performance = number_format(- (100 - (($avg_pingbacks_per_post * 100) / $pingbacks_per_post_goal)), 2)."%";
                    if($pingbacks_per_post_performance > 0) $pingbacks_per_post_performance = "+".$pingbacks_per_post_performance; 
                    if($avg_pingbacks_per_post < $pingbacks_per_post_goal){
                        $pingbacks_per_post_color = "red";
                    }
                    else 
                    {
                        $pingbacks_per_post_color = "green";
                    }

                } else $pingbacks_per_post_performance = " -- ";

                if($i % 2) $bgcolor = "#fff";
                else $bgcolor = "#eef";
                echo "<tr bgcolor=$bgcolor>";
                echo "<td>".$audit_months[$pingback->month]."</td><td align='center'>".$pingback->pingbacks."</td>";
                echo "<td align='center'>$avg_pingbacks_per_post</td>";
                echo "<td align='center'>$avg_pingbacks_daily</td>";
                echo "<td align='center'>$avg_pingbacks_weekly</td>";                
                /*
                get the goal setting option for pingback volume
                match it here and display it
                */
                echo "<td><span style='color: ".$pingbacks_per_post_color."'>".$pingbacks_per_post_performance."</span> / <span style='color: ".$pingbacks_daily_color."'>".$pingbacks_daily_performance."</span> / <span style='color: ".$pingbacks_weekly_color."'>".$pingbacks_weekly_performance."</span></td>";
                echo "</tr>";
                $i++; // alternate color hook
            }
            echo "</table>";
               
        
        echo "</td></tr>";
        echo "</table>";     
        
        echo "</div></div>";
        
	add_action('in_admin_footer', 'blog_audit_admin_footer');
                        
}

function audit_posting_speed(){
    global $wpdb;
    $now_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
    $begin_date = date('Y-m-d', mktime(0, 0, 0, date("m")-12, 0, 0));	
    $sql_posts = "select count(*) as posts, MONTH(post_date) as month, YEAR(post_date) as year 
        from wp_posts 
        where post_status='publish' 
	and post_date between CAST('".$begin_date."' AS DATETIME) and CAST('".$now_date."' AS DATETIME)
        group by year, month
        order by post_date desc
        limit 12";
    $posts_query = $wpdb->get_results($sql_posts); 
    return($posts_query);
}

function audit_comments_density(){
    global $wpdb;
    $now_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
    $begin_date = date('Y-m-d', mktime(0, 0, 0, date("m")-12  , 0, 0));	
    $sql_comments = "select count(*) as comments, MONTH(comment_date) as month, YEAR(comment_date) as year 
        from wp_comments 
        where comment_approved=1 
        and comment_type != 'pingback'
	and comment_date between CAST('".$begin_date."' AS DATETIME) and CAST('".$now_date."' AS DATETIME)
        group by year, month
        order by comment_date desc
        limit 12";
    $comments_query = $wpdb->get_results($sql_comments); 
    return($comments_query);
}
 
function audit_pingback_volume(){
    global $wpdb;
    $now_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
    $begin_date = date('Y-m-d', mktime(0, 0, 0, date("m")-12  , 0, 0));	
    $sql_pingbacks = "select count(*) as pingbacks, MONTH(comment_date) as month, YEAR(comment_date) as year 
        from wp_comments 
        where comment_approved=1 
        and comment_type like '%pingback%'
	and comment_date between CAST('".$begin_date."' AS DATETIME) and CAST('".$now_date."' AS DATETIME)
        group by year, month
        order by comment_date desc
        limit 12";
    $pingbacks_query = $wpdb->get_results($sql_pingbacks); 
    return($pingbacks_query);
}

function audit_category_distribution(){
    /*
    * this is only a placeholder for now showing the posts categories in descending order
    * next version will include a tool for setting up goals attached to categories
    */
    $cats = get_categories('pad_counts=1&orderby=count&order=desc');
    return($cats);
}
?>
