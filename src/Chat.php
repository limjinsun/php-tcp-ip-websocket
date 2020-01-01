<?php 

namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {

    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s'."\n", $from->resourceId, str_replace("\r\n","",$msg), $numRecv, $numRecv == 1 ? '' : 's');
        
        $myObj->resourceId = $from->resourceId;
        $myObj->msg = $msg;
        $myJSON = json_encode($myObj);

        foreach ($this->clients as $client) {
            $client->send($myJSON); // Send message to all clients. 
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

}





// if ($from !== $client) {
//     // If sender is not the receiver, send to each client connected
//     $client->send($msg);
// }