<?php

if (!function_exists('validIP')) {
    /**
     * @param string $haystack
     * @param string $needle
     */
    function searchMatches($haystack, $needle): bool
    {
        $hit = stristr($haystack, $needle);

        return $hit !== false && $hit !== "0";
    }

    /**
     * @param mixed $ip
     * @return false|string
     */
    function validIP($ip)
    {
        if (!is_string($ip)) {
            return false;
        }

        $pattern = "^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])"
            . "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}^";
        if (preg_match($pattern, $ip, $match)) {
            return $match[0];
        }

        return false;
    }

    /**
     * @param mixed $mac
     * @return false|string
     */
    function validMAC($mac)
    {
        if (!is_string($mac)) {
            return false;
        }

        $pattern = "/[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}/i";
        if (preg_match($pattern, $mac, $match)) {
            return $match[0];
        }

        return false;
    }

    /**
     * @param mixed $host
     * @param mixed $timeout
     * @return int|string
     */
    function ping($host, $timeout)
    {
        if (!is_scalar($host) || !is_scalar($timeout)) {
            return -1;
        }

        $command = "/usr/bin/nmap -PS21,22,23,25,67,80,113,3389 -sP -PE -PP -PM "
            . (string) $host . " --host-timeout " . (string) $timeout;
        $pingdata = commandStdout($command);

        if (is_string($pingdata) && stristr($pingdata, "1 host up") !== false) {
            $timedata = explode("scanned in", $pingdata);
            $ms = trim($timedata[1]);
            return $ms;
        }

        return -1;
    }

    /**
     * stdout of a shell command, matching the previous shell_exec result.
     *
     * @param string $command
     * @return string|null
     */
    function commandStdout($command)
    {
        $pipes = array();
        $process = proc_open(
            $command,
            array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("pipe", "w"),
            ),
            $pipes
        );
        if (!is_resource($process)) {
            return null;
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        if (!is_string($stdout)) {
            return null;
        }

        return $stdout;
    }

    /**
     * @return array<string, string>
     */
    function readFreeIpsLog()
    {
        $ips = array();
        $freeipslog = __DIR__ . "/logs/freeips.txt";
        $fp = fopen($freeipslog, 'r');

        if ($fp !== false) {
            $size = filesize($freeipslog);
            $data = "";
            if ($size !== false) {
                $read = fread($fp, $size);
                if (is_string($read)) {
                    $data = $read;
                }
            }
            fclose($fp);

            $vlans = explode("|", $data);
            for ($x = 0; $x < count($vlans) - 1; $x++) {
                $free = explode(",", $vlans[$x]);
                $ips[$free[0]] = $free[1];
            }
        }

        return $ips;
    }
}
