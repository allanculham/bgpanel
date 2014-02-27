<?php
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
$title = 'View Ticket';
$page = 'viewticket';
$tab = 5;
$isSummary = TRUE;
$return = 'viewticket.php';

require("configuration.php");
require("include.php");
include_once("./libs/lgsl/lgsl_class.php");

$rows = query_fetch_assoc( "SELECT * FROM `".DBPREFIX."client` WHERE `clientid` = '".$_SESSION['clientid']."' LIMIT 1" );
include("./bootstrap/header.php");

$error = '';

if(isset($_GET['tid']) AND $_GET['tid'] > 0)
{

	$tid = intval(mysql_real_escape_string($_GET['tid']));
	$clientid = $_SESSION['clientid'];
	
	$ticketQrySQL = sprintf("SELECT creator, firstname, lastname, subject, ts_created, ts_updated, tick.status, IFNULL(server,0) server, serv.name srvname 
								FROM %stickets tick
								LEFT JOIN %sserver serv ON serv.serverid = tick.server
								JOIN %sclient cl ON clientid = tick.creator
								WHERE ticketid = %d 
								AND creator = %d LIMIT 1", DBPREFIX, DBPREFIX, DBPREFIX, $tid, $clientid);
								
	$ticketqry = mysql_query($ticketQrySQL);	
	$ticketdata = mysql_fetch_assoc($ticketqry);
		
}else{

	$error .= 'Ticket ID not specified';
}


if(!empty($error)){
	$_SESSION['msg1'] = 'Couldn\'t open ticket!';
	$_SESSION['msg2'] = $error;
	$_SESSION['msg-type'] = 'error';
	unset($error);
	header( 'Location: tickets.php' );
	die();
}

$clientdata = query_fetch_assoc( "SELECT firstname,lastname FROM `".DBPREFIX."client` WHERE `clientid` = '".$_SESSION['clientid']."' LIMIT 1" );

/**
 * Notifications
 */
if (isset($_SESSION['msg1']) && isset($_SESSION['msg2']) && isset($_SESSION['msg-type']))
{
?>				
			<div class="alert alert-<?php
	switch ($_SESSION['msg-type'])
	{
		case 'block':
			echo 'block';
			break;

		case 'error':
			echo 'error';
			break;

		case 'success':
			echo 'success';
			break;

		case 'info':
			echo 'info';
			break;
	}
?>">
				<a class="close" data-dismiss="alert">&times;</a>
				<h4 class="alert-heading"><?php echo $_SESSION['msg1']; ?></h4>
				<?php echo $_SESSION['msg2']; ?>
			</div>
<?php
	unset($_SESSION['msg1']);
	unset($_SESSION['msg2']);
	unset($_SESSION['msg-type']);
}
/**
 *
 */
?>

<div class="well">
	<div style="text-align: center; margin-bottom: 5px;">
		<span class="label label-info">Ticket Information</span>
	</div>
	<table class="table table-striped table-bordered table-condensed">
		<tbody>
		<tr>
			<td>Subject</td>
			<td><?php echo $ticketdata['subject']; ?></td>
		</tr>
		<tr>
			<td>Created on</td>
			<td><?php echo $ticketdata['ts_created']; ?></td>
		</tr>
		<tr>
			<td>Last Updated</td>
			<td><?php echo $ticketdata['ts_updated']; ?></td>
		</tr>
		<tr>
			<td>Status</td>
			<td><?php echo '<span class="label '.($ticketdata['status']=='1'?'label-success">Open':'label-important">Closed').'</span>' ?></td>
		</tr>
		<tr>
			<td>Server</td>
			<td><?php echo $ticketdata['srvname']; ?></td>
		</tr>
	</tbody></table>
	<div style="text-align:right">
		<a href="ticketprocess.php?task=remove&tid=<?php echo $tid;?>" class="btn btn-danger pull-midle" onclick="return confirm('Are you sure you want to delete this ticket?')">Delete Ticket</a>
	</div>
</div>
<?php
	$ticketMsgSQL = sprintf("SELECT firstname,lastname, msg.time, msg.message, IFNULL(msg.admin, 0) admin
							FROM %sticketmsgs msg
							LEFT JOIN %sadmin adm ON adm.adminid = msg.admin
							WHERE ticket = %d
							ORDER BY time ASC", DBPREFIX, DBPREFIX, $tid);

	$ticketMsgResult = mysql_query($ticketMsgSQL);
	
	while ($message = mysql_fetch_assoc($ticketMsgResult)) 
	{
		if($message['admin'] >0)
		{ 
			$type = 'Admin';
			$class = 'label-success';
		
		}else{ 

			$type = 'Client';
			$class = 'label-info'; 	
		}	
		
		echo '<div class="well">';
		echo '<span class="label '.$class.'">'.$type.'</span> '.htmlspecialchars($ticketdata['firstname'], ENT_QUOTES).' '.htmlspecialchars($ticketdata['lastname'], ENT_QUOTES).' @ '.$message['time'].':<br/><br/>';
		echo nl2br(htmlspecialchars($message['message'], ENT_QUOTES));
		echo '</div>';	
	}
	

if($ticketdata['status']==1){
?>
<form method="post" action="ticketprocess.php">
	<input type="hidden" name="task" value="ticketreply" />
	<input type="hidden" name="tid" value="<?php echo $tid?>" />
	<div class="well">
		<label>Send a message</label>
		<textarea style="width: 100%; border: 1px solid #333; padding: 4px;" name="message" class="textarea span5"></textarea>
		<button type="submit" class="btn btn-primary">Reply</button>
	</div>
</form>
<?php
}
?>
<div style="text-align: center;">
	<ul class="pager">
		<li>
			<a href="tickets.php">Back to Tickets</a>
		</li>
	</ul>
</div>
<?php
include("./bootstrap/footer.php"); 
?>