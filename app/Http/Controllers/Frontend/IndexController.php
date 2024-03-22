<?php

namespace App\Http\Controllers\frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\News;
use App\NewsAttachment;
use App\Video;
use App\VideosAttachment;
use App\Podcast;
use App\PodcastsAttachment;
use App\homeGallery;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //display results for home gallery, news, videos and podcasts        
        $news = News::all()->count();
        if($news > 0)
        {                                  
            //$results = News::orderBy('date', 'desc')->limit(4)->get();
            $results = DB::table('news')->join('news_attachments','news.id','=','news_attachments.postId')
                        ->select('news.date','news.title','news.slug','news_attachments.name')
                        ->where('news_attachments.filePosition','top')
                        ->orderBy('news.date', 'desc')->limit(4)->get();
        }
        
        $videos = Video::all()->count();
        if($videos > 0)
        {                                  
            //$results2 = Video::orderBy('date', 'desc')->limit(4)->get();
            $results2 = DB::table('videos')->join('videos_attachments','videos.id','=','videos_attachments.postId')
                        ->select('videos.date','videos.title','videos.slug','videos_attachments.name')
                        ->where('videos_attachments.filePosition','top')
                        ->orderBy('videos.date', 'desc')->limit(4)->get();
        }
        
        $podcasts = Podcast::all()->count();
        if($podcasts > 0)
        {                                  
            //$results3 = Podcast::orderBy('date', 'desc')->paginate(10);
            $results3 = DB::table('podcasts')->join('podcasts_attachments','podcasts.id','=','podcasts_attachments.postId')
                        ->select('podcasts.date','podcasts.title','podcasts.slug','podcasts_attachments.name')
                        ->where('podcasts_attachments.filePosition','top')
                        ->orderBy('podcasts.date', 'desc')->limit(4)->get();
        }
        
        $gallery = homeGallery::all()->count();
        if($gallery > 0)
        {                                  
            $results4 = homeGallery::orderBy('created_at', 'asc')->get();
        }

        return view('frontend.index', compact('gallery', 'news', 'results', 'videos', 'results2', 'podcasts', 'results3', 'results4'));
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
    public function show($id)
    {
        //
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
