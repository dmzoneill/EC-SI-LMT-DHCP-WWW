<?php

include("intel_header.php");

if (!isset($filename)) {
    $filename = "";
}
if (!isset($syncdata)) {
    $syncdata = array();
    for ($syncIndex = 0; $syncIndex < 7; $syncIndex++) {
        $syncdata[$syncIndex] = array("", 0, 0, 0, 0, 0);
    }
}



require_once __DIR__ . '/includes/lab_tables.php';

print "<div id='container' style='width:900px;height:350px' align='center'></div><br/>";
print "<br/><div align='center'><span class='padme'>"
    . "<a href=\"javascript:showHide( 'dc' )\"  style='font-size:12pt'> Data Center </a>  </span>";
print "<span class='padme'><a href=\"javascript:showHide( 'sw' )\" style='font-size:12pt'> Software </a>"
    . "  </span>";
print "<span class='padme'><a href=\"javascript:showHide( 'sv' )\" style='font-size:12pt'> "
    . "Silicon Validation </a>  </span>";
print "<span class='padme'><a href=\"javascript:showHide( 'rr' )\" style='font-size:12pt'> Rack Room </a>  </span>";
print "<span class='padme'><a href=\"javascript:showHide( 'rr2' )\" style='font-size:12pt'> Rack Room 2 </a>  </span>";
print "<span class='padme'><a href=\"javascript:showHide( 'sr' )\" style='font-size:12pt'> Server Rack </a>"
    . "</span></div><hr><br/>";

if (isset($_GET['opt'])) {
    if ($_GET['opt'] == "lmtlog") {
        $filename = "/scripts/log/LMTdhcpLog.txt";
        $handle = fopen($filename, "rb");
        $contents = "";
        if ($handle === false) {
            print "Couldn't open file";
        } else {
            $size = filesize($filename);
            if ($size !== false) {
                $read = fread($handle, $size);
                if (is_string($read)) {
                    $contents = $read;
                }
            }
            fclose($handle);
        }

        $lab_index = -1;

        $syncdata = array();
        for ($syncIndex = 0; $syncIndex < 7; $syncIndex++) {
            $syncdata[$syncIndex] = array("", 0, 0, 0, 0, 0);
        }

        $contents = explode("\n", $contents);


        $lab_index++;
        $syncdata[0][0] = "Data Center";
        print "<div id='dcdiv'>";
        print "<h2>Data Center Assets</h2>";
        print getAssetTable($lab_index, $syncdata, $contents, "/boards/boards.DataCenter");
        print "<br /><h2>Data Center Asset Errors</h2>";
        print getAssetErrorTable($lab_index, $syncdata, $contents, "/boards/boards.DataCenter");
        print "<br /><h2>Data Center CRBS</h2>";
        print getCrbTable($lab_index, $syncdata, $contents, "/boards/boards.DataCenter");
        print "<br /><h2>Data Center CRB Errors</h2>";
        print getCrbErrorTable($lab_index, $syncdata, $contents, "/boards/boards.DataCenter");
        print "<br /><h2>Data Center Asset Reservations</h2>";
        print getassetReservationTable($lab_index, $syncdata, $contents, "/boards/boards.DataCenter");
        print "<br /><h2>Data Center Asset Reservation Errors</h2>";
        print getassetReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.DataCenter");
        print "<br /><h2>Data Center CRB Reservations</h2>";
        print getcrbReservationTable($lab_index, $syncdata, $contents, "/boards/boards.DataCenter");
        print "<br /><h2>Data Center CRB Reservation Errors</h2>";
        print getcrbReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.DataCenter");
        print "</div>";

        $lab_index++;
        $syncdata[1][0] = "Software";
        print "<div id='swdiv'>";
        print "<h2>Software Assets</h2>";
        print getAssetTable($lab_index, $syncdata, $contents, "/boards/boards.SW");
        print "<h2>Software Asset Errors</h2>";
        print getAssetErrorTable($lab_index, $syncdata, $contents, "/boards/boards.SW");
        print "<h2>Software CRBS</h2>";
        print getCrbTable($lab_index, $syncdata, $contents, "/boards/boards.SW");
        print "<h2>Software CRB Errors</h2>";
        print getCrbErrorTable($lab_index, $syncdata, $contents, "/boards/boards.SW");
        print "<br /><h2>Software Asset Reservations</h2>";
        print getassetReservationTable($lab_index, $syncdata, $contents, "/boards/boards.SW");
        print "<br /><h2>Software Asset Reservation Errors</h2>";
        print getassetReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.SW");
        print "<br /><h2>Software CRB Reservations</h2>";
        print getcrbReservationTable($lab_index, $syncdata, $contents, "/boards/boards.SW");
        print "<br /><h2>Software CRB Reservation Errors</h2>";
        print getcrbReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.SW");
        print "</div>";

        $lab_index++;
        $syncdata[2][0] = "Silicon Validation";
        print "<div id='svdiv'>";
        print "<h2>Silicon Validation Assets</h2>";
        print getAssetTable($lab_index, $syncdata, $contents, "/boards/boards.SV");
        print "<br /><h2>Silicon Validation Asset Errors</h2>";
        print getAssetErrorTable($lab_index, $syncdata, $contents, "/boards/boards.SV");
        print "<br /><h2>Silicon Validation CRBS</h2>";
        print getCrbTable($lab_index, $syncdata, $contents, "/boards/boards.SV");
        print "<br /><h2>Silicon Validation CRB Errors</h2>";
        print getCrbErrorTable($lab_index, $syncdata, $contents, "/boards/boards.SV");
        print "<br /><h2>Silicon Validation Asset Reservations</h2>";
        print getassetReservationTable($lab_index, $syncdata, $contents, "/boards/boards.SV");
        print "<br /><h2>Silicon Validation Asset Reservation Errors</h2>";
        print getassetReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.SV");
        print "<br /><h2>Silicon Validation CRB Reservations</h2>";
        print getcrbReservationTable($lab_index, $syncdata, $contents, "/boards/boards.SV");
        print "<br /><h2>Silicon Validation CRB Reservation Errors</h2>";
        print getcrbReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.SV");
        print "</div>";

        $lab_index++;
        $syncdata[3][0] = "RackRoom";
        print "<div id='rrdiv'>";
        print "<h2>RackRoom Assets</h2>";
        print getAssetTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom");
        print "<br /><h2>RackRoom Asset Errors</h2>";
        print getAssetErrorTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom");
        print "<br /><h2>RackRoom CRBS</h2>";
        print getCrbTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom");
        print "<br /><h2>RackRoom CRB Errors</h2>";
        print getCrbErrorTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom");
        print "<br /><h2>RackRoom Asset Reservations</h2>";
        print getassetReservationTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom");
        print "<br /><h2>RackRoom Asset Reservation Errors</h2>";
        print getassetReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom");
        print "<br /><h2>RackRoom CRB Reservations</h2>";
        print getcrbReservationTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom");
        print "<br /><h2>RackRoom CRB Reservation Errors</h2>";
        print getcrbReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom");
        print "</div>";

        $lab_index++;
        $syncdata[4][0] = "RackRoom2";
        print "<div id='rr2div'>";
        print "<h2>RackRoom Assets</h2>";
        print getAssetTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom2");
        print "<br /><h2>RackRoom Asset Errors</h2>";
        print getAssetErrorTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom2");
        print "<br /><h2>RackRoom CRBS</h2>";
        print getCrbTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom2");
        print "<br /><h2>RackRoom CRB Errors</h2>";
        print getCrbErrorTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom2");
        print "<br /><h2>RackRoom Asset Reservations</h2>";
        print getassetReservationTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom2");
        print "<br /><h2>RackRoom Asset Reservation Errors</h2>";
        print getassetReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom2");
        print "<br /><h2>RackRoom CRB Reservations</h2>";
        print getcrbReservationTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom2");
        print "<br /><h2>RackRoom CRB Reservation Errors</h2>";
        print getcrbReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.RackRoom2");
        print "</div>";

        $lab_index++;
        $syncdata[5][0] = "ServerRack";
        print "<div id='srdiv'>";
        print "<h2>ServerRack Assets</h2>";
        print getAssetTable($lab_index, $syncdata, $contents, "/boards/boards.ServerRack");
        print "<br /><h2>ServerRack Asset Errors</h2>";
        print getAssetErrorTable($lab_index, $syncdata, $contents, "/boards/boards.ServerRack");
        print "<br /><h2>ServerRack CRBS</h2>";
        print getCrbTable($lab_index, $syncdata, $contents, "/boards/boards.ServerRack");
        print "<br /><h2>ServerRack CRB Errors</h2>";
        print getCrbErrorTable($lab_index, $syncdata, $contents, "/boards/boards.ServerRack");
        print "<br /><h2>ServerRack Asset Reservations</h2>";
        print getassetReservationTable($lab_index, $syncdata, $contents, "/boards/boards.ServerRack");
        print "<br /><h2>ServerRack Asset Reservation Errors</h2>";
        print getassetReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.ServerRack");
        print "<br /><h2>ServerRack CRB Reservations</h2>";
        print getcrbReservationTable($lab_index, $syncdata, $contents, "/boards/boards.ServerRack");
        print "<br /><h2>ServerRack CRB Reservation Errors</h2>";
        print getcrbReservationErrorTable($lab_index, $syncdata, $contents, "/boards/boards.ServerRack");
        print "</div>";
    }
}

?>

<script type='text/javascript'>

function showHide( div )
{
    var rooms = new Array( "dc" , "sw" , "sv" , "rr" , "rr2" , "sr" );

    for( var g = 0; g < rooms.length; g++ )
    {       
        if( rooms[ g ] != div )
        {
            $( "#" + rooms[ g ] + "div" ).hide();
        }
        else
        {
            $( "#" + rooms[ g ] + "div" ).show();
        }       
    }
}

var chart;
$(document).ready(function() {
   chart = new Highcharts.Chart({
      chart: {
         renderTo: 'container',
         defaultSeriesType: 'column',
         margin: [50, 0, 70, 30]
      },
      title: {
         text: 'LMT DHCP Sync Log statistics'
      },
      subtitle: {
         text: 'Source: /scripts/log/LMTdhcpLog.txt - ' + '<?php
            $logMtime = filemtime($filename);
            echo date("F d Y H:i:s.", is_int($logMtime) ? $logMtime : 0);
            ?>'
      },
      xAxis: {
         categories: [
            '<?php print $syncdata[0][0]; ?>', 
            '<?php print $syncdata[1][0]; ?>', 
            '<?php print $syncdata[2][0]; ?>', 
            '<?php print $syncdata[3][0]; ?>', 
            '<?php print $syncdata[4][0]; ?>',
            '<?php print $syncdata[5][0]; ?>',
            '<?php print $syncdata[6][0]; ?>'
         ]
      },
      yAxis: {
         min: 0,
         title: {
            text: 'entries'
         }
      },
      tooltip: {
         formatter: function() {
            return ''+ this.y;
         }
      },
      plotOptions: {
         column: {
            pointPadding: 0.2,
            borderWidth: 0
         }
      },
           series: [{
         name: 'Entry',
         data: [ <?php print $syncdata[0][1]; ?> ,
            <?php print $syncdata[1][1]; ?> , <?php print $syncdata[2][1]; ?> ,
            <?php print $syncdata[3][1]; ?> , <?php print $syncdata[4][1]; ?> ,
            <?php print $syncdata[5][1]; ?> ]
   
      }, {
         name: 'Valid Mac',
         data: [ <?php print $syncdata[0][2]; ?> ,
            <?php print $syncdata[1][2]; ?> , <?php print $syncdata[2][2]; ?> ,
            <?php print $syncdata[3][2]; ?> , <?php print $syncdata[4][2]; ?> ,
            <?php print $syncdata[5][2]; ?>  ]
   
      }, {
         name: 'Invalid MAC',
         data: [ <?php print $syncdata[0][3]; ?> ,
            <?php print $syncdata[1][3]; ?> , <?php print $syncdata[2][3]; ?> ,
            <?php print $syncdata[3][3]; ?> , <?php print $syncdata[4][3]; ?> ,
            <?php print $syncdata[5][3]; ?>  ]
   
      }, {
         name: 'Invalid IP',
         data: [ <?php print $syncdata[0][4]; ?> ,
            <?php print $syncdata[1][4]; ?> , <?php print $syncdata[2][4]; ?> ,
            <?php print $syncdata[3][4]; ?> , <?php print $syncdata[4][4]; ?> ,
            <?php print $syncdata[5][4]; ?>  ]
   
      }, {
         name: 'Wrong Vlan',
         data: [ <?php print $syncdata[0][5]; ?> ,
            <?php print $syncdata[1][5]; ?> , <?php print $syncdata[2][5]; ?> ,
            <?php print $syncdata[3][5]; ?> , <?php print $syncdata[4][5]; ?> ,
            <?php print $syncdata[5][5]; ?>  ]
   
      }]
   });
    
    showHide( 'dc' );
});




</script>

<?php

include("intel_footer.php");

?>