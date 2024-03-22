<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\Opinion;
use App\OpinionsAttachment;

class OpinionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "OPINIONS";
        $opinions = Opinion::all()->count();
        if($opinions > 0)
        {                                  
            //$results = News::orderBy('date', 'desc')->paginate(10);
            $results = DB::table('opinions')->join('opinions_attachments','opinions.id','=','opinions_attachments.postId')
                        ->select('opinions.date','opinions.title','opinions.body','opinions.slug','opinions_attachments.name')
                        ->where('opinions_attachments.filePosition','top')
                        ->orderBy('opinions.date', 'desc')->paginate(10);
        }
        return view('frontend.opinions', compact('results', 'opinions', 'title'));
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
        $title = "OPINIONS";
        $opinions = Opinion::all()->count();
        if($opinions > 0)
        {                       
            $result = DB::table('opinions')
                       ->join('opinions_attachments', 'opinions.id', '=', 'opinions_attachments.postId')
                       ->select('opinions.id','opinions.date', 'opinions.title', 'opinions.body', 'opinions.slug')
                       ->where('opinions.date', $date)
                       ->where('opinions.slug', $slug)
                       ->first();
                       
            $attachments = DB::table('opinions')
                       ->join('opinions_attachments', 'opinions.id', '=', 'opinions_attachments.postId')
                       ->select('opinions_attachments.fileCategoryId', 'opinions_attachments.name', 'opinions_attachments.filePosition')
                       ->where('opinions.date', $date)
                       ->where('opinions.slug', $slug)
                       ->orderBy('opinions_attachments.id', 'asc')
                       ->get();
        }
        return view('frontend.opinionsView', compact('result', 'opinions', 'attachments', 'title'));
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
