<?php

include( "intel_header.php" );

if( $ldap->isloggedin() == true )
{
	print "Successfully logged in!";
	#$ldap->debug();
}
else
{
	print "<form action='login.php' method='post'>";
	print "<table cellpadding='10'>";
	print "<tr><td class='padme' colspan='2'><h2>Login</h2></td></tr>";
	print "<tr><td class='padme' valign='top'>Username : </td><td class='padme'><input type='text' name='username' value=''><br/>eg. dmoneil2<br/>eg. ad_pbhansal priviledged admin account</td></tr>";
	print "<tr><td class='padme'>Password : </td><td class='padme'><input type='password' name='password'></td></tr>";
	print "<tr><td class='padme'></td><td class='padme'><input type='submit' value='login'><br><br></td></tr>";
	print "</table>";
	print "</form>";
	
	if( $ldap->geterror() != 0 )
	{
		print "<br/><br/>" . $ldap->geterror();
		print "<br/><br/>" . $username;
	}
	
	
}

include( "intel_footer.php" );

?>
