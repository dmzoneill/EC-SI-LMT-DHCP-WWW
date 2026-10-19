<?php

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/leases.class.php';
require_once __DIR__ . '/includes/dhcpd_hosts.php';

include "intel_header.php";

$lines = file("/etc/dhcpd.conf");

foreach (dhcpdFixedHosts($lines) as $host) {
    $ixa = explode("-", $host["nameLine"]);
    $ixa = $ixa[1];
    $mac = $host["mac"];
    $ip = $host["ip"];

    if (validIP($ip) && validMAC($mac)) {
        $valid_hosts[] = array($mac, $ip, $ixa);
    }
}

$test = new \Intel\Lmt\Leases();
$test->readLease("/var/lib/dhcpd/dhcpd.leases");

print "<table>";

if ($_POST['lease']) {
    $duplicates = array();

    $found = false;
    while ($lease = $test->nextLease()) {
        $leaseIp = "";
        $leaseMac = "";
        if (isset($lease['ip_addr'])) {
            $leaseIp = $lease['ip_addr'];
        }
        if (isset($lease['mac'])) {
            $leaseMac = $lease['mac'];
        }
        $lease['ip_addr'] = $leaseIp;
        $lease['mac'] = $leaseMac;
        if (stristr($lease['ip_addr'], $_POST['lease']) || stristr($lease['mac'], $_POST['lease'])) {
            if (!in_array($lease['ip_addr'], $duplicates)) {
                $duplicates[] = $lease['ip_addr'];
                print "<tr><td></td><td class='padme'>" . $lease['mac'] . "</td><td class='padme'>"
                    . $lease['ip_addr'] . "</td></tr>";
                $found = true;
            }
        }
    }

    if (isset($valid_hosts)) {
        for ($t = 0; $t < count($valid_hosts); $t++) {
            if (
                stristr($valid_hosts[$t][0], $_POST['lease'])
                || stristr($valid_hosts[$t][1], $_POST['lease'])
                || stristr($valid_hosts[$t][2], $_POST['lease'])
            ) {
                print "<tr><td class='padme'>" . $valid_hosts[$t][2] . "</td><td class='padme'>"
                    . $valid_hosts[$t][0] . "</td><td class='padme'>" . $valid_hosts[$t][1] . "</td></tr>";
                $found = true;
            }
        }
    }

    if ($found == false) {
        print "No IP lease found matching " . $_POST['lease'];
    }
}

print "</table>";

include "intel_footer.php";
