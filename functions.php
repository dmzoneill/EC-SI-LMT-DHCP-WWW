<?php

if (!function_exists('validIP')) {
    function validIP($ip)
    {
        $pattern = "^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])"
            . "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}^";
        if (preg_match($pattern, $ip, $match)) {
            return $match[0];
        } else {
            return false;
        }
    }

    function validMAC($mac)
    {
        $pattern = "/[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}/i";
        if (preg_match($pattern, $mac, $match)) {
            return $match[0];
        } else {
            return false;
        }
    }

    function ping($host, $timeout)
    {
        $command = "/usr/bin/nmap -PS21,22,23,25,67,80,113,3389 -sP -PE -PP -PM "
            . $host . " --host-timeout " . $timeout;
        $pingdata = shell_exec($command);

        if (stristr($pingdata, "1 host up")) {
            $timedata = explode("scanned in", $pingdata);
            $ms = trim($timedata[ 1 ]);
            return $ms;
        } else {
            return -1;
        }
    }

    function readFreeIpsLog()
    {
        $ips = array();
        $freeipslog = __DIR__ . "/logs/freeips.txt";

        if ($fp = fopen($freeipslog, 'r')) {
            $data = fread($fp, filesize($freeipslog));
            $vlans = explode("|", $data);

            for ($x = 0; $x < count($vlans) - 1; $x++) {
                $free = explode(",", $vlans[ $x ]);
                $ips[ $free[0] ] = $free[1];
            }
        }

        return $ips;
    }
}
