<?php
$host = 'localhost';
$port = '8081';
$null = NULL;

$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($server, 0, $port);
socket_listen($server);
set_time_limit(0);
$connection = array($server);

$start = true;
while ($start) {
	$newListOfConnections = $connection;
	socket_select($newListOfConnections, $null, $null, 0, 10);
	
	if (in_array($server, $newListOfConnections)) {
		$socket_new = socket_accept($server);
		$connection[] = $socket_new; 
		$header = socket_read($socket_new, 1024); 
		handshaking($header, $socket_new, $host, $port);
		
		socket_getpeername($socket_new, $ip); 
		
		$found_socket = array_search($server, $newListOfConnections);
		unset($newListOfConnections[$found_socket]);
	}
	
	foreach ($newListOfConnections as $socketWithMess) {	

		while(socket_recv($socketWithMess, $buf, 1024, 0) >= 1)
		{
			$received_text = uncode($buf); 
			$tst_msg = json_decode($received_text);  
			$user_message = $tst_msg; 
			if ($user_message === 'e'){
				echo "Server has closed";
				$start = false;
				break 2;
			}
			$user_message = $user_message * $user_message;

			$response_text = code(json_encode($user_message ));

			@socket_write($socketWithMess,$response_text,strlen($response_text));

			break 2; 
		}
		
		$buf = @socket_read($socketWithMess, 1024, PHP_NORMAL_READ);
		if ($buf === false) { 
			$found_socket = array_search($socketWithMess, $connection);
			socket_getpeername($socketWithMess, $ip);
			unset($connection[$found_socket]);
		}
	}
}

socket_close($server);

function uncode($text) {
	$length = ord($text[1]) & 127;
	if($length == 126) {
		$codes = substr($text, 4, 4);
		$data = substr($text, 8);
	}
	elseif($length == 127) {
		$codes = substr($text, 10, 4);
		$data = substr($text, 14);
	}
	else {
		$codes = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $codes[$i%4];
	}
	return $text;
}

function code($text)
{
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);
	
	if($length <= 125)
		$header = pack('CC', $b1, $length);
	elseif($length > 125 && $length < 65536)
		$header = pack('CCn', $b1, 126, $length);
	elseif($length >= 65536)
		$header = pack('CCNN', $b1, 127, $length);
	return $header.$text;
}

function handshaking($receved_header,$newConnection, $host, $port)
{
	$headers = array();
	$lines = preg_split("/\r\n/", $receved_header);
	foreach($lines as $line)
	{
		$line = chop($line);
		if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
		{
			$headers[$matches[1]] = $matches[2];
		}
	}

	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
	"Upgrade: websocket\r\n" .
	"Connection: Upgrade\r\n" .
	"WebSocket-Origin: $host\r\n" .
	"WebSocket-Location: ws://$host:$port\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($newConnection,$upgrade,strlen($upgrade));
}
?>