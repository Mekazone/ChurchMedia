<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\Inspirational;
use App\InspirationalsAttachment;

class InspirationalsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "INSPIRATIONAL CORNER";
        $inspirational = Inspirational::all()->count();
        if($inspirational > 0)
        {                                  
            $results = DB::table('inspirationals')->join('inspirationals_attachments','inspirationals.id','=','inspirationals_attachments.postId')
                        ->select('inspirationals.date','inspirationals.title','inspirationals.body','inspirationals.slug','inspirationals_attachments.name')
                        ->where('inspirationals_attachments.filePosition','top')
                        ->orderBy('inspirationals.date', 'desc')->limit(4)->paginate(10);
            //$results = Inspirational::orderBy('date', 'desc')->paginate(10);
        }
        return view('frontend.inspirationals', compact('results', 'inspirational', 'title'));
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
        $title = "INSPIRATIONAL CORNER";
        $inspirational = Inspirational::all()->count();
        if($inspirational > 0)
        {                       
            $result = DB::table('inspirationals')
                       ->join('inspirationals_attachments', 'inspirationals.id', '=', 'inspirationals_attachments.postId')
                       ->select('inspirationals.id','inspirationals.date', 'inspirationals.title', 'inspirationals.body', 'inspirationals.slug')
                       ->where('inspirationals.date', $date)
                       ->where('inspirationals.slug', $slug)
                       ->first();
                       
            $attachments = DB::table('inspirationals')
                       ->join('inspirationals_attachments', 'inspirationals.id', '=', 'inspirationals_attachments.postId')
                       ->select('inspirationals_attachments.fileCategoryId', 'inspirationals_attachments.name', 'inspirationals_attachments.filePosition')
                       ->where('inspirationals.date', $date)
                       ->where('inspirationals.slug', $slug)
                       ->orderBy('inspirationals_attachments.id', 'asc')
                       ->get();
        }
        return view('frontend.inspirationalsView', compact('result', 'inspirational', 'attachments', 'title'));
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
