<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\Saint;
use App\SaintsAttachment;


class SaintsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "YOU TOO CAN BE A SAINT";
        $saints = Saint::all()->count();
        if($saints > 0)
        {                                  
            //$results = News::orderBy('date', 'desc')->paginate(10);
            $results = DB::table('saints')->join('saints_attachments','saints.id','=','saints_attachments.postId')
                        ->select('saints.date','saints.title','saints.body','saints.slug','saints_attachments.name')
                        ->where('saints_attachments.filePosition','top')
                        ->orderBy('saints.date', 'desc')->paginate(10);
        }
        return view('frontend.saints', compact('results', 'saints', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($date, $slug)
    {
        //display results if exists, else display form
        $title = "YOU TOO CAN BE A SAINT";
        $saints = Saint::all()->count();
        if($saints > 0)
        {                       
            $result = DB::table('saints')
                       ->join('saints_attachments', 'saints.id', '=', 'saints_attachments.postId')
                       ->select('saints.id','saints.date', 'saints.title', 'saints.body', 'saints.slug')
                       ->where('saints.date', $date)
                       ->where('saints.slug', $slug)
                       ->first();
                       
            $attachments = DB::table('saints')
                       ->join('saints_attachments', 'saints.id', '=', 'saints_attachments.postId')
                       ->select('saints_attachments.fileCategoryId', 'saints_attachments.name', 'saints_attachments.filePosition')
                       ->where('saints.date', $date)
                       ->where('saints.slug', $slug)
                       ->orderBy('saints_attachments.id', 'asc')
                       ->get();
        }
        return view('frontend.saintsView', compact('result', 'saints', 'attachments', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
