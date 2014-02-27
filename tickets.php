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
$title = 'Help Tickets';
$page = 'tickets';
$tab = 5;
$isSummary = TRUE;
$return = 'tickets.php';


require("configuration.php");
require("include.php");
include_once("./libs/lgsl/lgsl_class.php");

$rows = query_fetch_assoc( "SELECT * FROM `".DBPREFIX."client` WHERE `clientid` = '".$_SESSION['clientid']."' LIMIT 1" );
include("./bootstrap/header.php");


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
$tickets_result = mysql_query( "SELECT * from`".DBPREFIX."tickets` WHERE `creator` = '".$_SESSION['clientid']."' order by ts_updated desc" );
?>
	<div class="well">

		<div style="text-align: center; margin-bottom: 5px;">
			<span class="label label-info"><?php echo mysql_num_rows($tickets_result);?> Ticket(s) Found</span> (<a href="ticketadd.php">Open new Ticket</a>)
		</div>	
		<table id="opentickets" class="zebra-striped">
			<thead>
				<tr>
					<th class="header">ID</th>
					<th class="header">Subject</th>
					<th class="header">Server Associated</th>
					<th class="header">Created</th>
					<th class="header">Updated</th>
					<th/>
					<th/>
					<th/>
				</tr>
			</thead>
			<tbody>
			<?php 
				while ($row = mysql_fetch_assoc($tickets_result)) {
					$srvid = $row['server']+0;
					$srvname = "No server assigned";
					if($srvid > 0){						
						$srow = query_fetch_assoc( "SELECT `name` FROM `".DBPREFIX."server` WHERE `serverid` = '".$srvid."' LIMIT 1" );
						if(!empty($srow['name'])) $srvname = $srow['name'];
					}
					echo '<tr>
						<td>'.$row['ticketid'].'</td>
						<td>'.$row['subject'].'</td>
						<td>'.$srvname.'</td>
						<td>'.$row['ts_created'].'</td>
						<td>'.$row['ts_updated'].'</td>			
						<td><span class="label '.($row['status']=='1'?'label-success">Open':'label-important">Closed').'</span></td>
						<td><div style="text-align: center;"><a class="btn btn-info btn-small" href="viewticket.php?tid='.$row['ticketid'].'"><i class="icon-search icon-white"></i></a></div></td>
						<td><div style="text-align: center;"><a class="btn btn-danger btn-small" href="ticketprocess.php?task=remove&tid='.$row['ticketid'].'" onclick="return confirm(\'Are you sure you want to delete this ticket?\')" ><i class="icon-trash icon-white"></i></a></div></td>
						</tr>';					
				}
			?>
			</tbody>
		</table>		
	</div>
<?php

include("./bootstrap/footer.php"); 
 ?>