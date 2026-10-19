<?php

namespace Intel\Lmt;

class Leases
{
    /** @var array<int, array<string, mixed>>|null */
    public $leases;

    /** @var int|null */
    public $leaseIterator;

    /**
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @param string $filename
     * @return void
     */
    public function parseConfig($filename)
    {
    }

    /**
     * @return void
     */
    public function rewindLease()
    {
    }

    /**
     * @return mixed
     */
    public function getLease()
    {
     /* Will return the lease array for the lease of the index that the
        iterator is currently on.  */
        if ($this->leaseIterator == -1) {
            $this->leaseIterator++;
        }

        return $this->leases[$this->leaseIterator];
    }


    /**
     * @return mixed
     */
    public function nextActive()
    {
     /* Will retrieve the next value in the $leases array that contains
         an active lease. If the last active lease has already been
         returned by a previous call, the returned value will be false.

         Pre: Leasefile must have already been processed by readLease().
         Post: Returns an associative array containing all properties of
           the active lease.
                                                            */
        while ($lease = $this->nextLease()) {
            if ($lease["status"] === "active") {
                return $lease;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function nextLease()
    {
     /* Will retrieve the next lease in the $leases array. Acts as
        an array iterator.  If the iterator is set to the last index when
        called, false is returned.

        Pre: Leasefile must have been processed by readLease().
        Post: Returns an associative array containing the properties of
          the next lease */

        if ($this->leaseIterator < count($this->leases)) {
            return isset($this->leases[++$this->leaseIterator]) ? $this->leases[++$this->leaseIterator] : "";
        } else {
            return false;
        }
    }


    /**
     * @param string $filename
     * @return void
     */
    public function readLease($filename)
    {
     /* Opens a lease file, splits it into individual leases,
         then stores the results of each lease into the $leases array.

         Pre: Requires a valid dhcpd.leases file as parameter
         Post: $leases contains an array of all leases in file
                                                             */

        if (!($leaseFile = fopen($filename, "r"))) {
            print("ERROR: ");
            print("'$filename' could not be opened for reading\n");
            exit;
        }

        $entry = array();
        while (!feof($leaseFile)) {
            $line = fgets($leaseFile, 1024);
            if (!is_string($line)) {
                continue;
            }

            // Skips lines that don't matter
            if (preg_match("/^(\#)/", $line)) {
                continue;
            }
            if (preg_match("/^(})/", $line)) {
                $this->leases[] = $this->parseLease($entry);
                $entry = array();
                continue;
            }

            $entry[] = $line;
        } // End of while

        fclose($leaseFile);

        return;
    }

    /**
     * @param array<int, string> $rgLease
     * @return array<string, mixed>
     */
    public function parseLease($rgLease)
    {
        $lease = array();
        /* Parses the one lease statement.

       Pre: Input should be an array containing each line of lease definition
         starting with lease statement, ending with line before closing '}'
       Post: Output should be an associative array with each key containing the
         keyword in each line of the lease statement.
                                                                   */
        foreach ($rgLease as $line) {
            // Skips lines that don't matter
            if (preg_match("/^(\#|\})/x", $line)) {
                continue;
            }

            if (
                preg_match(
                    "/^lease\s+(?!0)((?:(?:[01]?\d\d?|2[0-4]\d|25[0-5])\.){3}"
                    . "(?:[01]?\d\d?|2[0-4]\d|25[0-5]))\s+\{/x",
                    $line,
                    $matches
                )
            ) {
                $lease["ip_addr"] = $matches[1];
                continue;
            }

            // Split the keywords from the values
            preg_match("/^\s+([a-z -]+[a-z-]+)\s*\"?(.+?)?\"?\;/x", $line, $matches);
            $key = isset($matches[1]) ? $matches[1] : "";
            $value = isset($matches[2]) ? $matches[2] : "";

            if ($key === "hardware ethernet") {
                $key = "mac";
                $value = strtoupper($value);
            } elseif ($key === "ends never") {
                $lease["ends"] = "never";
                continue;
            } elseif ($key === "starts" || $key === "ends") {
                // Need to trim the beginning to put date into YYYY/MM/DD HH:MM:SS format
                $value = strtotime(substr($value, 2));

                if ($key === "ends" && $value <= time()) {
                    $lease["status"] = "inactive";
                } else {
                    $lease["status"] = "active";
                }
            } elseif ($key === "client-hostname") {
                $key = "hostname";
            }

            $lease[$key] = $value;
        } // end of foreach

        return $lease;
    }
}
