<?php

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/includes/dhcpd_hosts.php';

include "intel_header.php";

$lines = file("/etc/dhcpd.conf");

foreach (dhcpdFixedHosts($lines) as $host) {
    $mac = $host["mac"];
    $ip = $host["ip"];

    if (validIP($ip) && validMAC($mac)) {
        $valid_hosts[$mac] = $ip;
    } else {
        $invalid_hosts[$mac] = $ip;
    }
}

if (isset($valid_hosts)) {
    natsort($valid_hosts);
}
$invalid_hosts = isset($invalid_hosts) ? $invalid_hosts : array();
natsort($invalid_hosts);

if (count($invalid_hosts) > 0) {
    print "<h2>InValid Static Leases</h2>";
    print "<table>";
    print "<tr><td>MAC</td><td>IP</td><td>PING</td></tr>";
    foreach ($invalid_hosts as $key => $value) {
        print "<tr><td class='padme'>" . $key . "</td><td class='padme'>" . $value
            . "</td><td class='padme'></td></tr>";
    }
    print "</table>";
}

if (isset($valid_hosts)) {
    $t = 1;
    print "<h2>Static Leases</h2>";
    printLeasePingHeader();

    foreach ($valid_hosts as $key => $value) {
        print "<tr><td class='padme'><b>$t</b></td><td id='m$t' class='padme'>" . $key
            . "</td><td id='i$t' class='padme'>" . $value . "</td><td id='p$t' class='padme'></td></tr>";
        $t++;
    }
    print "</table>";
}

if (!isset($t)) {
    $t = null;
}
printPingHostScript($t);

include "intel_footer.php";
