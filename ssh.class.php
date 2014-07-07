<?php

class ssh2 
{

	private $host = 'host';
	private $user = 'user';
	private $port = '22';
	private $password = 'password';
	private $con = null;
	private $shell_type = 'xterm';
	private $shell = null;
	private $log = '';
	private $keys;
	
	function __construct($host='', $port=''  ) 
	{
		if( $host!='' ) $this->host  = $host;
		if( $port!='' ) $this->port  = $port;

	        $this->keys = array();
	        $this->keys[] = array( "/root/.ssh/gaap1" , "/root/.ssh/gaap1.pub" );
        	$this->keys[] = array( "/root/.ssh/gaap2" , "/root/.ssh/gaap2.pub" );
	        $this->keys[] = array( "/root/.ssh/sisutil001" , "/root/.ssh/sisutil001.pub" );

		$this->con = ssh2_connect($this->host, $this->port, array('hostkey'=>'ssh-dss,ssh-rsa'));

		if( !$this->con ) 
		{
			$this->log .= "Connection failed !"; 
		}
	}

	function authPassword( $user = '', $password = '' ) 
	{
		if( $user!='' ) $this->user = $user;
		if( $password!='' ) $this->password = $password;

		if( !ssh2_auth_password( $this->con, $this->user, $this->password ) ) 
		{
			$this->log .= "Authorization failed !"; 
		}
	}

        function authPasswordLess( $user = '' )
        {
                if( $user!='' ) $this->user = $user;

		foreach( $this->keys as $pubkey )
		{
                	if( ssh2_auth_pubkey_file( $this->con, $this->user, $pubkey[1] , $pubkey[0] ) )
                	{
				return true;
                	}
			else
			{
				print "failed\n";
			}
		}
		
		return false;	
        }

	function openShell( $shell_type = '' ) 
	{
		if ( $shell_type != '' ) $this->shell_type = $shell_type;
		$this->shell = ssh2_shell( $this->con,  $this->shell_type );
		if( !$this->shell ) $this->log .= " Shell connection failed !";
	}

	function writeShell( $command = '' ) 
	{
		fwrite($this->shell, $command."\n");
	}

	function cmdExec( $cmd ) 
	{
		$stream = ssh2_exec( $this->con, $cmd );
		stream_set_blocking( $stream, true );
		return fread( $stream, 4096 );
	}

	function getLog() 
	{
		return $this->log; 
	}

	function debug_cb($message, $language, $always_display)
	{
        	print "$message , $language , $always_display \n";
	}

}


