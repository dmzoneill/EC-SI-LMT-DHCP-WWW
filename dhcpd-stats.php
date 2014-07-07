<?php

$year = date("Y"); 

$minus1  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
$minus2  = mktime(0, 0, 0, date("m")  , date("d"), date("Y")) - 86400;
$minus3  = mktime(0, 0, 0, date("m")  , date("d"), date("Y")) - 172800;
$minus4  = mktime(0, 0, 0, date("m")  , date("d"), date("Y")) - 259200;
$minus5  = mktime(0, 0, 0, date("m")  , date("d"), date("Y")) - 345600;
$minus6  = mktime(0, 0, 0, date("m")  , date("d"), date("Y")) - 432000;
$minus7  = mktime(0, 0, 0, date("m")  , date("d"), date("Y")) - 518400;

$minus1 = date( "Y" , $minus1 ) . date( "m" , $minus1 ) . date( "d" , $minus1 );
$minus2 = date( "Y" , $minus2 ) . date( "m" , $minus2 ) . date( "d" , $minus2 );
$minus3 = date( "Y" , $minus3 ) . date( "m" , $minus3 ) . date( "d" , $minus3 );
$minus4 = date( "Y" , $minus4 ) . date( "m" , $minus4 ) . date( "d" , $minus4 );
$minus5 = date( "Y" , $minus5 ) . date( "m" , $minus5 ) . date( "d" , $minus5 );
$minus6 = date( "Y" , $minus6 ) . date( "m" , $minus6 ) . date( "d" , $minus6 );
$minus7 = date( "Y" , $minus7 ) . date( "m" , $minus7 ) . date( "d" , $minus7 );

// /var/log/dhcpd.log
$days = array();
$days[ 0 ] = "/var/log/dhcpd.log";
$days[ 1 ] = "/var/log/dhcpd.log-" . $minus1;
$days[ 2 ] = "/var/log/dhcpd.log-" . $minus2;
$days[ 3 ] = "/var/log/dhcpd.log-" . $minus3;
$days[ 4 ] = "/var/log/dhcpd.log-" . $minus4;
$days[ 5 ] = "/var/log/dhcpd.log-" . $minus5;
$days[ 6 ] = "/var/log/dhcpd.log-" . $minus6;
$days[ 7 ] = "/var/log/dhcpd.log-" . $minus7;

$json = "{\n";
$json .= "	\"time\" : \"" . date('l jS \of F Y h:i A') . "\",\n";
$json .= "	\"logs\" : [ \n";

for( $i = 0; $i < count( $days ); $i++ )
{
	$daydata = trim( shell_exec( "tail -n 1 " . $days[ $i ] ) );
	$daydata = explode( " " , $daydata );

	if( count( $daydata ) > 4 )
	{
		$month = $daydata[ 0 ];
		if( $daydata[ 1 ] == "" )
		{
			$day = " " . $daydata[ 2 ];			
		}
		else
		{
			$day = $daydata[ 1 ];
		}
	}
	else
	{
		$month = "IGNORE";
		$day = "IGNORE";
	}
	
	$json .= "		{ \n";
	$dhcpoffer = trim( shell_exec( "cat " . $days[ $i ] . " | grep DHCPOFFER | wc -l" ) );
	$dhcpdiscover = trim( shell_exec( "cat " . $days[ $i ] . " | grep DHCPDISCOVER | wc -l" ) );
	$dhcprequest = trim( shell_exec( "cat " . $days[ $i ] . " | grep DHCPREQUEST | wc -l" ) );
	$dhcpack = trim( shell_exec( "cat " . $days[ $i ] . " | grep DHCPACK | wc -l" ) );
	$bootrequest = trim( shell_exec( "cat " . $days[ $i ] . " | grep BOOTREQUEST | wc -l" ) );
	$bootreply = trim( shell_exec( "cat " . $days[ $i ] . " | grep BOOTREPLY | wc -l" ) );
	$dhcprestarts = trim( shell_exec( "cat " . $days[ $i ] . " | grep \"Listening on\" | wc -l" ) );

	$json .= "			\"DHCPDISCOVER\" : $dhcpdiscover, \n";
	$json .= "			\"DHCPOFFER\" : $dhcpoffer, \n";
	$json .= "			\"DHCPREQUEST\" : $dhcprequest, \n";
	$json .= "			\"DHCPACK\" : $dhcpack, \n";
	$json .= "			\"BOOTREQUEST\" : $bootrequest, \n";
	$json .= "			\"BOOTREPLY\" : $bootreply, \n";
	$json .= "			\"SERVICE RESTARTS\" : $dhcprestarts, \n";
	
	$t = 0;	

	$hourdata = array();
		
	while( $t < 24 )
	{
		$dd = ( $t < 10 ) ? "0" . $t : $t;			
		$dhcpoffer = trim( shell_exec( "cat " . $days[ $i ] . " | grep DHCPOFFER | grep \"$month $day $dd:\" | wc -l" ) );
		$dhcpdiscover = trim( shell_exec( "cat " . $days[ $i ] . " | grep DHCPDISCOVER | grep \"$month $day $dd:\" | wc -l" ) );
		$dhcprequest = trim( shell_exec( "cat " . $days[ $i ] . " | grep DHCPREQUEST | grep \"$month $day $dd:\" | wc -l" ) );
		$dhcpack = trim( shell_exec( "cat " . $days[ $i ] . " | grep DHCPACK | grep \"$month $day $dd:\" | wc -l" ) );
		$dhcprestarts = trim( shell_exec( "cat " . $days[ $i ] . " | grep \"Listening on\" | grep \"$month $day $dd:\" | wc -l" ) );
		$bootrequest = trim( shell_exec( "cat " . $days[ $i ] . " | grep BOOTREQUEST | grep \"$month $day $dd:\" | wc -l" ) );
		$bootreply = trim( shell_exec( "cat " . $days[ $i ] . " | grep BOOTREPLY | grep \"$month $day $dd:\" | wc -l" ) );
		print "cat " . $days[ $i ] . " | grep DHCPOFFER | grep \"$month $day $dd:\" | wc -l\n";
		$hourdata[] = array( $dhcpoffer , $dhcpdiscover , $dhcprequest , $dhcpack , $bootrequest , $bootreply , $dhcprestarts );
		$t++;
	}

	$json .= "			\"HOURS\" : [ \n";

	for( $j = 0; $j < count( $hourdata ); $j++ )
	{
		$json .= "				{ \n";
		$json .= "					\"DHCPOFFER\" : " . $hourdata[$j][0] .", \n";
		$json .= "					\"DHCPDISCOVER\" : " . $hourdata[$j][1] .", \n";
		$json .= "					\"DHCPREQUEST\" : " . $hourdata[$j][2] .", \n";
		$json .= "					\"DHCPACK\" : " . $hourdata[$j][3] .", \n";
		$json .= "					\"RESTARTS\" : " . $hourdata[$j][6] .", \n";
		$json .= "					\"BOOTREQUEST\" : " . $hourdata[$j][4] .", \n";
		$json .= "					\"BOOTREPLY\" : " . $hourdata[$j][5] ." \n";
		
		if( $j == count( $hourdata ) - 1 )
		{
			$json .= "				} \n";
		}
		else
		{
			$json .= "				}, \n";
		}
	}
	
	$json .= "			] \n";
	
	if( $i == count( $days ) - 1 )
	{
		$json .= "		} \n";
	}
	else
	{
		$json .= "		}, \n";
	}
}
$json .= "	] \n";
$json .= "}\n";

$myFile = "/var/www/html/logs/dhcpd-stats.txt";
$fh = fopen( $myFile , 'w' ) or die( "can't open file" );
fwrite( $fh , $json );
fclose($fh);

?>
