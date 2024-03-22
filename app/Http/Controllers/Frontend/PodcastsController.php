<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\Podcast;
use App\PodcastsAttachment;

class PodcastsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "PODCASTS";
        $podcasts = Podcast::all()->count();
        if($podcasts > 0)
        {                                  
            $results = DB::table('podcasts')->join('podcasts_attachments','podcasts.id','=','podcasts_attachments.postId')
                        ->select('podcasts.date','podcasts.title','podcasts.body','podcasts.slug','podcasts_attachments.name')
                        ->where('podcasts_attachments.filePosition','top')
                        ->orderBy('podcasts.date', 'desc')->paginate(10);
            //$results = Podcast::orderBy('date', 'desc')->paginate(10);
        }
        return view('frontend.podcasts', compact('results', 'podcasts', 'title'));
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
        $title = "PODCASTS";
        $podcasts = Podcast::all()->count();
        if($podcasts > 0)
        {                       
            $result = DB::table('podcasts')
                       ->join('podcasts_attachments', 'podcasts.id', '=', 'podcasts_attachments.postId')
                       ->select('podcasts.id','podcasts.date', 'podcasts.title', 'podcasts.body', 'podcasts.slug')
                       ->where('podcasts.date', $date)
                       ->where('podcasts.slug', $slug)
                       ->first();
                       
            $attachments = DB::table('podcasts')
                       ->join('podcasts_attachments', 'podcasts.id', '=', 'podcasts_attachments.postId')
                       ->select('podcasts_attachments.fileCategoryId', 'podcasts_attachments.name', 'podcasts_attachments.filePosition')
                       ->where('podcasts.date', $date)
                       ->where('podcasts.slug', $slug)
                       ->orderBy('podcasts_attachments.id', 'asc')
                       ->get();
        }
        return view('frontend.podcastsView', compact('result', 'podcasts', 'attachments', 'title'));
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
