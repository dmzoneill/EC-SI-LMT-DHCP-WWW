<?php

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/leases.class.php';
require_once __DIR__ . '/includes/dhcpd_hosts.php';

include "intel_header.php";

$lines = file("/etc/dhcpd.conf");
if ($lines === false) {
    $lines = array();
}

$valid_hosts = array();
foreach (dhcpdFixedHosts($lines) as $host) {
    $ixa = explode("-", $host["nameLine"]);
    $ixa = $ixa[1];
    $mac = $host["mac"];
    $ip = $host["ip"];

    if (validIP($ip) !== false && validMAC($mac) !== false) {
        $valid_hosts[] = array($mac, $ip, $ixa);
    }
}

$test = new \Intel\Lmt\Leases();
$test->readLease("/var/lib/dhcpd/dhcpd.leases");

print "<table>";

$leaseQuery = $_POST['lease'];
if (is_string($leaseQuery) && $leaseQuery !== "" && $leaseQuery !== "0") {
    $duplicates = array();

    $found = false;
    while ($lease = $test->nextLease()) {
        $leaseIp = "";
        $leaseMac = "";
        if (is_array($lease) && isset($lease['ip_addr']) && is_string($lease['ip_addr'])) {
            $leaseIp = $lease['ip_addr'];
        }
        if (is_array($lease) && isset($lease['mac']) && is_string($lease['mac'])) {
            $leaseMac = $lease['mac'];
        }
        if (searchMatches($leaseIp, $leaseQuery) || searchMatches($leaseMac, $leaseQuery)) {
            if (!in_array($leaseIp, $duplicates)) {
                $duplicates[] = $leaseIp;
                print "<tr><td></td><td class='padme'>" . $leaseMac . "</td><td class='padme'>"
                    . $leaseIp . "</td></tr>";
                $found = true;
            }
        }
    }

    for ($t = 0; $t < count($valid_hosts); $t++) {
        if (
            searchMatches($valid_hosts[$t][0], $leaseQuery)
            || searchMatches($valid_hosts[$t][1], $leaseQuery)
            || searchMatches($valid_hosts[$t][2], $leaseQuery)
        ) {
            print "<tr><td class='padme'>" . $valid_hosts[$t][2] . "</td><td class='padme'>"
                . $valid_hosts[$t][0] . "</td><td class='padme'>" . $valid_hosts[$t][1] . "</td></tr>";
            $found = true;
        }
    }

    if ($found == false) {
        print "No IP lease found matching " . $leaseQuery;
    }
}

print "</table>";

include "intel_footer.php";
