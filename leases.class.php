<?php

class Leases
{
	var $leases;
	var $leaseIterator;
	
	function Leases()
	{
		$leaseIterator = -1; // To ensure you don't miss the 0 index
	}
	
	
	function parseConfig($filename)
	{
	}
	
	/* BEGIN LEASE METHODS */
	function rewindLease()
	{
		$this->Leases();
	}
	
	function getLease()
	{
	 /* Will return the lease array for the lease of the index that the
	    iterator is currently on.  */
		if($this->leaseIterator == -1)
			$this->leaseIterator++;
			
		return $this->leases[$this->leaseIterator];
	
	}
	
	
	function nextActive()
	{ /* Will retrieve the next value in the $leases array that contains
	     an active lease. If the last active lease has already been
		 returned by a previous call, the returned value will be false.
		 
		 Pre: Leasefile must have already been processed by readLease().
		 Post: Returns an associative array containing all properties of
		       the active lease.
			   															*/
		while($lease = $this->nextLease())
		{
			if($lease["status"] === "active")
				return $lease;
		}
		
		return false;
	
	}
	
	function nextLease()
	{ /* Will retrieve the next lease in the $leases array. Acts as
		an array iterator.  If the iterator is set to the last index when
		called, false is returned.
		
		Pre: Leasefile must have been processed by readLease().
		Post: Returns an associative array containing the properties of
			  the next lease */
			  															
		if($this->leaseIterator < count($this->leases))
			return isset( $this->leases[++$this->leaseIterator] ) ? $this->leases[++$this->leaseIterator] : "";
		else
			return false;
	}
	
	
	function readLease($filename)
	{ /* Opens a lease file, splits it into individual leases,
		 then stores the results of each lease into the $leases array.
		 
		 Pre: Requires a valid dhcpd.leases file as parameter
		 Post: $leases contains an array of all leases in file
	                                                                     */
	
		if(!($leaseFile = fopen($filename, "r")))
		{
			print("ERROR: ");
			print("'$filename' could not be opened for reading\n");
			exit;
		}
		
		while(!feof($leaseFile))
		{
			$line = fgets($leaseFile, 1024);
			
			// Skips lines that don't matter
			if(preg_match("/^(\#)/", $line))
			{
				continue;
			}
			if(preg_match("/^(})/", $line))
			{
				$this->leases[] = $this->parseLease($entry);
				unset($entry);
				continue;
			}
			
			$entry[] = $line;
	
		} // End of while
		
		fclose($leaseFile);
		
		return;
	}
	
	function parseLease($rgLease)
	{ /* Parses the one lease statement.
	
	   Pre: Input should be an array containing each line of lease definition
			 starting with lease statement, ending with line before closing '}'
	   Post: Output should be an associative array with each key containing the
			 keyword in each line of the lease statement.
																			   */
		foreach($rgLease as $line){
			
			// Skips lines that don't matter
			if(preg_match("/^(\#|\})/x", $line))
			{
				continue;
			}
			
			if(preg_match("/^lease\s+(?!0)((?:(?:[01]?\d\d?|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d\d?|2[0-4]\d|25[0-5]))\s+\{/x",
				$line, $matches))
			{
				$lease["ip_addr"] = $matches[1];
				continue;
			}
			
			// Split the keywords from the values
			preg_match("/^\s+([a-z -]+[a-z-]+)\s*\"?(.+?)?\"?\;/x", $line, $matches);
			$key = isset($matches[1]) ? $matches[1] : "";
			$value = isset($matches[2]) ? $matches[2] : "";
			
			if($key === "hardware ethernet")
			{
				$key = "mac";
				$value = strtoupper($value);
			}
			else if($key === "ends never")
			{
				$lease["ends"] = "never";
				continue;
			}
			else if ($key === "starts" || $key === "ends")
			{
				// Need to trim the begining to put date into YYYY/MM/DD HH:MM:SS format
				$value = strtotime(substr($value,2));
			
				if($key === "ends" && $value <= time())
					$lease["status"] = "inactive";
				else
					$lease["status"] = "active";
			}
			else if ($key === "client-hostname")
				$key = "hostname";
			
			if(isset($key) && isset($value))
				$lease[$key] = $value;
		} // end of foreach
		
		return $lease;
	} 	
} 