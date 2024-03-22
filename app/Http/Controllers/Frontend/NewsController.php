<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\News;
use App\NewsAttachment;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "NEWS AND EVENTS";
        $news = News::all()->count();
        if($news > 0)
        {                                  
            //$results = News::orderBy('date', 'desc')->paginate(10);
            $results = DB::table('news')->join('news_attachments','news.id','=','news_attachments.postId')
                        ->select('news.date','news.title','news.body','news.slug','news_attachments.name')
                        ->where('news_attachments.filePosition','top')
                        ->orderBy('news.date', 'desc')->paginate(10);
        }
        return view('frontend.news', compact('results', 'news', 'title'));
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
        $title = "NEWS &amp; EVENTS";
        $news = News::all()->count();
        if($news > 0)
        {                       
            $result = DB::table('news')
                       ->join('news_attachments', 'news.id', '=', 'news_attachments.postId')
                       ->select('news.id','news.date', 'news.title', 'news.body', 'news.slug')
                       ->where('news.date', $date)
                       ->where('news.slug', $slug)
                       ->first();
                       
            $attachments = DB::table('news')
                       ->join('news_attachments', 'news.id', '=', 'news_attachments.postId')
                       ->select('news_attachments.fileCategoryId', 'news_attachments.name', 'news_attachments.filePosition')
                       ->where('news.date', $date)
                       ->where('news.slug', $slug)
                       ->orderBy('news_attachments.id', 'asc')
                       ->get();
        }
        return view('frontend.newsView', compact('result', 'news', 'attachments', 'title'));
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
