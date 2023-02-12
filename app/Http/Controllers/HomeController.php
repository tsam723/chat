<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //$entireTable = Message::all();
        /*$entireTable = DB::table('messages')
        ->leftjoin('users', 'messages.iduser', '=', 'users.id')
        ->select('users.name AS username', 'messages.message AS msg', 'users.id AS userid')
        ->orderBy('messages.idmsg', 'asc')->get();*/
        return view('home');
        //->with('entireTable', $entireTable);
    }
}
