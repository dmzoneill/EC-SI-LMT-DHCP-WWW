<?php

	require_once( "db.php" );

	$db = DB::getinstance();
	
///////////////////////////////////////////////////////////////////////////////////////////
// AVAILABLE DRIVERS
///////////////////////////////////////////////////////////////////////////////////////////	

	print "<h2>AVAILABLE DRIVERS</h2>\n";

	$db->listdrivers();	
	

///////////////////////////////////////////////////////////////////////////////////////////
// CONNECTION EXAMPLE
///////////////////////////////////////////////////////////////////////////////////////////	

	print "<h2>MYSQL CONNECT EXAMPLE</h2>\n";

	if( $db->connect( "localhost" , "root" , "finepassword" , "computerTracking" , false ) )
	{
		/* OR
		 * $db->connectdsn( "pgsql:dbname=no_database;host=localhost", "username", "password" , false );
		 * $db->connectdsn( "sqlite:/path/to/database.sdb", "username", "password" , true );
		 * $db->connectdsn( "sqlite::memory", "username", "password" , false );
		 * $db->connectdsn( "firebird:dbname=localhost:C:\Programs\Firebird\DATABASE.FDB", "username", "password" , true );
		 * $db->connectdsn( "pgsql:dbname=no_database;host=localhost", "username", "password" , false );
		 * $db->connectdsn( "informix:DSN=InformixDB", "username", "password" , true );
		 * $db->connectdsn( "odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq=C:\accounts.mdb;Uid=Admin", "", "" , true );
		 * $db->connectdsn( "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=accounts; HOSTNAME=1.2.3,4;PORT=56789;PROTOCOL=TCPIP;", "username", "password" , true );
		 * $db->connectdsn( "dblib:host=localhost:3345;dbname=test", "username", "password" , true );
		 */
		print "<h4>Connected</h4>\n";
		
		
///////////////////////////////////////////////////////////////////////////////////////////
// INSERT EXAMPLE - RETURN NEW ID
///////////////////////////////////////////////////////////////////////////////////////////	
		
		print "<h2>INSERT EXAMPLE - RETURN NEW ID</h2>\n";
		
		//
		// always sanitize strings that go into sql queries!
		//	
		
		$user = $db->clean( "aDirtyUsername" );
		$pass = $db->clean( "aDirtyPassword' );drop table users;" );
		
		//
		// nice clean query
		//	
		
		$sql = "insert into users ( username , password ) values( %s , %s )";
		$sql = sprintf( $sql , $user , $pass );
		
		$userId = $db->nonquery( $sql , true ); // 'TRUE' returns the last id or false
		
		// if its not false
		if( $userId != false )
		{
			print "Created new user with No. $userId <br />\n";
		}
		else
		{
			print $db->geterror();
		}
			
			
///////////////////////////////////////////////////////////////////////////////////////////
// UPDATE EXAMPLE - RETURN ROWS AFFECTED
///////////////////////////////////////////////////////////////////////////////////////////			
			
		print "<h2>UPDATE EXAMPLE - RETURN ROWS AFFECTED</h2>\n";
			
		//
		// always sanitize strings that go into sql queries!
		//
			
		$user = $db->clean( "anewname" );
		$pass = $db->clean( "anewpassword" );

		//
		// nice clean query
		//
		
		$sql = "update users set username=%s , password=%s where id=%d";
		$sql = sprintf( $sql , $user , $pass , $userId );
		
		$rowschanged = $db->nonquery( $sql , false ); // 'FALSE' returns the rows affected
		
		// query was not false and at least one row changed
		if( $rowschanged != false )
		{
			print "$rowschanged row(s) modified <br />\n";
		}
		else
		{
			print $db->geterror();
		}	
			
			
///////////////////////////////////////////////////////////////////////////////////////////
// SELECT CELL EXAMPLE
///////////////////////////////////////////////////////////////////////////////////////////	
			
		print "<h2>SELECT CELL EXAMPLE</h2>\n";
			
		//
		// nice clean query
		//
		
		$sql = "select username from users where id=%d";			
		$sql = sprintf( $sql , 1 );
				
		$cell = $db->queryone( $sql ); // returns associative array or false
		
		if( $cell )
		{
			print $cell . "<br />\n";		
		}
		else
		{
			print $db->geterror();
		}			
			

///////////////////////////////////////////////////////////////////////////////////////////
// SELECT ROW EXAMPLE
///////////////////////////////////////////////////////////////////////////////////////////	

		print "<h2>SELECT ROW EXAMPLE</h2>\n";
			
		//
		// nice clean query
		//
		
		$sql = "select * from users where id=%d";			
		$sql = sprintf( $sql , $userId );
			
		$aRow = $db->queryrow( $sql ); // returns associative array or false
		
		if( $aRow )
		{
			print "\$aRow->id : " . $aRow->id . "<br />\n";
			print "\$aRow['id'] : " . $aRow['id']  . "<br />\n";
			print "\$aRow[0] : " . $aRow[0]  . "<br />\n";
			print $aRow->username . "<br />\n";
			print $aRow->password . "<br />\n";
			print "<br />\n";
		}
		else
		{
			print $db->geterror();
		}	
			

///////////////////////////////////////////////////////////////////////////////////////////
// SELECT MULTIPLE ROWS FOREACH ITERATOR EXAMPLE
///////////////////////////////////////////////////////////////////////////////////////////	
		
		print "<h2>SELECT MULTIPLE ROWS FOREACH ITERATOR EXAMPLE</h2>\n";
		
		//
		// nice clean query
		//
		
		$sql = "select * from users";		
			
		$rows = $db->queryrows( $sql ); // returns associative array or false
		
		if( $rows )
		{
			foreach( $rows as $row )
			{
				print $row->id . "<br />\n";
				print $row['username'] . "<br />\n";
				print $row[2] . "<br />\n";
				print "<br />\n";
								
				foreach( $row as $col )
				{
					print $col . "  ...<br />\n";
				}
				
			}
		}
		else
		{
			print $db->geterror();
		}	
		
		
///////////////////////////////////////////////////////////////////////////////////////////
// SELECT MULTIPLE ROWS FOREACH ITERATOR EXAMPLE
///////////////////////////////////////////////////////////////////////////////////////////	
		
		print "<h2>SELECT MULTIPLE ROWS COUNT AND INDEXES EXAMPLE</h2>\n";
		
		//
		// nice clean query
		//
		
		$sql = "select * from users";		
			
		$rows = $db->queryrows( $sql ); // returns associative array or false
		
		if( $rows )
		{
			for( $i=0; $i < count( $rows ); $i++ )
			{
				print $rows[$i]->id . "<br />\n";
				print $rows[$i]['username'] . "<br />\n";
				print $rows[$i][2] . "<br />\n";
				print "<br />\n";
			}
		}
		else
		{
			print $db->geterror();
		}	
		
   
		
///////////////////////////////////////////////////////////////////////////////////////////
// PRINT SQL DEBUG LOG
///////////////////////////////////////////////////////////////////////////////////////////	
		
		print "<h2>PRINT SQL DEBUG LOG</h2>\n";
		
		print $db->numqueries() . " queries executed <br />\n";
		
		$db->printqueries( true ); // true for html output :)
		
		
		
///////////////////////////////////////////////////////////////////////////////////////////
// SPRING CLEAN
///////////////////////////////////////////////////////////////////////////////////////////	
		
		print "<h2>SPRING CLEANING</h2>\n"; 
		
		if( $db->queryone( "select count(*) from users" ) > 5 )
		{
			if( $db->nonquery( "delete from users where id > 5" , false ) )
			{
				print "cleaned out a few rows <br />\n";
			}
			else
			{
				print $db->geterror();
			}
		}	
	}
	else
	{
		print $db->geterror();
	}

	
///////////////////////////////////////////////////////////////////////////////////////////
// ERROR EXAMPLE 
///////////////////////////////////////////////////////////////////////////////////////////	

	print "<h2>ERROR EXAMPLE</h2>\n"; 
	$db->nonquery( "select ll from eee" , false);


	$db->close();
	$db = null;
