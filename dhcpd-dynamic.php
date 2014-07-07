<?php

include( "intel_header.php" );

$t = 1;

print "<table>";
print "<tr><td class='padme'></td><td class='padme'><b>MAC</b></td><td class='padme'><b>IP</b></td><td class='padme'><b>PING</b></td></tr>";
print "<tr><td colspan='4'></td></tr>";

for( $z = 10; $z < 41; $z++ )
{
	print "<tr><td class='padme'><b>$t</b></td><td id='m$t' class='padme'>.</td><td id='i$t' class='padme'>10.243.18." . $z . "</td><td id='p$t' class='padme'></td></tr>";
	$t++;
}
print "</table>";

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