<?php
error_reporting(E_ALL);
require_once 'cfg.php';
require_once 'JsHttpRequest.php';
$JsHttpRequest = new JsHttpRequest ( 'windows-1251' );

function play($file) {
	$err = '';
	exec('pgrep omxplayer', $pids);  //omxplayer
	if ( empty($pids) ) {
		@unlink (FIFO);
		posix_mkfifo(FIFO, 0777);
		chmod(FIFO, 0777);
		shell_exec ( getcwd().'/omx_php.sh '.escapeshellarg($file));
		$out = 'playing '.basename($file);
	} else {
		$err = 'omxplayer is already runnning';
	}
	return array ( 'res' => $out, 'err' => $err );
}

function send($command) {
	$err = '';
	exec('pgrep omxplayer', $pids);
	if ( !empty($pids) ) {
		if ( is_writable(FIFO) ) {
			if ( $fifo = fopen(FIFO, 'w') ) {
				stream_set_blocking($fifo, false);
				fwrite($fifo, $command);
				fclose($fifo);
				if ($command == 'q') {
					sleep (1);
					@unlink(FIFO);
					$out = 'stopped';
				}
			}
		}
	} else {
		$err .= 'not running';
	}
	return array ( 'res' => $out, 'err' => $err );
}

$act = $_REQUEST['act'];
unset($result);

switch ($act) {

	case 'play':
	$result = play($_REQUEST['arg']);
	break;

	case 'stop';
	$result = send('q');
	break;

	case 'pause';
	$result = send('p');
	break;

	case 'volup';
	$result = send('+');
	break;

	case 'voldown';
	$result = send('-');
	break;

	case 'seek-30';
	$result = send(pack('n',0x5b44));
	break;

	case 'seek30';
	$result = send(pack('n',0x5b43));
	break;

	case 'seek-600';
	$result = send(pack('n',0x5b42));
	break;

	case 'seek600';
	$result = send(pack('n',0x5b41));
	break;

	case 'speedup';
	$result = send('1');
	break;

	case 'speeddown';
	$result = send('2');
	break;

	case 'nextchapter';
	$result = send('o');
	break;

	case 'prevchapter';
	$result = send('i');
	break;

	case 'nextaudio';
	$result = send('k');
	break;

	case 'prevaudio';
	$result = send('j');
	break;

	case 'togglesubtitles';
	$result = send('s');
	break;

	case 'nextsubtitles';
	$result = send('m');
	break;

	case 'prevsubtitles';
	$result = send('n');
	break;

	default:
	$err = 'wrong command';
}

$GLOBALS['_RESULT'] = $result;
?>
