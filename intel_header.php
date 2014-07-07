<?php

date_default_timezone_set( 'Europe/Dublin' );
header( "Pragma: nocache" );
header( "cache-Control: no-cache; must-revalidate" );
header( "Expires: Mon, 26 Jul 1993 00:00:00 GMT" );

include( "functions.php" );
include( "leases.class.php" );
include( "mailer.class.php" );
include( "ldap.class.php" );

$ldap = new Ldap();

if( isset( $_GET[ 'logout' ] ) )
{
	$ldap->logout();
}

if( isset( $_POST[ 'username' ] ) && isset( $_POST[ 'password' ] ) )
{	
	$ldap->login( $_POST[ 'username' ] , $_POST[ 'password' ] );
}

$status = shell_exec( '/etc/init.d/dhcpd status 2>&1' );
$syntax = shell_exec( '/etc/init.d/dhcpd configtest 2>&1' );

if( stristr( $syntax , "OK" ) )
{
	$syntax  = "<span style='color:#000077'>$syntax</span>";
}
else
{
	$syntax  = "<span style='color:#990000'>$syntax</span>";
}

if( stristr( $status , "pid" ) )
{
	$status  = "<span style='color:#007700'>$status</span>";
}
else
{
	$status  = "<span style='color:#990000'>$status</span>";
}

if( !ob_start( "ob_gzhandler" ) ) ob_start();

?>

<html>
<head>
<link rel='stylesheet' href='css/intel.css' type='text/css' media='screen'>
<link rel="stylesheet" href="css/smoothness/jquery-ui-1.8.21.custom.css" type="text/css" media="all" />
<link href="css/styles/shCore.css" rel="stylesheet" type="text/css" /> 
<link href="css/styles/shThemeDefault.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script> 
<script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script> 
<script type="text/javascript" src="js/intel.js.php"></script> 
<script type="text/javascript" src="js/highcharts.src.js"></script> 
 
<!--[if IE]>
	<script type="text/javascript" src="js/excanvas.compiled.js"></script>
<![endif]--> 

<script type="text/javascript">
			
	$(document).ready(function()
	{		
		setInterval("displaytime()", 1000);
	});
	
</script>
</head>
<body>
<?php include( "/nfs/sie/local/EC/IT/common/commonheader.php" ); ?>
<table width='1200'>
	 <tr>
                <td width='1200' colspan='22'>
                        <table>
                                <tr>
                                <td width='242' height='150'>
                                    <div id='intelloadingCon' style='width:162px;height:150px;margin-left:70px;margin-right:0px'>
                                        <a href='index.php'>
                                            <img id='intelloading' src='images/intel-small.jpg' width='100%' height='100%' border='0'/>
                                        </a>
                                    </div>
                                </td>
                                <td width='558'>
					<font style='padding-left:40px;padding-right:40px;font-size:18pt'>Shannon Labs DHCP Server Status</font><br/><br/>
			                <span id="servertime" style='padding-left:60px;'></span> &nbsp;&nbsp;<?php print $status . " " . $syntax; ?>
                                <td>
				<td width='250'>
					<?php print "<form action='checker.php' method='post'>Search IP / MAC / IXA : <input type='text' value='";
			                print isset( $_POST['lease'] ) ? $_POST['lease'] : "";
			                print "' name='lease'> <input type='submit' value='search'></form>"; ?>
				</td>
			        <td width='150'><h3>Free Ips : </h3>

			                <table width='60'>
			                <?php

                        		$ips = readFreeIpsLog();

		                        print "";
                		        foreach( $ips as $vlan => $free )
		                        {
                		                print "<tr><td>$vlan</td><td align='right'><b>$free</b></td></tr>";
		                        }
                			?>
			                </table>

				</td>
                                </tr>
                        </table>
                </td>
        </tr>
	<tr>
		<td width='200' style='text-align:right;vertical-align:top;padding-right:20px'>
			<h2 class='hr' style='border-bottom:1px dashed #99f;margin-left:10px'>Home</h2>
				<a href='index.php'>Homepage</a><br/>
			<?php
				if( $ldap->isloggedin() )
				{
					print "<a href='login.php?logout=true' style='color:#ff0000'>logout</a><br/>";			
				}
				else
				{
					print "<a href='login.php'>Login</a><br/>";	
				}
			?>		
			<br>
			<h2 class='hr' style='border-bottom:1px dashed #99f;margin-left:10px'>LMT</h2>
				<a href='log.php?opt=lmtlog'>LMT DHCP sync log</a><br/>
				<a href='inventory.php'>Inventory</a><br/>
				<a href='config.php?opt=leases'>IP Assignments</a><br/>
				<a href='config.php?opt=dhcp'>DHCP Config</a><br/>
				<a href='pxe.php'>My Crbs / Assets</a><br/>
			<br>
			<h2 class='hr' style='border-bottom:1px dashed #99f;margin-left:10px'>Network</h2>				
				<a href='dhcpd-dynamic.php'>Dynamic Nodes</a><br/>
				<a href='dhcpd-static.php'>Static Nodes</a><br/>
				<a href='servicepw.php'>PC Audit</a><br/>				
				<a href='dhcpd-unused.php'>Unused Leases</a><br/>
			<br>
			<h2 class='hr' style='border-bottom:1px dashed #99f;margin-left:10px'>Support</h2>	
				<a href='contacts.php'>Contacts</a><br/>		
				<a href='live-log.php'>Live log</a><br/>				
			<br>
			<h2 class='hr' style='border-bottom:1px dashed #99f;margin-left:10px'>Administrate</h2>	
				<a href='admin.php?opt=reboot'>Restart DHCPD</a><br/>	
				<a href='admin.php?opt=rebuild'>Rebuild DHCPD Config</a><br/>
		</td>
		<td style='padding:20px;vertical-align:top;border:1px dashed #99f;' width='1000'>
		
