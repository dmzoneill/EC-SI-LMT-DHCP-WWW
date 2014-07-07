<?php

$ixa = "";

if( isset( $mac ) )
{
	$ixa = shell_exec( "grep -i \"$mac\" /var/www/html/data/* | awk '{ print $1 }'" );
}

print $ixa;
