<?php
/**
 * PuTTY-to-Shuttle Converter
 */

header( 'content-type: text/plain; charset=utf-8' );

if ( PHP_SAPI !== 'cli' )
{
    echo 'This is meant to be run on the command line.' . PHP_EOL;
    exit( 1 );
};

if ( count( $argv ) !== 3 )
{
    echo 'Incorrect number of arguments.' . PHP_EOL;
    echo 'Format: php convert.php putty.reg shuttle.json' . PHP_EOL;
    exit( 1 );
};

$src = $argv[1];
$dst = $argv[2];

// parse registry export line-by-line.
$lines = file( $src );

// holds all sessions found.
$list = array();

// holds current session being processed in loop.
$session = array();

foreach ( $lines as $line )
{
    $parts = str_split( $line );

    $str = '';

    // parse file to strip non-ASCII chars.
    foreach ( $parts as $chr )
    {
        $ord = ord( $chr );

        $str .= ( $ord ? $chr : '' );
    };

    // denotes start of new session.
    if ( preg_match( '/Sessions\\\(.*)\]/', $str, $matches ) )
    {
        if ( isset( $session['name'] ) )
        {
            // reorder the session names based on the hostname for uniformity.
            if ( strpos( $session['name'], '@' ) !== FALSE )
            {
                list( $u, $h ) = explode( '@', $session['name'] );
            };

            // is the hostname provided as an IP address?
            $ip = preg_match( '/\d+\.\d+\.\d+\.\d+/', $session['host'] );

            $session['name'] = ( $ip ? $h : $session['host'] ) . ' - ' . $u;

            $list[] = $session;
        };

        $session = array(
            'name' => urldecode( $matches[1] ),
            'host' => '',
            'user' => ''
        );
    };

    // parse hostname.
    if ( preg_match( '/"HostName"="(.*)"/', $str, $matches ) )
    {
        #if ( strlen( $matches[1] ) === 0 ) continue;

        $session['host'] = $matches[1];
        
        if ( strpos( $session['host'], '@' ) !== FALSE )
        {
            list( $u, $h ) = explode( '@', $session['host'] );

            $session['host'] = $h;
        };
    };

    // parse username.
    if ( preg_match( '/"UserName"="(.*)"/', $str, $matches ) )
    {
        #if ( strlen( $matches[1] ) === 0 ) continue;

        $session['user'] = $matches[1];
    };
};

// construct .shuttle.json configuration file.
$shuttle = array(
    '_comments' => array(
        'Valid terminals include: \'Terminal.app\' or \'iTerm\'',
        'Hosts will also be read from your ~/.ssh/config or /etc/ssh_config file, if available',
        'For more information on how to configure, please see http://fitztrev.github.io/shuttle/'
    ),
    'terminal' => 'Terminal.app',
    'launch_at_login' => FALSE,
    'show_ssh_config_hosts' => TRUE,
    'ssh_config_ignore_hosts' => array(),
    'ssh_config_ignore_keywords'=> array(),
    'hosts' => array()
);

// convert sessions to accounts in shuttle format.
foreach ( $list as $item )
{
    $shuttle['hosts'][] = array( 
        'name' => $item['name'], 
        'cmd' => sprintf( 'ssh %s@%s', $item['user'], $item['host'] ) 
    );
};

// convert to JSON string.
$shuttle_json = json_encode( $shuttle );

// write the file to the $dst.
$bytes = file_put_contents( $dst, $shuttle_json );

echo sprintf( 'Completed conversion. %d bytes writtent to %s.', $bytes, $dst ) . PHP_EOL;
?>
