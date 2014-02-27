<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * LICENSE:
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @categories	Games/Entertainment, Systems Administration
 * @package		Bright Game Panel
 * @author		warhawk3407 <warhawk3407@gmail.com> @NOSPAM
 * @copyleft	2013
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @version		(Release 0) DEVELOPER BETA 5
 * @link		http://www.bgpanel.net/
 */
$return = TRUE;
require("../configuration.php");
require("./include.php");
if (isset($_POST['task']))
{
	$task = mysql_real_escape_string($_POST['task']);
}
else if (isset($_GET['task']))
{
	$task = mysql_real_escape_string($_GET['task']);
}

switch (@$task)
{	
	case 'close':
		$tid = intval(mysql_real_escape_string($_GET['tid']));
		if($tid > 0) {
			$ticketqry = mysql_query( "SELECT * from `".DBPREFIX."tickets` WHERE `ticketid` = '".$tid."'");
			$ticketdata = mysql_fetch_assoc($ticketqry);
			if(!$ticketdata){
				$error .= 'Ticket not Found';	
			}
		}else{	
			$error .= 'Invalid ticket ID';	
		}		
		if(!empty($error)){
			$_SESSION['msg1'] = 'Couldn\'t delete message!';
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( 'Location: viewticket.php?tid='.$tid );
			die();
		}
		$result = mysql_query("UPDATE `".DBPREFIX."tickets` SET `status`='0' WHERE `ticketid`='".$tid."';");
		if ($result){
			$_SESSION['msg1'] = 'Ticket Closed Successfully!';
			$_SESSION['msg2'] = 'The selected ticket was closed.';
			$_SESSION['msg-type'] = 'success';
			unset($error);
			header( 'Location: tickets.php' );
			die();		
		}else{
			$_SESSION['msg1'] = 'Error deleting ticket!';
			$_SESSION['msg2'] = 'The selected ticket was not removed due a database failure';
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( 'Location: tickets.php' );
			die();
		}
	break;
	case 'remove':
		$tid = intval(mysql_real_escape_string($_GET['tid']));
		if($tid > 0) {
			$ticketqry = mysql_query( "SELECT * from `".DBPREFIX."tickets` WHERE `ticketid` = '".$tid."'");
			$ticketdata = mysql_fetch_assoc($ticketqry);
			if(!$ticketdata){
				$error .= 'Ticket not Found';	
			}
		}else{	
			$error .= 'Invalid ticket ID';	
		}		
		if(!empty($error)){
			$_SESSION['msg1'] = 'Couldn\'t delete message!';
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( 'Location: viewticket.php?tid='.$tid );
			die();
		}
		$result = mysql_query("DELETE FROM `".DBPREFIX."tickets` WHERE `ticketid`='".$tid."';");
		if ($result){
			$_SESSION['msg1'] = 'Ticket deleted Successfully!';
			$_SESSION['msg2'] = 'The selected ticket was removed.';
			$_SESSION['msg-type'] = 'success';
			unset($error);
			header( 'Location: tickets.php' );
			die();		
		}else{
			$_SESSION['msg1'] = 'Error deleting ticket!';
			$_SESSION['msg2'] = 'The selected ticket was not removed due a database failure';
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( 'Location: tickets.php' );
			die();
		}
	break;
	case 'ticketreply':
		$error = '';
		$msg = mysql_real_escape_string($_POST['message']);
		$tid = intval(mysql_real_escape_string($_POST['tid']));
		if(empty($msg)) $error .= 'Message was empty. ';
		if($tid > 0) {
			$ticketqry = mysql_query( "SELECT * from`".DBPREFIX."tickets` WHERE `ticketid` = '".$tid."' and `status`=1" );
			$ticketdata = mysql_fetch_assoc($ticketqry);
			if(!$ticketdata){
				$error .= 'Ticket not Found';	
			}
		}else{	
			$error .= 'Invalid ticket ID';	
		}		
		if(!empty($error)){
			$_SESSION['msg1'] = 'Couldn\'t add message!';
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( 'Location: viewticket.php?tid='.$tid );
			die();
		}
		query_basic( "INSERT INTO `".DBPREFIX."ticketmsgs` SET
				`ticket` = '".$tid."',
				`message` = '".$msg."',
				`time` = now(),
				`admin` = '".$_SESSION['adminid']."'");				
		$mid = mysql_insert_id();
		if ($mid && $mid > 0){
			query_basic("UPDATE `".DBPREFIX."tickets` SET `ts_updated`=now() WHERE `ticketid`='".$tid."'");
			$_SESSION['msg1'] = 'Message Added Successfully!';
			$_SESSION['msg2'] = 'Your message was saved.';
			$_SESSION['msg-type'] = 'success';
			unset($error);
			header( 'Location: viewticket.php?tid='.$tid );
			die();		
		}else{
			$_SESSION['msg1'] = 'Couldn\'t add message!';
			$_SESSION['msg2'] = 'Error inserting into database';
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( 'Location: viewticket.php?tid='.$tid );
			die();
		}
	break;
}

?>