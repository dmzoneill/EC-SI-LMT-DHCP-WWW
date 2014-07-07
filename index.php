<?php

include( "intel_header.php" );

?>

<div id="container" style="width:900px;height:500px"></div>
<br /><br />
<div id="container2" style="width:900px;height:500px"></div>
<br /><br />
<div id="subnetsusage" style="width:900px;height:400px"></div>
<br /><br />
<div align='center' id="stolenips" style="width:800px;"><h2>Stolen</h2><br /></div>

<script type='text/javascript'>

var chart;
var chart2;
var subnetchart;
var dhcpdata;
var pingdata;

var curr_hour = <?php print date( "G" ); ?>;  
var lasthour = 0;
var starthour = 0;

if( curr_hour - 10 <= 0 )
{
	starthour = 0;
	lasthour = 10;
}
else
{
	starthour = curr_hour - 10;
	lasthour = curr_hour + 1;
}

$(document).ready(function() 
{
	$.getJSON( 'logs/dhcpd-stats.txt' , function( data ) 
	{
		dhcpdata = data;
		$.getJSON( 'logs/ping-stats.txt' , function( data2 ) 
		{
			pingdata = data2;
			chartPie();
			chartSubnets();
			showStolen();
			chartToday();	
		});
	});
});

</script>


<?php

include( "intel_footer.php" );

?>
