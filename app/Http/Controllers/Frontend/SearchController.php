<?php

namespace App\Http\Controllers\frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        $term = $request->search;
        $title = "SEARCH RESULT";
         
        $podcasts = DB::table('podcasts')->select('date','title','body','slug','category')->where('title','like',"%$term%")
                ->orWhere('body','like',"%$term%");
                
        $news = DB::table('news')->select('date','title','body','slug','category')->where('title','like',"%$term%")
                ->orWhere('body','like',"%$term%");
                
        $inspirationals = DB::table('inspirationals')->select('date','title','body','slug','category')->where('title','like',"%$term%")
                ->orWhere('body','like',"%$term%");    
                    
        $letters = DB::table('letters')->select('date','title','body','slug','category')->where('title','like',"%$term%")
                ->orWhere('body','like',"%$term%");
                
        $homilies = DB::table('homilies')->select('date','title','body','slug','category')->where('title','like',"%$term%")
                ->orWhere('body','like',"%$term%");
                
        $bishop_events = DB::table('bishop_events')->select('date','title','body','slug','category')->where('title','like',"%$term%")
                ->orWhere('body','like',"%$term%");
                
        $pastoral_letters = DB::table('pastoral_letters')->select('date','title','body','slug','category')->where('title','like',"%$term%")
                ->orWhere('body','like',"%$term%");   
        $opinions = DB::table('opinions')->select('date','title','body','slug','category')->where('title','like',"%$term%")
                ->orWhere('body','like',"%$term%");
        $saints = DB::table('saints')->select('date','title','body','slug','category')->where('title','like',"%$term%")
                ->orWhere('body','like',"%$term%");                     

        $query = DB::table('videos')->select('date','title','body','slug','category')->where('title','like',"%$term%")
                ->orWhere('body','like',"%$term%")
                ->union($podcasts)
                ->union($news)
                ->union($inspirationals)
                ->union($letters)
                ->union($homilies)
                ->union($bishop_events)
                ->union($pastoral_letters)
                ->union($opinions)
                ->union($saints)
                ->get();

        return view('frontend.searchResults', compact('query','title'));
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
