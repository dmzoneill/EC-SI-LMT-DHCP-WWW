<?php

/**
 * Shared dhcpd.conf host parsing and the lease-table chrome used by the
 * static and dynamic lease pages.
 */

function dhcpdFixedHosts($lines)
{
    $hosts = array();
    $total = count($lines);

    for ($t = 2; $t < $total - 3; $t++) {
        if (
            stristr($lines[$t], "hardware ethernet")
            && stristr($lines[$t + 1], "fixed-address")
        ) {
            $hosts[] = array(
                "mac" => substr(trim(preg_replace('/hardware ethernet/', "", $lines[$t])), 0, -1),
                "ip" => substr(trim(preg_replace('/fixed-address/', "", $lines[$t + 1])), 0, -1),
                "nameLine" => $lines[$t - 2],
            );
        }
    }

    return $hosts;
}

function printLeasePingHeader()
{
    print "<table>";
    print "<tr><td class='padme'></td><td class='padme'><b>MAC</b></td>"
        . "<td class='padme'><b>IP</b></td><td class='padme'><b>PING</b></td></tr>";
    print "<tr><td colspan='4'></td></tr>";
}

function printPingHostScript($lastId)
{
    echo "\n\n<script type='text/javascript' >\n\n"
        . "\t$(document).ready( function()\n"
        . "\t{\t\n"
        . "\t\tif( $( '#m1' ).length > 0 )\n"
        . "\t\t{\n"
        . "\t\t\tpinghost( 1 , " . $lastId . " );\t\t\t\n"
        . "\t\t}\n"
        . "\t});\n\n"
        . "</script>\n\n";
}
