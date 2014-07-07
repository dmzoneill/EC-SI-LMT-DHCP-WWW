<?php

include( "intel_header.php" );

if( isset( $_POST[ 'rules' ] ) ) 
{
	print "Sucessfully Edited<br>";
	file_put_contents( "in.txt" , $_POST[ 'rules' ] );
}

if( isset( $_GET[ 'edit' ] ) )
{
	$rules = file_get_contents( "in.txt" );

	print "Editing Rules:<br>Format: host[,host] | [transport:]port,[[transport:]port]<br>";
	print "<form action='index.php' method='post'>";
	print "<textarea cols=180 rows=40 wrap=off name='rules'>$rules</textarea><br><br>";
	print "<input type='submit' value='Apply'></form>";
}
else
{
        $lines = array_reverse( file( "out.txt" ) );

        echo "[ <a href='index.php?edit=true'>edit scan</a> ] Scan finished: " . date ( "D H:i:s.", filemtime( "out.txt" ) );

        print "<br><table border=0 cellspacing=0 cellpadding=1 align=left width=800>\n";

        $last = "";

        foreach( $lines as $line )
        {
                $aline = explode( "#" , $line );
                $bgcolor = ( trim( $aline[ 3 ] ) == "2" ) ? "#BB0000" : "#00BB00";
                $bgcolor = ( trim( $aline[ 3 ] ) == "4" ) ? "#FF6600" : $bgcolor;
                $status = ( trim( $aline[ 3 ] ) == "2" ) ? "Filtered" : "Open";
                $status = ( trim( $aline[ 3 ] ) == "4" ) ? "Filtered/Open" : $status;


                if( $last != $aline[ 2 ] )
                {
                        print "<tr><td colspan='3' align='left'>";
                        $parts = explode( "|" , $aline[ 2 ] );
                        print "<hr><b>Destinations:</b> " . $parts[ 0 ] . "<br>";
                        print "<b>Ports:</b> " . $parts[ 1 ] . "<br><hr>";
                        print "</td></tr>";
                        $last = $aline[ 2 ];
                        print "<tr><td width='90'><b>Status</b></td><td width='100'><b>Destination</b></td><td width='610'><b>Port</b></td></tr>\n";
                }

                print "<tr><td width='90' align='left'><font color='$bgcolor'>$status</font></td><td width='100'>" . $aline[0] ."</td><td widht='610'>" . $aline[1] . "</td></tr>\n";
        }

        print "</table>\n";


}

include( "intel_footer.php" );
