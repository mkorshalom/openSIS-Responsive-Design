<?php

#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************
include('../../RedirectModulesInc.php');
if (!$_REQUEST['modfunc']) {

    $start_date = date('Y-m') . '-01';
    $end_date = DBDate('mysql');
    echo '<div class="row">';
    echo '<div class="col-md-8 col-md-offset-2">';
    echo "<FORM class=\"form-horizontal\" name=log id=log action=Modules.php?modname=$_REQUEST[modname]&modfunc=generate method=POST>";
    PopTable('header', 'Log Details');

    echo '<h5 class="text-center">Please Select Date Range</h5>';

    echo '<div class="row">';
    echo '<div class="col-lg-6 col-lg-offset-3">';

    echo '<div class="form-group">';
    echo '<label class="col-md-2 control-label text-right">From</label><div class="col-md-10">';
    echo DateInputAY($start_date, 'start', 1);
    echo '</div>'; //.col-md-10
    echo '</div>'; //.form-group

    echo '</div>'; //.col-lg-6
    echo '</div>'; //.row
    echo '<div class="row">';
    echo '<div class="col-lg-6 col-lg-offset-3">';

    echo '<div class="form-group">';
    echo '<label class="col-md-2 control-label text-right">To </label><div class="col-md-10">';
    echo DateInputAY($end_date, 'end', 2);
    echo '</div>'; //.col-md-10
    echo '</div>'; //.form-group

    echo '</div>'; //.col-lg-6
    echo '</div>'; //.row

    $btn = '<div class="text-center"><input type="submit" class="btn btn-primary" value="Generate" name="generate"></div>';
    PopTable('footer', $btn);
    echo '</FORM>';
    echo '</div>';
    echo '</div>'; //.row
}


if ($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start']) {
//    $start_date = $_REQUEST['day_start'] . '-' . $_REQUEST['month_start'] . '-' . substr($_REQUEST['year_start'], 2, 4);
//    $org_start_date = $_REQUEST['day_start'] . '-' . $_REQUEST['month_start'] . '-' . $_REQUEST['year_start'];

//    $conv_st_date = con_date($org_start_date);
    $conv_st_date=$_REQUEST['year_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['day_start'];
}

if ($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end']) {
//    $end_date = $_REQUEST['day_end'] . '-' . $_REQUEST['month_end'] . '-' . substr($_REQUEST['year_end'], 2, 4);
//    $org_end_date = $_REQUEST['day_end'] . '-' . $_REQUEST['month_end'] . '-' . $_REQUEST['year_end'];
//
//    $conv_end_date = con_date_end($org_end_date);
//    
    
    $conv_end_date=$_REQUEST['year_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['day_end'];
}
if($_REQUEST['modfunc']=='del')
{
    print_r($_REQUEST);
    
     if (DeletePromptMod('Acess log', $qs)) {
        if(count($_REQUEST['log_arr'])>0)
        {
        // $del_id=implode(',',$_REQUEST['log_arr']);
            //print_r($_REQUEST['log_arr']);
     $del_id=  implode(',', $_REQUEST['log_arr']);
//         echo "DELETE FROM login_records WHERE id in('$del_id')";exit;
     echo "DELETE FROM login_records WHERE id in($del_id)";
        DBQuery("DELETE FROM login_records WHERE id in($del_id)");
            
        }
        unset($_REQUEST['modfunc']);
        }
}
if ($_REQUEST['modfunc'] == 'generate') {

 echo "<FORM action=Modules.php?modname=" . strip_tags(trim($_REQUEST[modname])) . "&modfunc=del method=POST >";
    if (isset($conv_st_date) && isset($conv_end_date)) {
        //echo 'SELECT DISTINCT FIRST_NAME,CONCAT(\'<INPUT type=checkbox name=log_arr[]  checked >\') AS CHECKBOX,USER_NAME,LAST_NAME,LOGIN_TIME,PROFILE,STAFF_ID,FAILLOG_COUNT,FAILLOG_TIME,USER_NAME,IF(IP_ADDRESS LIKE \'::1\',\'127.0.0.1\',IP_ADDRESS) as IP_ADDRESS,STATUS FROM login_records WHERE LOGIN_TIME >=\'' . $conv_st_date . '\' AND LOGIN_TIME <=\'' . $conv_end_date . '\' AND SCHOOL_ID=' . UserSchool() . ' ORDER BY LOGIN_TIME DESC';
       
        $alllogs_RET = DBGet(DBQuery('SELECT  FIRST_NAME,CONCAT(\'<INPUT type=checkbox name=log_arr[] value=\',ID,\' checked >\') AS CHECKBOX,USER_NAME,LAST_NAME,LOGIN_TIME,PROFILE,STAFF_ID,FAILLOG_COUNT,FAILLOG_TIME,USER_NAME,IF(IP_ADDRESS LIKE \'::1\',\'127.0.0.1\',IP_ADDRESS) as IP_ADDRESS,STATUS FROM login_records WHERE LOGIN_TIME >=\'' . $conv_st_date . '\' AND LOGIN_TIME <=\'' . $conv_end_date . '\' AND SCHOOL_ID=' . UserSchool() . ' ORDER BY LOGIN_TIME DESC'));

        foreach($alllogs_RET as $k => $v)
        {

        if($v['PROFILE']!='Student' && $v['PROFILE']!='parent')
        {

        $profile=  DBGet(DBQuery('SELECT PROFILE_ID FROM staff WHERE STAFF_ID='.$v['STAFF_ID'].''));
        if($profile[1]['PROFILE_ID']==0)
            {

             $alllogs_RET[$k]['PROFILE']='Super Administrator';   
            }
        }

        }
        if (count($alllogs_RET)) {
            echo '<div class="panel panel-default">';
              //$extra['columns_before'] = array('CHECKBOX' => '</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
            ListOutput($alllogs_RET, array('CHECKBOX' => '</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'log_arr\');"><A>','LOGIN_TIME' => 'Login Time', 'USER_NAME' => 'User Name', 'FIRST_NAME' => 'First Name', 'LAST_NAME' => 'Last Name', 'PROFILE' => 'Profile', 'FAILLOG_COUNT' => 'Failure Count', 'STATUS' => 'Status', 'IP_ADDRESS' => 'IP Address'), 'login record', 'login records', array(), array(), array('count' => true, 'save' => true));
            if(count($alllogs_RET)>0) 
            echo '<div class="panel-footer text-center"><INPUT type=submit value="Delete Log" class="btn btn-primary"></div>';
            echo '</div>';
            echo "</FORM>";
        } else {

            echo '<table border=0 width=90%><tr><td class="alert"></td><td class="alert_msg"><b>No login records were found.</b></td></tr></table>';
        }
    }
    if ((!isset($conv_st_date) || !isset($conv_end_date))) {
        echo '<center><font color="red"><b>You have to select date from the date range</b></font></center>';
    }
}

function con_date($date) {
    $mother_date = $date;
    $year = substr($mother_date, 7);
    $temp_month = substr($mother_date, 3, 3);

    if ($temp_month == 'JAN')
        $month = '01';
    elseif ($temp_month == 'FEB')
        $month = '02';
    elseif ($temp_month == 'MAR')
        $month = '03';
    elseif ($temp_month == 'APR')
        $month = '04';
    elseif ($temp_month == 'MAY')
        $month = '05';
    elseif ($temp_month == 'JUN')
        $month = '06';
    elseif ($temp_month == 'JUL')
        $month = '07';
    elseif ($temp_month == 'AUG')
        $month = '08';
    elseif ($temp_month == 'SEP')
        $month = '09';
    elseif ($temp_month == 'OCT')
        $month = '10';
    elseif ($temp_month == 'NOV')
        $month = '11';
    elseif ($temp_month == 'DEC')
        $month = '12';

    $day = substr($mother_date, 0, 2);

    $select_date = $year . '-' . $month . '-' . $day . ' ' . '00:00:00';
    return $select_date;
}

function con_date_end($date) {
    $mother_date = $date;
    $year = substr($mother_date, 7);
    $temp_month = substr($mother_date, 3, 3);

    if ($temp_month == 'JAN')
        $month = '01';
    elseif ($temp_month == 'FEB')
        $month = '02';
    elseif ($temp_month == 'MAR')
        $month = '03';
    elseif ($temp_month == 'APR')
        $month = '04';
    elseif ($temp_month == 'MAY')
        $month = '05';
    elseif ($temp_month == 'JUN')
        $month = '06';
    elseif ($temp_month == 'JUL')
        $month = '07';
    elseif ($temp_month == 'AUG')
        $month = '08';
    elseif ($temp_month == 'SEP')
        $month = '09';
    elseif ($temp_month == 'OCT')
        $month = '10';
    elseif ($temp_month == 'NOV')
        $month = '11';
    elseif ($temp_month == 'DEC')
        $month = '12';

    $day = substr($mother_date, 0, 2);

    $select_date = $year . '-' . $month . '-' . $day . ' ' . '23:59:59';
    return $select_date;
}

?>