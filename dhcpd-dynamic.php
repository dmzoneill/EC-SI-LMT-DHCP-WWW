<?php

require_once __DIR__ . '/includes/dhcpd_hosts.php';

include "intel_header.php";

$t = 1;

printLeasePingHeader();

for ($z = 10; $z < 41; $z++) {
    print "<tr><td class='padme'><b>$t</b></td><td id='m$t' class='padme'>.</td><td id='i$t' class='padme'>10.243.18."
        . $z . "</td><td id='p$t' class='padme'></td></tr>";
    $t++;
}
print "</table>";

printPingHostScript($t);

include "intel_footer.php";
