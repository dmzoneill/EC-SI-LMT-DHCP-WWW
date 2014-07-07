<?php

include( "ssh.class.php" );

$link = mysql_connect( "localhost" , "root" , "Aga!g04." );
$db = mysql_select_db( "computerTracking" );

error_reporting(0);

function sshConnect( $ip , $debug )
{
	$passwordless = false;
	$counter = 0;
	$result = -1;
	$passwords = array( "tester" , "basisadmin" , "basisbuild" , "Ins3cur3" , "Aga!g04." , "\$abc.123\$" , "\$1lahinch\$" , "s" , "basisppdsw" );
	$connected = false;
	$i = count( $passwords ) - 1;
	
	while( $connected == false && $i > -1 ) 
	{
		$ssh = new ssh2( $ip , "22" );
		if( $counter == 0 )
		{
			$passwordless = $ssh->authPasswordLess( "root" ); 
			$counter++;
		}
		
		if( $passwordless == false )
		{
			$ssh->authPassword( "root" , $passwords[ $i ] );
		}
	
		if( $ssh->getlog() == "" )
		{
			$pw = ( $passwords[ $i ] == "Aga!g04." ) ? "Contact IT" : $passwords[ $i ];
			$version = trim( $ssh->cmdExec( "cat /etc/*-release | tail -n 2" ) );
			$lastlogin = trim( $ssh->cmdExec( "/usr/bin/last -a -n 1 root | /usr/bin/head -1" ) );
			
			if( stristr( $lastlogin , "root" ) )
			{
				// Remove consecutive white space from output
				$lastlogin = preg_split( "/[\t\s]+/", $lastlogin );
				$lastdate = $lastlogin[ 3 ] . " " . $lastlogin[ 4 ] . " " . $lastlogin[ 5 ];
				$lasthost = $lastlogin[ ( count($lastlogin) - 1 ) ];
				
				// Split host further
				if ( stristr( $lasthost , "ger.corp.intel.com" ) )
				{

					$remotehost = $lasthost;
					$lasthost = preg_split( "/\.ger\.corp\.intel\.com+/", $lasthost );
					$lasthost = preg_split( "/-+/", $lasthost [ 0 ] );
					$lastuser = $lasthost[ 0 ];
					
				}
				elseif ( stristr( $lasthost, "ir.intel.com" ) )
				{
					$remotehost = $lasthost;
					$lastuser = "Unknown";
				}
				else
				{
					$remotehost = "Unknown";
					$lastuser = "Unknown";
				}
				
			}
			
			if( stristr( $version , "VERSION" ) )
			{
				$version = explode( "\n" , $version );
				$version = $version[ 0 ];
			}
			else if( stristr( $version , "DISTRIB_CODENAME" ) )
			{
				$release = explode( "\n" , $version );
				
				$codename = explode( "=" , $release[ 0 ] );
				$codename = $codename[ 1 ];
				
				$distro = explode( "=" , $release[ 1 ] );
				$distro = eregi_replace( "\"" , "" , $distro[ 1 ] );
				$version = $distro . " ($codename)";
			}
			else
			{
				if( stristr( $version , "\n" ) )
				{
					$version = explode( "\n" , $version );					
					if( stristr( $version[ 0 ] , "LSB_VERSION=" ) )
					{
						$version = $version[ 1 ];
					}
					else
					{
						$version = $version[ 0 ];
					}
				}
				if( $version == "" )
				{
					//$version = trim( $ssh->cmdExec( "uname -s -v" ) );
				}
			}

			$pw = ( $passwordless ) ? "gaap" : $pw;
			$result = "linux - $ip - " . $pw . " - ssh - " . $version . " - root";
			//$result = "linux - $ip - " . $pw . " - ssh - " . $version . " - root - " . $lastdate . " - " . $lastuser;
			
			$sql = "select count(*) from access where accessDate = '$lastdate' AND user = '$lastuser' AND remoteHost = '$remotehost' ";
			$check = mysql_query( $sql );
			$check = mysql_result( $check, 0 );
			
			print $sql . "\n";
			
			$getid = mysql_query ( "select id from log where ip = '$ip'" );
		
			$num = mysql_num_rows ( $getid );
			if ( $num > 0 )
			{
				$getid = mysql_result( $getid, 0 );
			}			
								
			if ( $check == 0 && $num > 0)
			{
				$insert = mysql_query( "insert into access ( logId , accessDate , user , remoteHost ) values( '$getid','$lastdate','$lastuser','$remotehost' )" , $link );
			}

			print 'Duplicate: ' . $check . '\n';
			print 'Record ID: ' . $getid . '\n';
			
			$connected = true;
		}
		else
		{
			//print $ssh->getlog();
		}
		
		$ssh = NULL;
		$i--;
	}
	
	return $result;
}


function smbConnect( $ip )
{
	$result = -1;
	$passwords = array( "\\\$abc.123\\\$" , "\\\$Zpx032d" , "\\\$1lahinch\\\$" , "tester" );
	$connected = false;
	$i = count( $passwords ) - 1;	
	
	while( $connected == false && $i > -1 ) 
	{
		$p = trim( shell_exec( "sudo smbclient //$ip/c\\\$ -UAdministrator%" . $passwords[ $i ] . " -c ls 2>&1" ) );
		$q = trim( shell_exec( "sudo smbclient //$ip/c\\\$ -Uinteladmin%" . $passwords[ $i ] . " -c ls 2>&1" ) );
	
		if( stristr( $p , "blocks" ) )
		{
			if( preg_match( "/Server=\[.*\] /" , $p , $os ) > 0 )
			{
				$os = explode( "[" , $os[ 0 ] );
				$os = explode( "]" , $os[ 1 ] );
				$os = $os[ 0 ];
			}			
			else if( preg_match( "/OS=\[.*\] /" , $p , $os ) > 0 )
			{
				$os = explode( "[" , $os[ 0 ] );
				$os = explode( "]" , $os[ 1 ] );
				$os = $os[ 0 ];
			}
			else
			{
				$os = "";
			}
			
			$pw = stripslashes( ereg_replace( "\\$" , "&#36;" , $passwords[ $i ] ) );
			$result = "windows - $ip - $pw - smb - $os - Administrator";
			$connected = true;
		}
		
		
		if( stristr( $q , "blocks" ) )
		{
			if( preg_match( "/Server=\[.*\] /" , $q , $os ) > 0 )
			{
				$os = explode( "[" , $os[ 0 ] );
				$os = explode( "]" , $os[ 1 ] );
				$os = $os[ 0 ];
			}			
			else if( preg_match( "/OS=\[.*\] /" , $q , $os ) > 0 )
			{
				$os = explode( "[" , $os[ 0 ] );
				$os = explode( "]" , $os[ 1 ] );
				$os = $os[ 0 ];
			}
			else
			{
				$os = "";
			}
			
			$pw = stripslashes( ereg_replace( "\\$" , "&#36;" , $passwords[ $i ] ) );
			$result = "windows - $ip - $pw - smb - $os - inteladmin";
			$connected = true;
		}
		
		$p = NULL;
		$os = NULL;
		$i--;
			
	}
	
	return $result;
}



function portscan( $ip )
{
	$tcpports = array( "21" , "22" , "23" , "25" , "53" , "80" , "139" , "8080" , "3306" , "3389" );
	$udpports = array( "67" );
	
	$result = "";
	
	foreach( $tcpports as $port )
	{
		$tcpscan = shell_exec( "sudo /usr/bin/nmap -sT $ip -p T:$port --host-timeout 1501 2>&1" );
		if( stristr( $tcpscan , "$port/tcp open" ) )
		{
			$result .= "$port|";
		}
	}
	
	foreach( $udpports as $port )
	{
		$udpscan = shell_exec( "sudo /usr/bin/nmap -sU $ip -p U:$port --host-timeout 1501 2>&1" );
		if( stristr( $udpscan , "$port/udp open" ) )
		{
			$result .= "$port|";
		}
	}
	
	return $result;
}


function updateDb( $entry )
{
	global $link;
	
	if( $link )
	{
		$data = explode( "-" , $entry );
		$os = trim( $data[ 0 ] );
		$ip = trim( $data[ 1 ] );
		$pass = trim( $data[ 2 ] );
		$service = trim( $data[ 3 ] );
		$distro = trim( $data[ 4 ] );
		$user = trim( $data[ 5 ] );
		$ports = trim( $data[ 6 ] );
		$up = trim( $data[ 7 ] );

		$exists = "select count(*) from log where ip = '$ip'";
		$exists = mysql_query( $exists , $link );
		$exists = mysql_result( $exists , 0 , 0 );
		
		if( $exists == "1" )
		{
			$exists = "select * from log where ip = '$ip'";
			$exists = mysql_query( $exists , $link );
			$cos = mysql_result( $exists , 0 , 1 );
			$cdistro = mysql_result( $exists , 0 , 2 );
			$cip = mysql_result( $exists , 0 , 3 );
			$cuser = mysql_result( $exists , 0 , 4 );
			$cpass = mysql_result( $exists , 0 , 5 );			
			$cservice = mysql_result( $exists , 0 , 6 );
			$cports = mysql_result( $exists , 0 , 7 );
			$cup = mysql_result( $exists , 0 , 8 );
			
			if( ( $cos != $os ) || ( $distro != $cdistro ) || ( $pass != $cpass ) || ( $service != $cservice ) )
			{
				$update = mysql_query( "update log set os = '$os', distro = '$distro', user = '$user', pass = '$pass', proto = '$service', ports = '$ports', dayup = '1' where ip = '$ip'" , $link );
			}
			else
			{
				$cup = $cup + 1;
				$update = mysql_query( "update log set os = '$os', distro = '$distro', user = '$user', pass = '$pass', proto = '$service', ports = '$ports', dayup = '$cup' where ip = '$ip'" , $link );
			}			
		}
		else
		{
			$insert = mysql_query( "insert into log ( os , distro , ip , user , pass , proto , ports , dayup ) values( '$os','$distro','$ip','$user','$pass','$service','$ports','1' )" , $link );
		}
		
		print_r( $data );		
	}
	else
	{
		print "No Connection";
	}

}


function updateDbUnknown( $ip , $ports )
{
	global $link;
	
	if( $link )
	{
		$exists = "select count(*) from log where ip = '$ip'";
		$exists = mysql_query( $exists , $link );
		$exists = mysql_result( $exists , 0 , 0 );
		
		if( $exists == "1" )
		{
			$exists = "select * from log where ip = '$ip'";
			$exists = mysql_query( $exists , $link );
			$cos = mysql_result( $exists , 0 , 1 );
			$cdistro = mysql_result( $exists , 0 , 2 );
			$cip = mysql_result( $exists , 0 , 3 );
			$cuser = mysql_result( $exists , 0 , 4 );
			$cpass = mysql_result( $exists , 0 , 5 );			
			$cservice = mysql_result( $exists , 0 , 6 );
			$cports = mysql_result( $exists , 0 , 7 );
			$cup = mysql_result( $exists , 0 , 8 );
			
			if( ( 'unknown' != $os ) || ( 'unknown' != $cdistro ) || ( 'unknown' != $cpass ) || ( 'unknown' != $cservice ) )
			{
				$update = mysql_query( "update log set os = 'unknown', distro = 'unknown', user = 'unknown', pass = 'unknown', proto = 'unknown', ports = '$ports', dayup = '1' where ip = '$ip'" , $link );
			}
			else
			{
				$cup = $cup + 1;
				$update = mysql_query( "update log set os = 'unknown', distro = 'unknown', user = 'unknown', pass = 'unknown', proto = 'unknown', ports = '$ports', dayup = '$cup' where ip = '$ip'" , $link );
			}			
		}
		else
		{
			$insert = mysql_query( "insert into log ( os , distro , ip , user , pass , proto , ports , dayup ) values( 'unknown','unknown','$ip','unknown','unknown','unknown','$ports','1' )" , $link );
		}
		
		print_r( $data );		
	}
	else
	{
		print "No Connection";
	}

}


if( count( $argv ) == 3 )
{
	$ip = $argv[ 1 ];
	$portscan = portscan( $ip );

	if( $portscan != "" )
	{
		$ssh = sshConnect( $ip, false );
		$smb = smbConnect( $ip );
		
		if( $ssh != -1 )
		{
			print $ssh . " - $portscan\n";
			updateDb( $ssh . " - $portscan" );
		}
		else if( $smb != -1 )
		{
			print $smb . " - $portscan\n";
			updateDb( $smb . " - $portscan" );
		}
		else
		{
			print "unknown - $ip - $portscan\n";
			updateDbUnknown( $ip , $portscan );
		}			
	}
	else
	{
		print "dead - $ip - $portscan\n";
		$update = mysql_query( "delete from log where ip = '$ip'" , $link );
	}
	
	exit;
}
else if( isset( $_GET[ 'ip' ] ) )
{
	$ip = $_GET[ 'ip' ];
	print $ip . "<br>";
	
	$ssh = sshConnect( $ip , true );
	$smb = smbConnect( $ip );
	$data = portscan( $ip ) . "<br>";
	
	if( $ssh != -1 )
	{
		$data .= $ssh . "<br>";
	}
	else if( $smb != -1 )
	{
		$data .= $smb . "<br>";
	}
	else
	{
		$data .= "unknown - $ip - unknown<br>";
	}
	print $data;
}
else if( isset( $_GET['dumpdb'] ) )
{
	$result = mysql_query( "select * from log order by INET_ATON(ip) asc" );
	$lines[] = array();

        while( $row = mysql_fetch_array( $result , MYSQL_NUM ) )
        {
		$lines[] = $row;	
	}
	
	print serialize( $lines );
}
else
{
	include( "intel_header.php" );
	
	print "<div id='container' style='width:900px;height:800px'></div><br /><br />";
	
	echo "<br /><table>";
	
	$count = 1;
	
	$vlans = array();
	$vlans[ 18 ] = array( 0 , 0 , 0 );
	$vlans[ 22 ] = array( 0 , 0 , 0 );
	$vlans[ 23 ] = array( 0 , 0 , 0 );
	$vlans[ 212 ] = array( 0 , 0 , 0 );
	$vlans[ 213 ] = array( 0 , 0 , 0 );	
	$vlans[ 214 ] = array( 0 , 0 , 0 );	
	$vlans[ 216 ] = array( 0 , 0 , 0 );	

	$distros = array();	
	
	print "<tr><td class='padme'></td><td class='padme' width='80'><b>OS</b></td><td class='padme'><b>VERSION</b></td><td class='padme'><b>IP</b></td><td class='padme'><b>USER</b></td><td class='padme'><b>PASS</b></td><td class='padme'><b>PROTO</b></td><td class='padme'><b>PORTS</b></td><td class='padme'><b>Days Up</b></td></tr><tr><td colspan='8'>&nbsp;</td></tr>" ;
		
	$result = mysql_query( "select * from log order by INET_ATON(ip) asc" );

	while( $row = mysql_fetch_array( $result , MYSQL_NUM ) ) 
	{
		$ip = explode( "." , $row[ 3 ] );
		$vlan = $ip[ 2 ];
		$distro = "";
		$user = "";
		$pass = "";
		$img = "";
		$ports = "";
		
		if( trim( $row[ 1 ] ) != "dead" )
		{
			$rowcolor = ( $count % 2 == 0 ) ? "style='background-color:#eeeeee'" : "style='background-color:#ffffff'";
					
			if( trim( $row[ 1 ] ) == "linux" )
			{
				$img = "<img src='images/linux.gif' width='16' height='16' style='vertical-align:text-bottom;' />";
				$user = $row[ 4 ];
				$pass = $row[ 5 ];
				$distro = $row[ 2 ];
				$distro = preg_replace( "/DISTRIB_DESCRIPTION=\"/" , "" , $distro );
				$distro = preg_replace( "/\"/" , "" , $distro );
				$distro = preg_replace( "/BUILD: /" , "" , $distro );
				$vlans[ $vlan ][ 0 ] += 1;
			}
			else if( trim( $row[ 1 ] ) == "windows" )
			{
				$img = "<img src='images/windows.gif' width='16' height='16' style='vertical-align:text-bottom;' />";
				$user = $row[ 4 ];
				$pass = $row[ 5 ];
				$distro = $row[ 2 ];
				$vlans[ $vlan ][ 1 ] += 1;
			}
			else
			{
				$img = "<img src='images/question.gif' width='16' height='16' style='vertical-align:text-bottom;' />";
				$pass = "";
				$user = "";
				$vlans[ $vlan ][ 2 ] += 1;
			}					
			
			$passlen = strlen( $pass );
			if( $passlen > 2 && !stristr( $pass , "Contact" ) )
			{		
				if( stristr( $pass , "#36" ) )
				{
					$passlen = $passlen - ( substr_count( $pass , "#36" ) * 4 );
					$pass = preg_replace( '/&#36;/' , "\$" , $pass );
				}			
				$pass_end = substr( $pass , -2 );
				$pass_start = substr( $pass , 0 , 2 );
				$pass_endlen = strlen( substr( $pass , 3 , $passlen - 1 ) );
				$pass = $pass_start . str_repeat( "*" , $passlen - 4 ) . $pass_end;
			}
			 
			
			print "<tr>";
			print "<td class='padme' $rowcolor><b>" . $count . "</b></td>";
			print "<td class='padme' width='80' $rowcolor>$img " . $row[ 1 ] . "</td>";
			$distro = ( strlen( $distro ) > 40 ) ? substr( $distro , 0 , 40 ) : $distro;
			$distro = strip_tags( $distro );
			print "<td class='padme' $rowcolor>" . $distro . "</td>";
			print "<td class='padme' $rowcolor>" . $row[ 3 ] . "</td>";
			print "<td class='padme' $rowcolor>" . $user . "</td>";
			print "<td class='padme' $rowcolor>" . $pass . "</td>";
			print "<td class='padme' $rowcolor>";
			print isset( $row[ 6 ] ) ? $row[ 6 ] : "";
			print "</td>";
			
			$ports = $row[ 7 ];
			
			if( stristr( $ports , "|" ) )
			{
				$ports = substr( $ports , 0 , strlen( $ports ) - 1 );
				$ports = str_replace( "|" , ", " , $ports );
			}
			
			print "<td class='padme' $rowcolor>" . $ports . "</td>";
			print "<td class='padme' $rowcolor>" . $row[ 8 ] . "</td>";
			print "</tr>" ;
			
			$count++;
			
			$distro = trim( $distro );
			
			$distro = ( $distro == "" ) ? "Unknown" : $distro;
			
			if( !isset( $distros[ $distro ] ) )
			{			
				if( $vlan == 18 )
				{
					$first = 1;
					$second = 0;
					$third = 0;
					$fourth = 0;
					$fifth = 0;
					$sixth = 0;
					$seventh = 0;
				}
                                else if ( $vlan == 22 )
                                {
                                        $first = 0;
                                        $second = 1;
                                        $third = 0;
                                        $fourth = 0;
                                        $fifth = 0;
                                        $sixth = 0;
                                        $seventh = 0;
                                }		
                                else if ( $vlan == 23 )
                                {
                                        $first = 0;
                                        $second = 0;
                                        $third = 1;
                                        $fourth = 0;
                                        $fifth = 0;
                                        $sixth = 0;
                                        $seventh = 0;
                                }	
				else if ( $vlan == 212 )
				{
					$first = 0;
					$second = 0;
					$third = 0;
					$fourth = 1;
                                        $fifth = 0;
                                        $sixth = 0;
                                        $seventh = 0;
				}
				else if ( $vlan == 213 )
				{
					$first = 0;
					$second = 0;
					$third = 0;
					$fourth = 0;
                                        $fifth = 1;
                                        $sixth = 0;
                                        $seventh = 0;
				}
				else if ( $vlan == 214 )
                                {
                                        $first = 0;
                                        $second = 0;
                                        $third = 0;
                                        $fourth = 0;
                                        $fifth = 0;
                                        $sixth = 1;
                                        $seventh = 0;
                                }
				else
				{
					$first = 0;
					$second = 0;
					$third = 0;
					$fourth = 0;
                                        $fifth = 0;
                                        $sixth = 0;
                                        $seventh = 1;
				}
				$distros[ $distro ] = array( $first , $second , $third , $fourth , $fifth , $sixth , $seventh );
			}
			else
			{
				if( $vlan == 18 )
				{				
					$distros[ $distro ][ 0 ] += + 1;
				}			
				else if ( $vlan == 22 )
                                {
                                        $distros[ $distro ][ 1 ] += + 1;
                                }
				else if ( $vlan == 23 )
                                {
                                        $distros[ $distro ][ 2 ] += + 1;
                                }
				else if ( $vlan == 212 )
				{
					$distros[ $distro ][ 3 ] += + 1;
				}
				else if ( $vlan == 213 )
				{
					$distros[ $distro ][ 4 ] += + 1;
				}
                                else if ( $vlan == 214 )
                                {
                                        $distros[ $distro ][ 5 ] += + 1;
                                }
				else
				{
					$distros[ $distro ][ 6 ] += + 1;
				}
			}
		}
	}

	mysql_free_result( $result );
	
	print "</table>";	

	?>

	<script type='text/javascript'>
	var chart;
	$(document).ready(function() {
	   chart = new Highcharts.Chart({
		  chart: {
			 renderTo: 'container',
			 defaultSeriesType: 'column',
			 margin: [60, 10, 550, 40 ]
		  },
		  title: {
			 text: 'OS / Credential Check Statistics'
		  },
		  subtitle: {
			 text: ''
		  },
		  xAxis: {
			 categories: [
				'18',
				'22',
				'23', 
				'212', 
				'213',
				'214',
				'216'
			 ]
		  },
		  yAxis: {
			 min: 0,
			 title: {
				text: 'entries'
			 }
		  },
		  legend: 
		  {
			layout: 'vertical',
			backgroundColor: '#FFFFFF',
			align: 'center',
			verticalAlign: 'bottom',
			x: 0,
			y: 0
		  },
		  tooltip: {
			 formatter: function() {
				return this.series.name + ' : ' + this.y;
			 }
		  },		  
		  plotOptions: {
			 column: {
				pointPadding: 0.2,
				borderWidth: 0
			 }
		  },
			   series: [
			<?php
					ksort( $distros );
					$keys = array_keys( $distros );
					for( $i = 0; $i < count( $keys ) -1; $i++ )
					{
						print "{";
							print "name: '". $keys[ $i ] ."',";
							print "data: [";
								print $distros[ $keys[ $i ] ][ 0 ] . "," . $distros[ $keys[ $i ] ][ 1 ] . "," . $distros[ $keys[ $i ] ][ 2 ] . "," . $distros[ $keys[ $i ] ][ 3 ] . "," . $distros[ $keys[ $i ] ][ 4 ] . "," . $distros[ $keys[ $i ] ][ 5 ] . "," . $distros[ $keys[ $i ] ][ 6 ];
							print "]";
						print "},";
					}
					
					print "{";
						print "name: '". $keys[ count( $keys ) -1 ] ."',";
						print "data: [";
							print $distros[ $keys[ count( $keys ) -1 ] ][ 0 ] . "," . $distros[ $keys[ count( $keys ) -1 ] ][ 1 ] . "," . $distros[ $keys[ count( $keys ) -1 ] ][ 2 ] . "," . $distros[ $keys[ count( $keys ) -1 ] ][ 3 ] . "," . $distros[ $keys[ count( $keys ) -1 ] ][ 4 ]  . "," . $distros[ $keys[ count( $keys ) -1 ] ][ 5 ]  . "," . $distros[ $keys[ count( $keys ) -1 ] ][ 6 ];
						print "]";
					print "}";
			?>
			]
	   });
	   
	});

	</script>

	<?php
	
	include( "intel_footer.php" );
}

?>
