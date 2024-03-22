<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\Homily;
use App\HomiliesAttachment;

class HomiliesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //display results if exists, else display form
        $title = "SUNDAY HOMILIES";
        $homily = Homily::all()->count();
        if($homily > 0)
        {                          
            $results = Homily::orderBy('date', 'desc')->paginate(10);
        }
        return view('frontend.homilies', compact('results', 'homily', 'title'));
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
        $title = "SUNDAY HOMILIES";
        $homily = Homily::all()->count();
        if($homily > 0)
        {             
            /*$result = DB::table('homilies')
                       ->join('homilies_attachments', 'homilies.id', '=', 'homilies_attachments.postId')
                       ->select('homilies.id','homilies.date', 'homilies.title', 'homilies.body', 'homilies.slug')
                       ->where('homilies.date', $date)
                       ->where('homilies.slug', $slug)
                       ->first();
                       */
            $result = Homily::select('id','date', 'title', 'body', 'slug')
                       ->where('date', $date)
                       ->where('slug', $slug)
                       ->first();
                       
            $attachments = DB::table('homilies')
                       ->join('homilies_attachments', 'homilies.id', '=', 'homilies_attachments.postId')
                       ->select('homilies_attachments.fileCategoryId', 'homilies_attachments.name', 'homilies_attachments.filePosition')
                       ->where('homilies.date', $date)
                       ->where('homilies.slug', $slug)
                       ->orderBy('homilies_attachments.id', 'asc')
                       ->get();
        }
        return view('frontend.homiliesView', compact('result', 'homily', 'attachments', 'title'));
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
