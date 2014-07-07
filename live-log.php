<?php

if( isset( $_GET[ 'log' ] ) && isset( $_GET[ 'lines' ] ) )
{

	$lines = ( $_GET[ 'lines' ] == "1" || $_GET[ 'lines' ] == "2" || $_GET[ 'lines' ] == "3" ) ? ($_GET[ 'lines' ] * 10) : 10;
	
	if ( $_GET[ 'log' ] == "1" )
	{
		print shell_exec( "tail -n $lines /var/log/dhcpd.log" );
	}
	
	exit;
}
else
{
	include( "intel_header.php" );
	
	?>
	<table width='100%'>
		<tr>
			<td>
				Log File : <select id='logfile'><option value='1'>dhcpd.log</option></select> ||| 
				Fetch <select id='loglines'><option value='1'>10</option><option value='2'>20</option><option value='3'>30</option></select> Lines |||  
				History <select id='historylines'><option value='1'>100</option><option value='5'>500</option><option value='10'>1000</option></select>
			</td>
			<td align='right'>
				<span id='countdown'></span>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<textarea style='width:100%;height:450px' id='logviewer'></textarea>
			</td>
		</tr>
		
	</table>
	<br/>	
	
	<script type='text/javascript'>
			
		$( document ).ready( function()
		{
			init();
		});
		
	</script>
	
	<?php
	
	include( "intel_footer.php" );
}

?>
