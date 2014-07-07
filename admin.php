<?php

include( "intel_header.php" );


$emailBody = "
	<html>
	<head>
	<style>

		body
		{
			font-size: 12pt;
			color: #544E4F;
			margin:5px;
			padding:5px;
			font-family: 'Neo Sans Intel';
		}

		body,table,td,pre
		{
			font-family: 'Neo Sans Intel';
			font-size: 12pt;
			color: #544E4F;
		}
		
		h1
		{
			font-family: 'Neo Sans Intel';
			font-size: 16pt;
			color: #0860AD;
		}
		
		h2
		{
			font-family: 'Neo Sans Intel';
			color: #0860AD;
			font-size:18pt
		}
		
		h3
		{
			font-family: 'Neo Sans Intel';
			color: #0860AD;
			font-size:14pt
		}

		a
		{
			font-size: 12pt;
			text-decoration: none;
			color: #0860AD;
		}
				
		#newstolenIPs
		{
			padding: 10px;
			margin: 10px;
		}

		#stolenIPs
		{
			padding: 10px;
			margin: 10px;
		}

		#inactiveIPs
		{
			padding: 10px;
			margin: 10px;
		}

		#usedIPs
		{
			padding: 10px;
			margin: 10px;
		}

		#staticIPs
		{
			padding: 10px;
			margin: 10px;
		}

		#dhcpIPs
		{
			padding: 10px;
			margin: 10px;
		}

		#freeIPs
		{
			padding: 10px;
			margin: 10px;
		}
			
	</style>
	</head>
	<body>
	<table cellpadding='10' width='800'>
		<tr>
			<td width='150'><img src='INTELLOGO' alt='intel-logo' width='140'/></td>
			<td width='650' style='padding-left:15px; text-align:left'><h2>Lab Management Tool</h2><span>[today]</span></td>
		</tr>
		<tr>
			<td colspan='2'>
				Hi, <br />
				<p> [message] <br />
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<strong>DHCP Server</strong> <br>
				<a href='http://siedhcp.ir.intel.com' target='_blank'>http://siedhcp.ir.intel.com</a>
				<br><br>
				<strong>LMT Portal</strong> <br>
				<a href='http://lmt.ir.intel.com/' target='_blank'>http://lmt.ir.intel.com/</a>
				<br><br>
				Regards,<br>
				The LMT Team
			</td>
		</tr>
	</table>
</body>
</html>
";

if( $ldap->isloggedin() == true  && $ldap->getlogintype() == "admin" )
{
	if( isset( $_GET[ 'opt' ] ) )
	{
		if( $_GET[ 'opt' ] == "reboot" )
		{						
			print "<h2>Atttempting to reboot dhcp service</h2>";
			$result = shell_exec( "sudo /etc/init.d/dhcpd restart" );
			print "<br><br>Result : $result</br></br>";			
				
			$message = "The user <b>" . $ldap->getuser() . "</b> has restarted the DHCP service through the web interface." ;
			$emailBody = str_replace( "[message]" , $message , $emailBody );
			$emailBody = str_replace( "[today]" , trim( shell_exec( "date 2>&1" ) ) , $emailBody );
			$sendit = new Mailer( "[ LMT ] " . $ldap->getuser() . " has restarted the DHCP service" , $emailBody );
			$sendit->addRecipient( "\"SHN Lab Support and Admins\" <shn.lab.support.and.admins@intel.com>" );
			$sendit->embedPicture( "INTELLOGO" , "images/intel-small.jpg" );
			$sendit->mail();
		}
		else if( $_GET[ 'opt' ] == "rebuild" )
		{					
			print "<h2>Atttempting to rebuild the DHCP config</h2>";
			$cmd = "sudo /opt/rational/clearquest/bin/cqperl /scripts/dhcp.pl 2>&1 > /scripts/log/LMTdhcpLog.txt";
			
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$start = $time;
			$result = shell_exec( $cmd );
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$finish = $time;
			$total_time = round(($finish - $start), 4);
			print "<br><br>Rebuilt in $total_time seconds  </br></br>";		

			$message = "The user <b>" . $ldap->getuser() . "</b> has rebuilt the DHCP configuration through the web interface." ;
			$emailBody = str_replace( "[message]" , $message , $emailBody );
			$emailBody = str_replace( "[today]" , trim( shell_exec( "date 2>&1" ) ) , $emailBody );
			$sendit = new Mailer( "[ LMT ] " . $ldap->getuser() . " has rebuilt the DHCP configuration" , $emailBody );
			$sendit->addRecipient( "\"SHN Lab Support and Admins\" <shn.lab.support.and.admins@intel.com>" );
			$sendit->embedPicture( "INTELLOGO" , "images/intel-small.jpg" );
			$sendit->mail();			
		}
	}
}
else
{
	print "Please <a href='login.php'>login</a>";
}

include( "intel_footer.php" );

?>
