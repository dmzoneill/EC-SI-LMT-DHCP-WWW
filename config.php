<?php

include( "intel_header.php" );


function natksort( $array ) 
{
	$keys = array_keys($array);
	natsort($keys);

	foreach ($keys as $k)
		$new_array[$k] = $array[$k];

	return $new_array;
} 



if( isset( $_GET['opt'] ) )
{

	if( $_GET['opt'] == "leases" )
	{					
		$labs = array();
		$labs[0] = "/var/www/html/data/boards.SW";
		$labs[1] = "/var/www/html/data/boards.SV";
		$labs[2] = "/var/www/html/data/boards.RackRoom";
		$labs[3] = "/var/www/html/data/boards.RackRoom2";
		$labs[4] = "/var/www/html/data/boards.DataCenter";
		$labs[5] = "/var/www/html/data/boards.TME";
		$labs[6] = "/var/www/html/data/boards.ServerRack";
		$labs[7] = "/var/www/html/data/boards.NTS";
	
		print "<h2>IP Leases</h2><br />";
	
		$sortedlines = array();	
		
		for( $i = 0; $i < count( $labs ); $i++ )
		{	
			$lines = file( $labs[ $i ] );			
			
			foreach( $lines as $line )
			{
				$cols = explode( " " , $line );
				$sortedlines[ $cols[ 2 ] ] = array();
				
				for( $y = 0; $y < count( $cols ); $y++ )
				{
					$sortedlines[ $cols[ 2 ] ][] =  $cols[ $y ];
				}
				$sortedlines[ $cols[ 2 ] ][] =  $labs[ $i ];
			}						
		}
		
		$sortedlines = natksort( $sortedlines );
		
		print "<table>";
		
		$count = 1;
		
		foreach( $sortedlines as $line )
		{
			print "<tr>";
			
			$rowcolor = ( $count % 2 == 0 ) ? "style='background-color:#eeeeee;padding-left:10px;padding-right:10px;'" : "style='background-color:#ffffff;padding-left:10px;padding-right:10px;'";
			
			print "<td $rowcolor><b>" . $count . "</b></td>";
			
			for( $y = 0; $y < count( $line ); $y++ )
			{
				print "<td $rowcolor>" . $line[ $y ] . "</td>";
			}
			
			$count++;
			
			print "</tr>";
		}	
		
		print "</table>";
	}
	else 
	{
		print "<h2>Configuration</h2><br />";
	
		$filename = "/etc/dhcpd.conf";
		$handle = fopen($filename, "rb");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		print "<table><tr><td><pre>" . $contents . "</pre></td></tr></table>";
	}

}

include( "intel_footer.php" );

?>