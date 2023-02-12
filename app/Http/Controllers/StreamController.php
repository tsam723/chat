<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Message;
use DB;

class StreamController extends Controller
{
    /**
     * The stream source.
     *
     * @return \Illuminate\Http\Response
     */
    public function stream(){
        set_time_limit(0);
        // make session read-only
        session_start();
        session_write_close();

        // disable default disconnect checks
        ignore_user_abort(true);

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header("Access-Control-Allow-Origin: *");
        
        $prvic = true;
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
            /*
            $sql = "SELECT u.name as username, m.message as msg, u.id as id FROM messages m
            LEFT JOIN users u ON m.iduser = id ORDER BY idmsg ASC";
            $sql1 = "SELECT u.name as username, m.message as msg, u.id as id FROM messages m
            LEFT JOIN users u ON m.iduser = id ORDER BY idmsg DESC LIMIT 1";

            $result=pg_query($conn, $sql1);
            $result2=pg_query($conn, $sql);
            */         

            if($prvic){
                //vsa sporočila iz baze
                $entireTable = DB::table('messages')
                ->leftjoin('users', 'messages.iduser', '=', 'users.id')
                ->select('users.name AS username', 'messages.message AS msg', 'users.id AS userid')
                ->orderBy('messages.idmsg', 'asc')->get();

                foreach ($entireTable as $row1) {
                    echo 'data: {"id": "' . $row1->userid . '", "username": "' . $row1->username . '", "msg": "' . $row1->msg . '"}' . "\n\n";
                }
                
                ob_flush();
                flush();
                $prvic=false;
            }

            if (!$notify) {
                //echo 'heartbeat' . "\n\n";
            } else {  
                //najnovejšo sporočilo iz baze
                $latestMsg = Message::latest('idmsg')->first();

                /*$latestMsg = DB::table('messages')
                ->leftjoin('users', 'messages.iduser', '=', 'users.id')
                ->select('users.name AS username', 'messages.message AS msg', 'users.id AS userid')
                ->orderBy('messages.idmsg', 'desc')->take(1)->get();*/
                
                $id =  $latestMsg->iduser;
                $user = DB::table('users')->where('id', $id)->first();
                $username = $user->name;
                echo 'data: {"id": "' . $latestMsg->iduser . '", "username": "' . $username . '", "msg": "' . $latestMsg->message . '"}' . "\n\n";
                //echo 'data: {"id": "' . $latestMsg->iduser . '", "msg": "' . $latestMsg->message . '"}' . "\n\n";
                
                //echo 'data: {"username": "test", "msg": "xd"}' . "\n\n";
                
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