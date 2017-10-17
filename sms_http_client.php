<?php
class HTTP_Client {
  var $host;
  var $port;
  var $socket;
  var $errno;
  var $errstr;
  var $timeout;
  var $buf;
  var $result;
  var $post_data;
  var $path = "/";
  var $agent_name = "MyAgent";

	//Constructor, timeout 30s
  function HTTP_Client($host, $port, $timeout = 30) {
    $this->host = $host;
    $this->port = $port;
    $this->timeout = $timeout;
  }

	//Opens a connection
  function connect() {
    $this->socket = fsockopen($this->host,
    $this->port,
    $this->errno,
    $this->errstr,
    $this->timeout
  );
  if(!$this->socket) return false;
  else return true;
}

	//Set the path
  function set_path($path) {
    $this->path = $path;
  }

	//Send request and clean up
  function send_request() {
    if(!$this->connect()) {
      return false;
    }
    else {
      $this->result = $this->request($this->post_data);
      return $this->result;
    }
  }

  function request($post_data) {
    $this->buf = "";
    fwrite($this->socket,
    "POST $this->path HTTP/1.0\r\n".
    "Host:$this->host\r\n".
    "User-Agent: $this->agent_name\r\n".
    "Content-Type: application/xml\r\n".
    "Content-Length: ".strlen($post_data).
    "\r\n".
    "\r\n".$post_data.
    "\r\n"
    );

    while(!feof($this->socket))
      $this->buf .= fgets($this->socket, 2048);
      $this->close();
    return $this->buf;
  }

  function close() {
    fclose($this->socket);
  }
  
}
?>
