<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class StreamController extends Controller
{
    /**
     * The stream source.
     *
     * @return \Illuminate\Http\Response
     */
    public function stream(){
        // make session read-only
        session_start();
        session_write_close();

        // disable default disconnect checks
        ignore_user_abort(true);

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header("Access-Control-Allow-Origin: *");
        
        // Is this a new stream or an existing one?
        $lastEventId = floatval(isset($_SERVER["HTTP_LAST_EVENT_ID"]) ? $_SERVER["HTTP_LAST_EVENT_ID"] : 0);
        if ($lastEventId == 0) {
            $lastEventId = floatval(isset($_GET["lastEventId"]) ? $_GET["lastEventId"] : 0);
        }

        echo ":" . str_repeat(" ", 2048) . "\n"; // 2 kB padding for IE
        echo "retry: 2000\n";

        
        $conn = pg_pconnect("dbname=chat user=postgres password=postgres");
        if (!$conn) {
            echo "An error occurred.\n";
            exit;
        }

        while (true) {
            pg_query($conn, 'LISTEN messages;');
            $notify = pg_get_notify($conn);
            // Every second, send a "ping" event.
            //echo "event: ping\n";

            //echo 'data: ' . stripslashes(json_encode($notify));
            //echo "\n\n";
            $sql = "SELECT u.name as username, m.message as msg FROM messages m
            LEFT JOIN users u ON m.iduser = u.id";
            $sql1 = "SELECT u.name as username, m.message as msg FROM messages m
            LEFT JOIN users u ON m.iduser = u.id ORDER BY idmsg DESC LIMIT 1";

            $result=pg_query($conn, $sql1);

            if (!$notify) {
            echo 'data: No new messages' . "\n\n";
            ob_flush();
            flush();
            //echo 'heartbeat' . "\n\n";
            } else {

            //echo 'data:' . stripslashes(json_encode($notify)) . "\n\n";
            //echo 'data: This is a message at time ' . stripslashes(json_encode($notify)) . "\n\n";    
                    while($row = pg_fetch_assoc($result)){ 
                    echo 'data:' . $row['username'] . ': ' . $row['msg'] . "\n\n";
                }
            ob_flush();
            flush();
            
            }
            
            // Send a simple message at random intervals.
            // Break the loop if the client aborted the connection (closed the page)

            if (connection_aborted()) break;

            sleep(2);
        }
    pg_close($conn);
    }
}