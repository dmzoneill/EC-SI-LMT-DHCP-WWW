<?php

/**
 * Shared dhcpd.conf host parsing and the lease-table chrome used by the
 * static and dynamic lease pages.
 */

/**
 * @param array<int, string> $lines
 * @return list<array{mac: string, ip: string, nameLine: string}>
 */
function dhcpdFixedHosts($lines)
{
    $hosts = array();
    $total = count($lines);

    for ($t = 2; $t < $total - 3; $t++) {
        $hardware = stristr($lines[$t], "hardware ethernet");
        $address = stristr($lines[$t + 1], "fixed-address");
        if ($hardware === false || $address === false) {
            continue;
        }

        $macSource = preg_replace('/hardware ethernet/', "", $lines[$t]);
        $ipSource = preg_replace('/fixed-address/', "", $lines[$t + 1]);
        if (!is_string($macSource) || !is_string($ipSource)) {
            continue;
        }

        $hosts[] = array(
            "mac" => substr(trim($macSource), 0, -1),
            "ip" => substr(trim($ipSource), 0, -1),
            "nameLine" => $lines[$t - 2],
        );
    }

    return $hosts;
}

/**
 * @return void
 */
function printLeasePingHeader()
{
    print "<table>";
    print "<tr><td class='padme'></td><td class='padme'><b>MAC</b></td>"
        . "<td class='padme'><b>IP</b></td><td class='padme'><b>PING</b></td></tr>";
    print "<tr><td colspan='4'></td></tr>";
}

/**
 * @param int|string|null $lastId
 * @return void
 */
function printPingHostScript($lastId)
{
    $idText = $lastId === null ? "" : (string) $lastId;
    echo "\n\n<script type='text/javascript' >\n\n"
        . "\t$(document).ready( function()\n"
        . "\t{\t\n"
        . "\t\tif( $( '#m1' ).length > 0 )\n"
        . "\t\t{\n"
        . "\t\t\tpinghost( 1 , " . $idText . " );\t\t\t\n"
        . "\t\t}\n"
        . "\t});\n\n"
        . "</script>\n\n";
}
