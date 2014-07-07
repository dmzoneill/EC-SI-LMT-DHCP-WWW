<?php

include( "intel_header.php" );
	
$lines = file( "/etc/dhcpd.conf" );


for( $t=2 ; $t < count( $lines ) -3; $t++ )
{
	if( stristr( $lines[ $t ] , "hardware ethernet" ) && stristr( $lines[ $t + 1 ] , "fixed-address" ) )
	{
		$mac = substr( trim( preg_replace( '/hardware ethernet/' , "" , $lines[ $t ] ) ) , 0 , -1 );
		$ip = substr( trim( preg_replace( '/fixed-address/' , "" , $lines[ $t + 1 ] ) ) , 0 , -1 );
		
		if( validIP( $ip ) && validMAC( $mac ) )
		{
			$valid_hosts[$mac] = $ip;
		}
		else
		{
			$invalid_hosts[$mac] = $ip;
		}
	}
}

natsort( $valid_hosts );
$invalid_hosts = isset( $invalid_hosts ) ? $invalid_hosts : array();
natsort( $invalid_hosts );

if( count( $invalid_hosts ) > 0 )
{
	print "<h2>InValid Static Leases</h2>";
	print "<table>";
	print "<tr><td>MAC</td><td>IP</td><td>PING</td></tr>";
	foreach( $invalid_hosts as $key => $value )
	{
		print "<tr><td class='padme'>" . $key . "</td><td class='padme'>" . $value . "</td><td class='padme'></td></tr>";
	}
	print "</table>";
	
}

if( isset( $valid_hosts ) )
{
	$t = 1;
	print "<h2>Static Leases</h2>";
	print "<table>";
	print "<tr><td class='padme'></td><td class='padme'><b>MAC</b></td><td class='padme'><b>IP</b></td><td class='padme'><b>PING</b></td></tr>";
	print "<tr><td colspan='4'></td></tr>";
	
	foreach( $valid_hosts as $key => $value )
	{
		print "<tr><td class='padme'><b>$t</b></td><td id='m$t' class='padme'>" . $key . "</td><td id='i$t' class='padme'>" . $value . "</td><td id='p$t' class='padme'></td></tr>";
		$t++;
	}
	print "</table>";
}


?>

<script type='text/javascript' >

	$(document).ready( function()
	{	
		if( $( '#m1' ).length > 0 )
		{
			pinghost( 1 , <?php print $t; ?> );			
		}
	});

</script>

<?php

include( "intel_footer.php" );

?>