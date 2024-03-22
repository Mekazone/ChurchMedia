<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\Video;
use App\VideosAttachment;

class VideosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "VIDEOS";
        $videos = Video::all()->count();
        if($videos > 0)
        {                                  
            $results = DB::table('videos')->join('videos_attachments','videos.id','=','videos_attachments.postId')
                        ->select('videos.date','videos.title','videos.body','videos.slug','videos_attachments.name')
                        ->where('videos_attachments.filePosition','top')
                        ->orderBy('videos.date', 'desc')->paginate(10);
            //$results = Video::orderBy('date', 'desc')->paginate(10);
        }
        return view('frontend.videos', compact('results', 'videos', 'title'));
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
        $title = "VIDEOS";
        $videos = Video::all()->count();
        if($videos > 0)
        {                       
            $result = DB::table('videos')
                       ->join('videos_attachments', 'videos.id', '=', 'videos_attachments.postId')
                       ->select('videos.id','videos.date', 'videos.title', 'videos.body', 'videos.slug')
                       ->where('videos.date', $date)
                       ->where('videos.slug', $slug)
                       ->first();
                       
            $attachments = DB::table('videos')
                       ->join('videos_attachments', 'videos.id', '=', 'videos_attachments.postId')
                       ->select('videos_attachments.fileCategoryId', 'videos_attachments.name', 'videos_attachments.filePosition')
                       ->where('videos.date', $date)
                       ->where('videos.slug', $slug)
                       ->orderBy('videos_attachments.id', 'asc')
                       ->get();
        }
        return view('frontend.videosView', compact('result', 'videos', 'attachments', 'title'));
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
