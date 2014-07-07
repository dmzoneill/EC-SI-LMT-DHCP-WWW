<?php

class Ldap
{
	private $ldap_controllers = array( "IRSGER201.ger.corp.intel.com" , "IRSGER202.ger.corp.intel.com" , "IRSGER203.ger.corp.intel.com" );
	private $ldap_admins_ou_dn = "OU=Admin-Account,OU=Resources,DC=ger,DC=corp,DC=intel,DC=com";
	private $ldap_workers_ou_dn = "OU=Workers,DC=ger,DC=corp,DC=intel,DC=com";
	private $ldap_upn_domain = "@ger.corp.intel.com";
	private $ldap_connection = NULL;
	private $ldap_error = 0;

	private $ldap_admin_groups = array( "CN=EC GER EUEC SRV Ops,OU=Delegated,OU=Groups,DC=ger,DC=corp,DC=intel,DC=com" ,
					    "CN=SHN Lab Support and Admins,OU=Delegated,OU=Groups,DC=ger,DC=corp,DC=intel,DC=com",
	    "CN=ec ger unix euec ops,OU=Managed,OU=Groups,OU=Engineering Computing,OU=Resources,DC=ger,DC=corp,DC=intel,DC=com" );
	
	private $search_attributes = null;
	private $user_obj = null;
	private $user_properties = null;

	public function __construct()
	{
		session_start();
	}

	private function connect()
	{
		$connected = false;
		$count = 0;

		while( $connected == false && $count < count( $this->ldap_controllers ) )
		{
			$this->ldap_connection = ldap_connect( $this->ldap_controllers[ $count ] );

	                if( !$this->ldap_connection )
        	        {
				$this->ldap_error = "Unable to login... The server said : " . ldap_error( $this->ldap_connection );
                        	$count++;             
                	}
			else
			{
				$connected = true;
				$count = count( $this->ldap_controllers ) + 1;
			}
		}

		if( $connected )
		{
                	ldap_set_option( $this->ldap_connection , LDAP_OPT_PROTOCOL_VERSION , 3 );
	                ldap_set_option( $this->ldap_connection , LDAP_OPT_REFERRALS , 0 );
		}
		
		return $connected;
	}
	
	public function login( $user , $pass )
	{
		if( $this->connect() == true )
		{
			if( ldap_bind( $this->ldap_connection , $user . $this->ldap_upn_domain , $pass ) === TRUE )
			{
				$ou = ( substr( $user , 0 , 3 ) == "ad_" ) ? $this->ldap_admins_ou_dn : $this->ldap_workers_ou_dn;
				
				$this->search_attributes = array( "*" );	
				$filter = "(sAMAccountName=$user)";
				$this->user_obj = ldap_search( $this->ldap_connection , $ou , $filter , $this->search_attributes );
				$this->user_properties = ldap_get_entries( $this->ldap_connection , $this->user_obj );		
					
				$_SESSION[ 'loggedin' ] = true;
			
				if( $this->isinadmingroup() )
				{
					$_SESSION[ 'type' ] = "admin";
				}
				else
				{
					$_SESSION[ 'type' ] = "user";
				}
			
				$_SESSION[ 'username' ] = $user;
				$this->setupn();
			
				ldap_unbind( $this->ldap_connection );
			}
			else
			{
				$_SESSION[ 'loggedin' ] = false;
				$this->error = "Unable to login... The server said : " . ldap_error( $this->ldap_connection );
			}	
		}
	}

	private function isinadmingroup()
	{
		foreach( $this->user_properties[0]['memberof'] as $group )
		{
			if( in_array( $group , $this->ldap_admin_groups ) )
			{
				return true;
			}
		}	
		
		return false;
	}

	public function getuser()
	{
        	return $_SESSION[ 'username' ];
	}
	
	public function logout()
	{
		$_SESSION[ 'loggedin' ] = false;
	}

	public function geterror()
	{
		return $this->ldap_error;
	}
	
	public function isloggedin()
	{
		return isset( $_SESSION[ 'loggedin' ] ) ? $_SESSION[ 'loggedin' ] : false;
	}

	public function getlogintype()
	{
		return $_SESSION[ 'type' ];
	}

	private function setupn()
	{
		$_SESSION['upn'] = $this->user_properties[0]['userprincipalname'][0];
	}

	public function getupn()
	{
		return $_SESSION['upn'];
	}	

	public function debug()
	{
		print "<pre>";
		print_r( $this->user_properties );
		print "</pre>";
	}	
}
