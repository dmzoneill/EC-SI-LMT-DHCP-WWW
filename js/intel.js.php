<?php

header( 'Content-Type: text/javascript' , true );

$c_min = date( "i" );
$c_sec = date( "s" );
$c_hour = date("H");
$c_year = date("Y");
$c_month = date("n");
$c_day = date("j")

?>

var seconds = 0;
var currenttime = '<?php print date("F d, Y H:i:s", time()); ?>';

var montharray=new Array("January","February","March","April","May","June","July","August","September","October","November","December");
var serverdate=new Date(currenttime);

function padlength(what)
{
	var output=(what.toString().length==1)? "0"+what : what;
	return output;
}

function displaytime()
{
	serverdate.setSeconds(serverdate.getSeconds()+1);
	var datestring=montharray[serverdate.getMonth()]+" "+padlength(serverdate.getDate())+", "+serverdate.getFullYear();
	var timestring=padlength(serverdate.getHours())+":"+padlength(serverdate.getMinutes())+":"+padlength(serverdate.getSeconds());
	document.getElementById("servertime").innerHTML=datestring+" "+timestring;
}

function getlog( logfile , loglines )
{
	$.get( 
		"live-log.php" , 
		{ 
			log: logfile, 
			lines: loglines 
		},
		function( data )
		{
			var datalines = data.split( '\n' );
			var datacount = datalines.length;
			
			var historylines = $( "#logviewer" ).val().split( '\n' );
			var histcount = historylines.length;
		
			var userhistory = parseInt( $( "#historylines" ).val() ) * 100;
			
			var newData = "";
			
			var found = false;
			var trimcount = ( histcount + datacount ) - userhistory;
								
			if( histcount + datacount > userhistory )
			{
				// trim
				for ( var t = trimcount; t < histcount; t++ )
				{
					if( t < histcount - 1 )
					{
						newData += historylines[ t ] + "\n"; 
					}
					else
					{
						newData += historylines[ t ];
					}
				}
				newData += data;
			}
			else
			{
				newData = $( "#logviewer" ).val() + data;
			}
			
			$( "#countdown" ).text( " Received update..." );
			$( "#logviewer" ).val( newData );					
			$( "#logviewer" ).animate({ scrollTop: 999999 } , 1000 );
		}
	);			
}

function init()
{
	if( seconds == 0 )
	{
		seconds = 20;
		getlog( $( "#logfile" ).val() , $( "#loglines" ).val() );
		setTimeout( "init()" , 1000 );
	}
	else if( seconds == 1 )
	{
		seconds -= 1; 
		$( "#countdown" ).text( " Requesting update..." );
		setTimeout( "init()" , 1000 );
	}
	else
	{
		seconds -= 1; 
		$( "#countdown" ).text( "Updating in " +seconds + " seconds" );
		setTimeout( "init()" , 1000 );
	}
}


function pingData( type )
{
	var data = "";
	if( type == "alive" )
	{
		var a = pingdata.subnets[0].alive.length;
		var b = pingdata.subnets[1].alive.length;
		var c = pingdata.subnets[2].alive.length;
		var d = pingdata.subnets[3].alive.length;
		var e = pingdata.subnets[4].alive.length;
		var f = pingdata.subnets[5].alive.length;
		var g = pingdata.subnets[6].alive.length;
		return eval( "[ " + a + "," + b + "," + c + "," + d + "," + e + "," + f + "," + g + " ]"  );
	}
	else if( type == "dead" )
	{
		var a = pingdata.subnets[0].dead.length;
		var b = pingdata.subnets[1].dead.length;
		var c = pingdata.subnets[2].dead.length;
		var d = pingdata.subnets[3].dead.length;
                var e = pingdata.subnets[4].dead.length;
		var f = pingdata.subnets[5].dead.length;
		var g = pingdata.subnets[6].dead.length;
		return eval( "[ " + a + "," + b + "," + c + "," + d + "," + e + "," + f + "," + g + " ]"  );
	}
	else
	{
		var a = pingdata.subnets[0].stolen.length;
		var b = pingdata.subnets[1].stolen.length;
		var c = pingdata.subnets[2].stolen.length;
		var d = pingdata.subnets[3].stolen.length;
                var e = pingdata.subnets[4].stolen.length;
                var f = pingdata.subnets[5].stolen.length;
                var g = pingdata.subnets[6].stolen.length;
		return eval( "[ " + a + "," + b + "," + c + "," + d + "," + e + "," + f + "," + g + " ]" );
	}
}




function chartData( type )
{
	var data = "";
	if( type == "discover" )
	{
		for( var t = starthour; t < lasthour; t++ )
		{
			if( t != dhcpdata.logs[0].HOURS.length - 2 )
			{
				data += dhcpdata.logs[0].HOURS[t].DHCPDISCOVER + ",";
			}
			else
			{
				data += dhcpdata.logs[0].HOURS[t].DHCPDISCOVER;
			}
		}
	}
	else if( type == "offer" )
	{
		for( var t = starthour; t < lasthour; t++ )
		{
			if( t != dhcpdata.logs[0].HOURS.length - 2 )
			{
				data += dhcpdata.logs[0].HOURS[t].DHCPOFFER + ",";
			}
			else
			{
				data += dhcpdata.logs[0].HOURS[t].DHCPOFFER;
			}
		}
	}
	else if( type == "request" )
	{
		for( var t = starthour; t < lasthour; t++ )
		{
			if( t != dhcpdata.logs[0].HOURS.length - 2 )
			{
				data += dhcpdata.logs[0].HOURS[t].DHCPREQUEST + ",";
			}
			else
			{
				data += dhcpdata.logs[0].HOURS[t].DHCPREQUEST;
			}
		}
	}
	else if( type == "ack" )
	{
		for( var t = starthour; t < lasthour; t++ )
		{
			if( t != dhcpdata.logs[0].HOURS.length - 2 )
			{
				data += dhcpdata.logs[0].HOURS[t].DHCPACK + ",";
			}
			else
			{
				data += dhcpdata.logs[0].HOURS[t].DHCPACK;
			}
		}
	}
	else if( type == "brequest" )
	{
		for( var t = starthour; t < lasthour; t++ )
		{
			if( t != dhcpdata.logs[0].HOURS.length - 2 )
			{
				data += dhcpdata.logs[0].HOURS[t].BOOTREQUEST + ",";
			}
			else
			{
				data += dhcpdata.logs[0].HOURS[t].BOOTREQUEST;
			}
		}
	}
	else if( type == "breply" )
	{
		for( var t = starthour; t < lasthour; t++ )
		{
			if( t != dhcpdata.logs[0].HOURS.length - 2 )
			{
				data += dhcpdata.logs[0].HOURS[t].BOOTREPLY + ",";
			}
			else
			{
				data += dhcpdata.logs[0].HOURS[t].BOOTREPLY;
			}
		}
	}
	else if( type == "restart" )
	{
		for( var t = starthour; t < lasthour; t++ )
		{
			if( t != dhcpdata.logs[0].HOURS.length - 2 )
			{
				data += dhcpdata.logs[0].HOURS[t].RESTARTS + ",";
			}
			else
			{
				data += dhcpdata.logs[0].HOURS[t].RESTARTS;
			}
		}
	}
	
	return  eval ( "[ " + data + " ]" );
}


function chartPie()
{
	var discover = chartData( 'discover' );	
	var offer = chartData( 'offer' );
	var brequest = chartData( 'brequest' );
	var breply = chartData( 'breply' );
	var ack = chartData( 'ack' );
	
	var discoverTotal = 0;
	var offerTotal = 0;
	var brequestTotal = 0;
	var breplyTotal = 0;
	var ackTotal = 0;
	
	var total = 0;
	var offerPercent = 0;
	var discoverPercent = 0;
	var brequestPercent = 0;
	var breplyPercent = 0;
	var ackPercent = 0;
	
	for( var t = 0; t < discover.length; t++ )
	{
		if( discover[ t ] == undefined )
		{
			continue;
		}
		discoverTotal += parseInt( discover[ t ] );
	}
	
	for( var t = 0; t < offer.length; t++ )
	{
		if( offer[ t ] == undefined )
		{
			continue;
		}
		offerTotal += parseInt( offer[ t ] );
	}
	
	for( var t = 0; t < brequest.length; t++ )
	{
		if( brequest[ t ] == undefined )
		{
			continue;
		}
		brequestTotal += parseInt( brequest[ t ] );
	}
	
	for( var t = 0; t < breply.length; t++ )
	{
		if( breply[ t ] == undefined )
		{
			continue;
		}
		breplyTotal += parseInt( breply[ t ] );
	}
	
	for( var t = 0; t < ack.length; t++ )
	{
		if( ack[ t ] == undefined )
		{
			continue;
		}
		ackTotal += parseInt( ack[ t ] );
	}
	
	total = offerTotal + discoverTotal + brequestTotal + breplyTotal + ackTotal;
	offerPercent = Math.round( ( offerTotal / total ) * 100 );
	discoverPercent = Math.round( ( discoverTotal / total ) * 100 );
	brequestPercent = Math.round( ( brequestTotal / total ) * 100 );
	breplyPercent = Math.round( ( breplyTotal / total ) * 100 );
	ackPercent = Math.round( ( ackTotal / total ) * 100 );
		
	chart2 = new Highcharts.Chart({
      chart: {
          renderTo: 'container2',
          margin: [50, 200, 50, 50],
		  plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false
      },
      title: {
         text: '( DHCP Requests / DHCP Offers / DHCP ACK ) ( BOOT Requests / BOOT Replies ) '
      },
      plotArea: {
         shadow: null,
         borderWidth: null,
         backgroundColor: null
      },
      tooltip: {
         formatter: function() {
            return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
         }
      },
      plotOptions: {
         pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
               enabled: true,
			   color: '#000000',
               formatter: function() {
                  return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
               }
            }
         }
      },      
       series: [{
         type: 'pie',
         name: 'RequestOfferShare',
         data: [
            ['DHCP Request', discoverPercent ],
            ['DHCP Offer', offerPercent ],
			['BOOT Request', brequestPercent ],
            ['BOOT Reply', breplyPercent ],
			['DHCP Ack', ackPercent ]
         ]
      }]
   });
}


function chartToday()
{
	chart = new Highcharts.Chart({
      chart: {
         renderTo: 'container',
         defaultSeriesType: 'column',
		 margin: [50, 0, 100, 40]
      },
      title: {
         text: 'DHCP Activity Today'
      },
      subtitle: {
         text: 'Source: /var/www/html/log/dhcpd.log - ' + dhcpdata.time
      },
      xAxis: {
         categories: [
            starthour, 
            starthour +1, 
            starthour +2,  
            starthour +3, 
            starthour +4, 
            starthour +5, 
            starthour +6,  
            starthour +7, 
            starthour +8, 
            starthour +9, 
            starthour +10, 
            starthour +11
         ]
      },
      yAxis: {
         min: 0,
         title: {
            text: 'Entries'
         }
      },
      credits: {
         enabled: false
      },
      tooltip: {
         formatter: function() {
            return ''+
               this.series.name +' : '+ this.y +' entries';
         }
      },
   
        series: [
			{
				name: 'DHCPDISCOVER',
				data: chartData( 'discover' )
			}, 
			{
				name: 'DHCPOFFER',
				data: chartData( 'offer' )
			}, 
			{
				name: 'DHCPREQUEST',
				data: chartData( 'request' )
			}, 
			{
				name: 'DHCPACK',
				data: chartData( 'ack' )
			}, 
			{
				name: 'BOOTREQUEST',
				data: chartData( 'brequest' )
			}, 
			{
				name: 'BOOTREPLY',
				data: chartData( 'breply' )
			}, 
			{
				name: 'SERVICE RESTART',
				data: chartData( 'restart' )
			}
		]
   });
}



function chartSubnets()
{
	subnetchart = new Highcharts.Chart({
      chart: {
         renderTo: 'subnetsusage',
         defaultSeriesType: 'column'
      },
      title: {
         text: 'Subnet Ping Analysis'
      },
	   subtitle: {
         text: pingdata.time
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
            text: 'Pings'
         }
      },
	  tooltip: {
         formatter: function() {
            return ''+
               this.series.name +' : '+ this.y;
         }
      },
	  series: [
			{
				name: 'Alive',
				data: pingData( 'alive' )
			}, 
			{
				name: 'Dead',
				data: pingData( 'dead' )
			}
			, 
			{
				name: 'Stolen',
				data: pingData( 'stolen' )
			}
		]
   });
}


function showStolen( )
{
	var stolen18 = pingdata.subnets[ 0 ].stolen;
	var stolen22 = pingdata.subnets[ 1 ].stolen;
	var stolen23 = pingdata.subnets[ 2 ].stolen;
	var stolen212 = pingdata.subnets[ 3 ].stolen;
	var stolen213 = pingdata.subnets[ 4 ].stolen;
	var stolen214 = pingdata.subnets[ 5 ].stolen;
	var stolen216 = pingdata.subnets[ 6 ].stolen;
	var max = 0;
	
	if( stolen18.length > max ) max = stolen18.length;

	if( stolen22.length > max ) max = stolen22.length;

	if( stolen23.length > max ) max = stolen23.length;
	
	if( stolen212.length > max ) max = stolen212.length;
	
	if( stolen213.length > max ) max = stolen213.length;
	
	if( stolen214.length > max ) max = stolen214.length;
	
	if( stolen216.length > max ) max = stolen216.length;
	
	$( "#stolenips" ).append( "<table id='stolentable'></table>" );
	$( "#stolentable" ).append( "<tr><td class='padme'><b>Software</b></td><td class='padme'><b>TM1</b></td><td class='padme'><b>TM2</b></td><td class='padme'><b>Rackroom</b></td><td class='padme'><b>Server</b></td><td class='padme'><b>Server2</b></td><td class='padme'><b>ILab</b></td></tr>" );
	$( "#stolentable" ).append( "<tr><td class='padme'>&nbsp;</td><td class='padme'>&nbsp;</td><td class='padme'>&nbsp;</td></tr>" );
	
	for( var f = 0; f < max; f++ )
	{
		$( "#stolentable" ).append( "<tr><td class='padme' id='st18" + f +  "'></td><td class='padme' id='st22" + f +  "'></td><td class='padme' id='st23" + f +  "'></td><td class='padme' id='st212" + f +  "'></td><td class='padme' id='st213" + f +  "'></td><td class='padme' id='st214" + f +  "'></td><td class='padme' id='st216" + f +  "'></td></tr>" );
	}
	
	for( var f = 0; f < stolen18.length; f++ )
	{
		$( "#st18" + f ).append( stolen18[ f ] );
	}

        for( var f = 0; f < stolen22.length; f++ )
        {
                $( "#st22" + f ).append( stolen22[ f ] );
        }

        for( var f = 0; f < stolen23.length; f++ )
        {
                $( "#st23" + f ).append( stolen23[ f ] );
        }

	for( var f = 0; f < stolen212.length; f++ )
	{
		$( "#st212" + f ).append( stolen212[ f ] );
	}
	
	for( var f = 0; f < stolen213.length; f++ )
	{
		$( "#st213" + f ).append( stolen213[ f ] );
	}
	
	for( var f = 0; f < stolen214.length; f++ )
	{
		$( "#st214" + f ).append( stolen214[ f ] );
	}

        for( var f = 0; f < stolen216.length; f++ )
        {
                $( "#st216" + f ).append( stolen216[ f ] );
        }

}

function pinghost( start , stopid )
{
	var randomnumber = Math.floor( Math.random() * 11 );
	
	$( "#p" + start ).html( "<img src='images/ajax-loader.gif' />" );

	$.get( "ping.php", 
		{ 
			ipaddr: $( "#i" + start ).html(),
			random: randomnumber
		},
		function( data )
		{			
			if( data.indexOf( "?" ) == -1 )
			{
				$( "#p" + start ).html( "<span class='good'>" + data + "</span>" );
			}
			else
			{
				$( "#p" + start ).html( "<span class='bad'>" + data + "</span>" );
			}
			if( start <= stopid )
			{
				pinghost( start +1 , stopid );
			}
		}
	);
}


function verifyIP (IPvalue) 
{
	if( IPvalue.length == 0 )
		return true;
		
	errorString = "";
	theName = "IPaddress";

	var ipPattern = /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;
	var ipArray = IPvalue.match(ipPattern);

	if (IPvalue == "0.0.0.0")
		errorString = errorString + theName + ': '+IPvalue+' is a special IP address and cannot be used here.';
	else if (IPvalue == "255.255.255.255")
		errorString = errorString + theName + ': '+IPvalue+' is a special IP address and cannot be used here.';
	if (ipArray == null)
		errorString = errorString + theName + ': '+IPvalue+' is not a valid IP address.';
	else {
		for (i = 0; i < 4; i++) {
			thisSegment = ipArray[i];
			if (thisSegment > 255) {
				errorString = errorString + theName + ': '+IPvalue+' is not a valid IP address.';
				i = 4;
			}
			if ((i == 0) && (thisSegment > 255)) {
				errorString = errorString + theName + ': '+IPvalue+' is a special IP address and cannot be used here.';
				i = 4;
			}
		}
	}
	extensionLength = 3;
	if (errorString == "")
		return true;
	else
		return false;
}

function isValidVendorEO( veo )  
{ 
	var RegExPattern = /^[0-9a-fA-F:]+$/; 

	if ( !(veo.match(RegExPattern)) )  
	{ 
		if ( veo.length != 38 && veo.length != 0 )
		{
			return false; 
		}
		else
		{
			return true;
		}
	} 
	else 
	{ 
		return true; 
	} 
}   


function submitChange( num )
{
	var veo = isValidVendorEO( $( '#veo' + num ).val() );	
	var ns = verifyIP( $( '#ns' + num ).val() );

	if( ns == false )
	{
		alert( "Invalid next server ip address" );
		return;
	}
	if( veo == false )
	{
		alert( "Invalid vendor encapsulated options" );
		return;
	}
	
	$( "#change" + num ).submit();
}
