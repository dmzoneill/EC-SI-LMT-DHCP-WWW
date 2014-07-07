<?php

include( "intel_header.php" );

if( $ldap->isloggedin() == true  && $ldap->getlogintype() == "admin" )
{
	$lines = file( "/scripts/log/LMTall.txt" );
	
	print "<h2>CRB</h2><br/><table width='90%'>";
	
	$count = 1;
	
	foreach( $lines as $line )
	{
		if( stristr( $line , "|" ) )
		{
			$data = explode( "|" , $line );
			
			if( $data[ 0 ] == "crb" )
			{	
				print "<tr>";		

				$rowcolor = ( $count % 2 == 0 ) ? "style='background-color:#eeeeee; style='color:#007700''" : "style='background-color:#ffffff; style='color:#007700''";
				
				print "<td class='padme2' $rowcolor><b>$count</b></td>";
				
				for( $i = 1 ; $i < count( $data ); $i++ )
				{
					$field = $data[ $i ];
					print "<td class='padme2' $rowcolor>$field</td>";
				}
				
				$count++;
				
				print "</tr>\n";
			}	
		}
	}
	
	print "</table>";
	
	$count = 1;
	
	print "<br/><br/><h2>Asset</h2><br/><table width='90%'>";
	
	foreach( $lines as $line )
	{
		if( stristr( $line , "|" ) )
		{
			$data = explode( "|" , $line );
			
			if( $data[ 0 ] == "asset" )
			{	
				print "<tr>";	

				$rowcolor = ( $count % 2 == 0 ) ? "style='background-color:#eeeeee; style='color:#007700''" : "style='background-color:#ffffff; style='color:#007700''";
				
				print "<td class='padme2' $rowcolor><b>$count</b></td>";
				
				for( $i = 1 ; $i < count( $data ); $i++ )
				{
					$field = $data[ $i ];
					print "<td class='padme2' $rowcolor>$field</td>";
				}
				
				$count++;
				
				print "</tr>\n";
			}				
		}
	}
	
	print "</table>";
	
}
else
{	
	?>
	You need to be an administrator to access this page <a href='login.php'>Login</a>
	<?php
}

include( "intel_footer.php" );


?>