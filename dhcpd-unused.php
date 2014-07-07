<?php

include( "intel_header.php" );

$path = "/var/www/html/";

$boardsfiles = array();
$boardsfiles[] = "data/boards.DataCenter";
$boardsfiles[] = "data/boards.ServerRack";
$boardsfiles[] = "data/boards.SV";
$boardsfiles[] = "data/boards.SW";
$boardsfiles[] = "data/boards.RackRoom";
$boardsfiles[] = "data/boards.RackRoom2";
$leases = array();


print "<h2>Cross Reference VLANs Leases with Ping Stats</h2>\n";
print "<font style='color:#cc0000;font-weight:bold'>Disclaimer: An error deviation should be considered when dealing with ping stats.<br><br></font>";

print "<h4>LMT Database assigned leases</h4><br>\n";

foreach( $boardsfiles as $boardfile )
{
	print "Reading <a href='$boardfile'>http:/siedhcp.ir.intel.com/$boardfile</a><br>\n";

	$fp = @fopen( $path . $boardfile , 'r' );	

	if( $fp )
	{
		$chunk = fread( $fp , filesize( $path . $boardfile ) );
		$lines = explode( "\n" , $chunk );
		
		foreach( $lines as $line )
		{
			$entry = explode( " " , $line );
			if( count( $entry ) < 3 ) continue;
			
			$leases[] = array( $entry[0] , $entry[1] , $entry[2] , $boardfile );
		}
		
		fclose( $fp );
	}
}



print "<br><h4>DHCP Server Ping Stats</h4><br>\n";
print "Reading dead from <a href='logs/ping-stats.txt'>http://siedhcp.ir.intel.com/logs/ping-stats.txt</a><br><br>";

$dead = "";
$fp = fopen( $path . "logs/ping-stats.txt" , 'r' );

if( $fp )
{
	$chunk = fread( $fp , filesize( $path . "logs/ping-stats.txt" ) );
	$num = preg_match_all( "/\"dead\" : \[.*?\]/sm" , $chunk , $matches );

	$matches = $matches[0];
	
	foreach( $matches as $match )
	{
		$dead .= $match;
	}	
}



print "<h4>Begin Comparison</h4><br>\n";

$g = 1;

print "<table cellpadding='2' width='800'>";
print "<tr><th>#</th><th>IXA</th><th>MAC</th><th>IP</th><th>Source</th><th>Comment</th><th>DHCP Offers 30 Days</th></tr>";

$dhcploglines = file( $path . "logs/leases-30-days-not-found.txt" );

function indhcplogs( $mac )
{
	global $dhcploglines;
	
	foreach( $dhcploglines as $line )
	{
		if( stristr( $line , $mac ) )
		{
			$bits = explode( "|" , $line );
			return $bits[1];
		}
	}
	
	return 0;
}


if( isset( $argv[1] ) )
{
	$myFile = $path . "/logs/leases-30-days-not-found.txt";
	$fp = fopen( $myFile, 'w' );
}
	
foreach( $leases as $lease )
{
	if( stristr( $dead , "\"$lease[2]\"" ) )
	{
		if( isset( $argv[1] ) )
		{
			$offers = shell_exec( "grep \"DHCPOFFER.*" . strtolower( $lease[1] ) . "\" /var/log/dhcpd.log* | wc -l" );
			fwrite( $fp, $lease[1] . "|" . $offers );
		}
		else
		{
			print "<tr><td>$g</td><td>" . $lease[0] . "</td><td>" . $lease[1] . "</td><td>" . $lease[2] . "</td><td>" . $lease[3] . "</td><td style='color:#cc0000'>Unused lease</td><td>" . indhcplogs( $lease[1] ) . "</td></tr>\n";
		}
		$g++;
	}
}

if( isset( $argv[1] ) )
{
	fclose( $fp );
}

print "</table>";




include( "intel_footer.php" );
