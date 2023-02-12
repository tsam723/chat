<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use DB;

class InsertController extends Controller
{
    /**
     * The stream source.
     *
     * @return \Illuminate\Http\Response
     */
    public function insertIntoDB(Request $data){

        
        $message = $data->input('text');
        $id = Auth::id();
        
        
        DB::table('messages')->insert([
            'channel' => 'foo',
            'message' => $message,
            'iduser' => $id
        ]);
        return redirect()->back();
    }
}