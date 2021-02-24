<?php
##
## Retrieve the file passed in the URL and send it to the browser.
## Set the $WORKDIR variable to point to the directory containing
## the files to be displayed.

$FILENAME = $_SERVER['QUERY_STRING'];
$WORKDIR = '/var/lib/genenet/bnw/';

if ( file_exists( $WORKDIR . $FILENAME ) )
{
    header("Content-type: text/plain");
    ##readfile($WORKDIR.$FILENAME);
    print file_get_contents( "file://" . $WORKDIR . $FILENAME, FALSE );
}
else
{
    echo " File " . $FILENAME . " not found in " . $WORKDIR . "\n";
}
?>

