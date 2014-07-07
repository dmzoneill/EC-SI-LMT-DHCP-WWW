<?php

include( "intel_header.php" );


if( $ldap->isloggedin() == true )
{
	$pxe = file( "/var/www/html/pxe/pxe.txt" );
	
	if( isset( $_POST[ 'ip' ] ) && isset( $_POST[ 'mac' ] ) && isset( $_POST[ 'rootpath' ] ) && isset( $_POST[ 'vend' ] ) && isset( $_POST[ 'nextserver' ] ) && isset( $_POST[ 'filename' ] ) )
	{
		print "Updated PXE Options.  Check the timer above for next DHCP update". "<br><br>";
					
		$temp = array();
		
		for( $g = 0; $g < count( $pxe ); $g++ )
		{
			$entry = explode( "|" , $pxe[ $g ] );
			if( ( $ldap->getupn() == $entry[ 0 ] ) && ( $_POST[ 'ip' ] == $entry[ 2 ] ) && ( $_POST[ 'mac' ] == $entry[ 1 ] ) )
			{
				continue;
			}
			else
			{
				if( trim( $pxe[ $g ] ) == "" )
				{
					continue;
				}
				else
				{
					$temp[] = $pxe[ $g ];
				}
			}
		}		

		$stripbad = array( "#", "�", "\"", "�", "$", "%", "^", "&", "*", "(" , ")" , "+" , "=" , "`" , "~" , "|" , "?" , "<" , ">" , "," , "@" , "'" , "\\" , "{" , "}" , "[" , "]" , "_" , ":" , "!" );
		$_POST[ 'rootpath' ] = str_replace( $stripbad , "" , $_POST[ 'rootpath' ] );
		$_POST[ 'filename' ] = str_replace( $stripbad , "" , $_POST[ 'filename' ] );

		$temp[] = $this->getupn() ."|" . $_POST[ 'mac' ] . "|" . $_POST[ 'ip' ] . "|" . $_POST[ 'rootpath' ] . "|" . $_POST[ 'vend' ] . "|" . $_POST[ 'nextserver' ] . "|" . $_POST[ 'filename' ];
		
		$fp = fopen( "/var/www/html/pxe/pxe.txt" , 'w' );
		
		if( $fp )
		{
			for( $j = 0; $j < count( $temp ); $j++ )
			{
				fwrite( $fp , trim( $temp[ $j ] ) . "\n" );					
			}
			fclose( $fp );
		}
	}

	
	$boards = file( "/scripts/log/LMTdhcpLog.txt" );
	$myboards = 0;				
	
	for( $t = 0; $t < count( $boards ); $t++ )
	{
		$board = explode( "|" , $boards[ $t ] );
		$type = $board[ 1 ];
		
		if( $type == "crbreservationentry" || $type == "crbentry" || $type =="assetreservation" )
		{		
			if( $type == "crbreservationentry" || $type == "crbentry" )
			{
				$ixa = $board[ 0 ];
				$type = $board[ 1 ];
				$mac = $board[ 2 ];
				$ip = $board[ 3 ];
				$vlan = $board[ 4 ];
				$rootpath = $board[ 5 ];
				$boardsfile = $board[ 6 ];
				$fullname = $board[ 7 ];
				$email = $board[ 8 ];
				$status = $board[ 9 ];
				$area = $board[ 10 ];
			}
			else
			{
				$ixa = $board[ 0 ];
				$type = $board[ 1 ];
				$mac = $board[ 2 ];
				$ip = $board[ 3 ];
				$vlan = $board[ 4 ];
				$rootpath = $board[ 5 ];
				$boardsfile = $board[ 6 ];
				$assetstate = $board[ 7 ];
				$state = $board[ 8 ];
				$location = $board[ 9 ];
				$email = $board[ 10 ];				
			}

			if( $email == $ldap->getupn() )
			{
				for( $g = 0; $g < count( $pxe ); $g++ )
				{
					$boardpxe = explode( "|" , $pxe[ $g ] );
					
					if( $boardpxe[ 0 ] == $ldap->getupn() && $boardpxe[ 1 ] == $mac && $boardpxe[ 2 ] == $ip )
					{
						$rootpath = $boardpxe[ 3 ];
						$vend = $boardpxe[ 4 ];
						$nextserver = $boardpxe[ 5 ];
						$bootfile = $boardpxe[ 6 ];
					}
				}
				$n = $myboards + 1;
				print "<form action='pxe.php' method='post' id='change$n'>";
				print "<table>";
				print "<tr><td colspan='2'><h3>$ixa</h3></td></tr>";
				print "<tr><td>Mac Address</td><td><input style='width:250px;margin-left:20px' type='text' name='mac' value='$mac' readonly></td></tr>";
				print "<tr><td>Ip address</td><td><input style='width:250px;margin-left:20px' type='text' name='ip' value='$ip' readonly></td></tr>";
				print "<tr><td>Root-path</td><td><input style='width:250px;margin-left:20px' type='text' id='rp$n' value='$rootpath' name='rootpath'></td></tr>";
				print "<tr><td>Vendor Encapsulated Options</td><td><input style='width:250px;margin-left:20px' id='veo$n' name='vend' type='text' value='$vend'></td></tr>";
				print "<tr><td>Next Server</td><td><input style='width:250px;margin-left:20px' type='text' id='ns$n' name='nextserver' value='$nextserver'></td></tr>";
				print "<tr><td>Filename</td><td><input style='width:250px;margin-left:20px' type='text' id='bf$n' value='$bootfile' name='filename'></td></tr>";
				print "<tr><td></td><td><input style='margin-left:20px' type='button' value='update' onclick='submitChange($n)'></td></tr>";
				print "</table>";
				print "</form><br><br>";
				
				$rootpath = "";
				$vend = "";
				$nextserver = "";
				$bootfile = "";
				$myboards++;
			}				
		}
	}	
}
else
{	
	print "To configure your assets / crbs please login as a customer <a href='login.php'>Login</a>";
}

include( "intel_footer.php" );
	
?>
