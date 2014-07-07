<?php

include( "intel_header.php" );


$lines = file( "/etc/dhcpd.conf" );

for( $t=2 ; $t < count( $lines ) -3; $t++ )
{
	if( stristr( $lines[ $t ] , "hardware ethernet" ) && stristr( $lines[ $t + 1 ] , "fixed-address" ) )
	{
		$ixa = explode( "-" , $lines[ $t -2 ] );
		$ixa = $ixa[ 1 ];
		$mac = substr( trim( preg_replace( '/hardware ethernet/' , "" , $lines[ $t ] ) ) , 0 , -1 );
		$ip = substr( trim( preg_replace( '/fixed-address/' , "" , $lines[ $t + 1 ] ) ) , 0 , -1 );
		
		if( validIP( $ip ) && validMAC( $mac ) )
		{
			$valid_hosts[] = array( $mac , $ip , $ixa );
		}
	}
}

$test = new Leases; 
$test->readLease("/var/lib/dhcpd/dhcpd.leases"); 

print "<table>";

if( $_POST['lease'] )
{
	$duplicates = array();
	
	$found = false;
	while($lease = $test->nextLease()) 
	{ 
		$lease['ip_addr'] = isset( $lease['ip_addr'] ) ? $lease['ip_addr'] : "";
		$lease['mac'] = isset( $lease['mac'] ) ? $lease['mac'] : "";
		if( stristr( $lease['ip_addr'] , $_POST['lease'] ) || stristr( $lease['mac'] , $_POST['lease'] ) )
		{
			if( !in_array( $lease['ip_addr'] , $duplicates ) )
			{
				$duplicates[] = $lease['ip_addr'];
				print "<tr><td></td><td class='padme'>" . $lease['mac'] . "</td><td class='padme'>" . $lease['ip_addr'] . "</td></tr>";
				$found = true;
			}
		}
	}

	for( $t=0;$t<count($valid_hosts);$t++)
	{
		if( stristr( $valid_hosts[$t][0] , $_POST['lease'] ) || stristr( $valid_hosts[$t][1] , $_POST['lease'] ) || stristr( $valid_hosts[$t][2] , $_POST['lease'] ) )
		{
			print "<tr><td class='padme'>" . $valid_hosts[$t][2] . "</td><td class='padme'>" . $valid_hosts[$t][0] . "</td><td class='padme'>" . $valid_hosts[$t][1] . "</td></tr>";
			$found = true;
		}
	}

	if( $found == false )
	{
		print "No IP lease found matching " . $_POST['lease'];
	}
}

print "</table>";

include( "intel_footer.php" );

?>