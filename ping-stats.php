<?php

require_once( "functions.php" );

$g_icmp_error = "No Error";

// timeout in ms


$sub18live = array();
$sub18dead = array();

print "18\n";

for( $i = 1; $i < 255; $i++ )
{
	$d = ping( "10.243.18.$i" , 2500 );
	print "10.243.18.$i - $d \n";	
	
	if( $d == -1 )
	{
		$sub18dead[] = "10.243.18.$i";
	}
	else
	{
		$sub18live[] = "10.243.18.$i";
	}
}



$sub22live = array();
$sub22dead = array();

print "22\n";

for( $i = 1; $i < 255; $i++ )
{
        $d = ping( "10.243.22.$i" , 2500 );
        print "10.243.22.$i - $d \n";

        if( $d == -1 )
        {
                $sub22dead[] = "10.243.22.$i";
        }
        else
        {
                $sub22live[] = "10.243.22.$i";
        }
}




$sub23live = array();
$sub23dead = array();

print "23\n";

for( $i = 1; $i < 255; $i++ )
{
        $d = ping( "10.243.23.$i" , 2500 );
        print "10.243.23.$i - $d \n";

        if( $d == -1 )
        {
                $sub23dead[] = "10.243.23.$i";
        }
        else
        {
                $sub23live[] = "10.243.23.$i";
        }
}





$sub212live = array();
$sub212dead = array();

print "212\n";

for( $i = 1; $i < 255; $i++ )
{
	$d = ping( "10.237.212.$i" , 2500 );
	print "10.237.212.$i - $d\n";	
	
	if( $d == -1 )
	{
		$sub212dead[] = "10.237.212.$i";
	}
	else
	{
		$sub212live[] = "10.237.212.$i";
	}
}

$sub213live = array();
$sub213dead = array();

print "213\n";

for( $i = 1; $i < 255; $i++ )
{
	$d = ping( "10.237.213.$i" , 2500 );
	print "10.237.213.$i - $d\n";	

	if( $d == -1 )
	{
		$sub213dead[] = "10.237.213.$i";
	}
	else
	{
		$sub213live[] = "10.237.213.$i";
	}
}


$sub214live = array();
$sub214dead = array();

print "214\n";

for( $i = 1; $i < 255; $i++ )
{
	$d = ping( "10.237.214.$i" , 2500 );
	print "10.237.214.$i - $d\n";	

	if( $d == -1 )
	{
		$sub214dead[] = "10.237.214.$i";
	}
	else
	{
		$sub214live[] = "10.237.214.$i";
	}
}


$sub216live = array();
$sub216dead = array();

print "216\n";

for( $i = 1; $i < 255; $i++ )
{
        $d = ping( "10.237.216.$i" , 2500 );
        print "10.237.216.$i - $d\n";

        if( $d == -1 )
        {
                $sub216dead[] = "10.237.216.$i";
        }
        else
        {
                $sub216live[] = "10.237.216.$i";
        }
}













// LMT assigned
$myFile = "/etc/dhcpd.conf";
$fh = fopen( $myFile , 'r' );
$theData = fread( $fh , filesize( $myFile ) );
fclose( $fh );
preg_match_all( "/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/" , $theData , $dhcpdconf , PREG_SET_ORDER );

// dynamically assigned
$myFile = "/var/lib/dhcpd/dhcpd.leases";
$fh = fopen( $myFile , 'r' );
$theData = fread( $fh , filesize( $myFile ) );
fclose( $fh );
preg_match_all( "/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/" , $theData , $dhcpdleases , PREG_SET_ORDER );

// nts assigned
$myFile = "/scripts/boards/boards.NTS";
$fh = fopen( $myFile , 'r' );
$theData = fread( $fh , filesize( $myFile ) );
fclose( $fh );
preg_match_all( "/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/" , $theData , $nts , PREG_SET_ORDER );

// get all allowed ip together
$arr = array_merge( $dhcpdconf , $dhcpdleases, $nts );




// take out duplicates
$knownips = array();
foreach( $arr as $entry )
{
	if( !in_array( $entry[ 0 ] , $knownips ) )
	{
		$knownips[] = $entry[ 0 ];
	}
}
natsort( $knownips );


$alive = array_merge( $sub213live , $sub18live , $sub212live , $sub214live );
$stolen = array();
foreach( $alive as $entry )
{
	if( !in_array( $entry , $knownips ) )
	{
		$stolen[] = $entry;
	}
}
natsort( $stolen );


$stole18 = array();
$stole22 = array();
$stole23 = array();
$stole212 = array();
$stole213 = array();
$stole214 = array();
$stole216 = array();


foreach( $stolen as $stole )
{
	if( stristr( $stole , "10.237.212." ) )
	{
		$stole212[] = $stole;
	}
	else if( stristr( $stole , "10.237.213." ) )
	{
		$stole213[] = $stole;
	}
	else if( stristr( $stole , "10.237.214." ) )
	{
		$stole214[] = $stole;
	}
        else if( stristr( $stole , "10.237.216." ) )
        {
                $stole216[] = $stole;
        }
        else if( stristr( $stole , "10.243.22." ) )
        {
                $stole22[] = $stole;
        }
        else if( stristr( $stole , "10.243.23." ) )
        {
                $stole23[] = $stole;
        }
	else
	{
		$stole18[] = $stole;
	}
}
























$json = "{\n";
$json .= "	\"time\" : \"" . date('l jS \of F Y h:i A') . "\",\n";
$json .= "	\"subnets\" : [\n";



$json .= "		{\n";
$json .= "			\"alive\" : [\n";

for( $t = 0; $t < count( $sub18live ); $t++ )
{
	if( $t < count( $sub18live ) - 1 )
	{
		$json .= "				\"" . $sub18live[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub18live[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";
$json .= "			\"dead\" : [\n";

for( $t = 0; $t < count( $sub18dead ); $t++ )
{
	if( $t < count( $sub18dead ) - 1 )
	{
		$json .= "				\"" . $sub18dead[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub18dead[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";

$json .= "			\"stolen\" : [\n";

for( $t = 0; $t < count( $stole18 ); $t++ )
{
	if( $t < count( $stole18 ) - 1 )
	{
		$json .= "				\"" . $stole18[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $stole18[ $t ] . "\"\n";
	}
} 

$json .= "			]\n";

$json .= "		},\n";










$json .= "		{\n";
$json .= "			\"alive\" : [\n";

for( $t = 0; $t < count( $sub22live ); $t++ )
{
	if( $t < count( $sub22live ) - 1 )
	{
		$json .= "				\"" . $sub22live[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub22live[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";
$json .= "			\"dead\" : [\n";

for( $t = 0; $t < count( $sub22dead ); $t++ )
{
	if( $t < count( $sub22dead ) - 1 )
	{
		$json .= "				\"" . $sub22dead[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub22dead[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";

$json .= "			\"stolen\" : [\n";

for( $t = 0; $t < count( $stole22 ); $t++ )
{
	if( $t < count( $stole22 ) - 1 )
	{
		$json .= "				\"" . $stole22[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $stole22[ $t ] . "\"\n";
	}
} 

$json .= "			]\n";

$json .= "		},\n";









$json .= "		{\n";
$json .= "			\"alive\" : [\n";

for( $t = 0; $t < count( $sub23live ); $t++ )
{
	if( $t < count( $sub23live ) - 1 )
	{
		$json .= "				\"" . $sub23live[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub23live[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";
$json .= "			\"dead\" : [\n";

for( $t = 0; $t < count( $sub23dead ); $t++ )
{
	if( $t < count( $sub23dead ) - 1 )
	{
		$json .= "				\"" . $sub23dead[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub23dead[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";

$json .= "			\"stolen\" : [\n";

for( $t = 0; $t < count( $stole23 ); $t++ )
{
	if( $t < count( $stole23 ) - 1 )
	{
		$json .= "				\"" . $stole23[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $stole23[ $t ] . "\"\n";
	}
} 

$json .= "			]\n";

$json .= "		},\n";











$json .= "		{\n";
$json .= "			\"alive\" : [\n";

for( $t = 0; $t < count( $sub212live ); $t++ )
{
	if( $t < count( $sub212live ) - 1 )
	{
		$json .= "				\"" . $sub212live[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub212live[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";
$json .= "			\"dead\" : [\n";

for( $t = 0; $t < count( $sub212dead ); $t++ )
{
	if( $t < count( $sub212dead ) - 1 )
	{
		$json .= "				\"" . $sub212dead[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub212dead[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";

$json .= "			\"stolen\" : [\n";

for( $t = 0; $t < count( $stole212 ); $t++ )
{
	if( $t < count( $stole212 ) - 1 )
	{
		$json .= "				\"" . $stole212[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $stole212[ $t ] . "\"\n";
	}
} 

$json .= "			]\n";

$json .= "		},\n";






$json .= "		{\n";
$json .= "			\"alive\" : [\n";

for( $t = 0; $t < count( $sub213live ); $t++ )
{
	if( $t < count( $sub213live ) - 1 )
	{
		$json .= "				\"" . $sub213live[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub213live[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";
$json .= "			\"dead\" : [\n";

for( $t = 0; $t < count( $sub213dead ); $t++ )
{
	if( $t < count( $sub213dead ) - 1 )
	{
		$json .= "				\"" . $sub213dead[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub213dead[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";

$json .= "			\"stolen\" : [\n";

for( $t = 0; $t < count( $stole213 ); $t++ )
{
	if( $t < count( $stole213 ) - 1 )
	{
		$json .= "				\"" . $stole213[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $stole213[ $t ] . "\"\n";
	}
} 

$json .= "			]\n";
$json .= "		},\n";









$json .= "		{\n";
$json .= "			\"alive\" : [\n";

for( $t = 0; $t < count( $sub214live ); $t++ )
{
	if( $t < count( $sub214live ) - 1 )
	{
		$json .= "				\"" . $sub214live[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub214live[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";
$json .= "			\"dead\" : [\n";

for( $t = 0; $t < count( $sub214dead ); $t++ )
{
	if( $t < count( $sub214dead ) - 1 )
	{
		$json .= "				\"" . $sub214dead[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub214dead[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";

$json .= "			\"stolen\" : [\n";

for( $t = 0; $t < count( $stole214 ); $t++ )
{
	if( $t < count( $stole214 ) - 1 )
	{
		$json .= "				\"" . $stole214[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $stole214[ $t ] . "\"\n";
	}
} 

$json .= "			]\n";
$json .= "		},\n";








$json .= "		{\n";
$json .= "			\"alive\" : [\n";

for( $t = 0; $t < count( $sub216live ); $t++ )
{
	if( $t < count( $sub216live ) - 1 )
	{
		$json .= "				\"" . $sub216live[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub216live[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";
$json .= "			\"dead\" : [\n";

for( $t = 0; $t < count( $sub216dead ); $t++ )
{
	if( $t < count( $sub216dead ) - 1 )
	{
		$json .= "				\"" . $sub216dead[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $sub216dead[ $t ] . "\"\n";
	}
} 

$json .= "			],\n";

$json .= "			\"stolen\" : [\n";

for( $t = 0; $t < count( $stole216 ); $t++ )
{
	if( $t < count( $stole216 ) - 1 )
	{
		$json .= "				\"" . $stole216[ $t ] . "\",\n";
	}
	else
	{
		$json .= "				\"" . $stole216[ $t ] . "\"\n";
	}
} 

$json .= "			]\n";
$json .= "		}\n";



$json .= "	]\n";
$json .= "}\n";


$myFile = "/var/www/html/logs/ping-stats.txt";
$fh = fopen( $myFile , 'w' ) or die( "can't open file" );
fwrite( $fh , $json );
fclose($fh);

?>
