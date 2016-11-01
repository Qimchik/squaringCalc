<?php


$host = 'localhost';
$port = '8111';

$server = socket_create(AF_INET, SOCK_STREAM, 0);
socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($server, 0, $port);
set_time_limit(0);
socket_listen($server);

$connections = array($server);


$null = null;
while (true) {
	$changedConnections = $connections;
	socket_select($changedConnections, $null, $null, 0, 10);

	if (in_array($server, $changedConnections)) {
		$newClient = socket_accept($server);
		$connections[] = $newClient;
		
		$header = socket_read($newClient, 1024);
		perform_handshaking($header, $newClient, $host, $port);
		
		socket_getpeername($newClient, $ip);

		$found_socket = array_search($server, $changedConnections);
		unset($changedConnections[$found_socket]);
	}
	foreach ($changedConnections as $changedConnections_socket) {	
		while(socket_recv($socket_new, $buf, 1024, 0) >= 1)
		{
			$received_text = unmask($buf); //unmask data
			$tst_msg = json_decode($received_text); //json decode 
			$user_message = $tst_msg; //message text

			if ($user_message === 'e'){
				echo "Server has closed";
				$start = false;
				break 2;
			}
			$user_message = $user_message * $user_message;

			$response_text = mask(json_encode($user_message ));

			send_message($response_text); //send data

			$clients = array($socket);
			$getConnection = false;

		}
		
		$buf = @socket_read($changedConnections_socket, 1024, PHP_NORMAL_READ);
		if ($buf === false) {
			$found_socket = array_search($changedConnections_socket, $connections);
			socket_getpeername($changedConnections_socket, $ip);
			unset($connections[$found_socket]);
		}
	}
}



?>