<?php
/**
 * PuTTY-to-OpenSSH Converter
 */

header( 'content-type: text/plain; charset=utf-8' );

if ( PHP_SAPI !== 'cli' ) {
    echo 'This is meant to be run on the command line.' . PHP_EOL;
    exit( 1 );
}

if ( count( $argv ) !== 3 ) {
    echo 'Incorrect number of arguments.' . PHP_EOL;
    echo 'Format: php convert.php putty.reg ssh_config' . PHP_EOL;
    exit( 1 );
}

$src = $argv[1];
$dst = $argv[2];

// parse registry export line-by-line.
$lines = file( $src );

// holds all sessions found.
$list = array();

// sessions counter
$i = 0;

foreach ( $lines as $line ) {
    $parts = str_split( $line );

    $str = '';

    // parse file to strip non-ASCII chars.
    foreach ( $parts as $chr ) {
        $ord = ord( $chr );

        $str .= ( $ord ? $chr : '' );
    }

    // denotes start of new session.
    if ( preg_match( '/Sessions\\\(.*)\]/', $str, $matches ) ) {
        $list[++$i] = array();
        $list[$i]['Host'] = urldecode( $matches[1] );
    }

    // parse username.
    if ( preg_match( '/"UserName"="(.*)"/', $str, $matches ) ) {
        $list[$i]['User'] = $matches[1];
    }

    // parse hostname.
    if ( preg_match( '/"HostName"="(.*)"/', $str, $matches ) ) {
        $list[$i]['HostName'] = $matches[1];

        if ( strpos( $list[$i]['HostName'], '@' ) !== FALSE )
        {
            list( $u, $h ) = explode( '@', $list[$i]['HostName'] );

            $list[$i]['HostName'] = $h;

            if( !isset($list[$i]['User']) && strpos( $u, ':' ) !== FALSE) {
                list( $u, $p ) = explode( ':', $u );
                $list[$i]['User'] = $u;
            }
        }
    }

    // parse port.
    if ( preg_match( '/"PortNumber"="(.*)"/', $str, $matches ) ) {
        $list[$i]['Port'] = hexdec($matches[1]);
    }

    // parse PublicKeyFile.
    if ( preg_match( '/"PublicKeyFile"="(.*)"/', $str, $matches ) ) {
        if(strlen($matches[1]) > 0) {
            echo "WARNING: for host ".$list[$i]['Host']." copy key manually. ".$matches[1]."\n\n";
        }
    }

}

$config = "";
foreach ($list as $session) {
    $srv = "";
    foreach ($session as $key => $value) {
        if($key != "Host") $srv .= "\t";
        $srv .= sprintf("%s %s\n", $key, $value);
    }
    $config .= $srv."\n";
}

// write the file to the $dst.
$bytes = file_put_contents( $dst, $config );

echo sprintf( 'Completed conversion. %d bytes writtent to %s.', $bytes, $dst ) . PHP_EOL;
?>
