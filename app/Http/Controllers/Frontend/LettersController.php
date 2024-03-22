<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\Letter;
use App\LettersAttachment;

class LettersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "LETTER TO THE PEOPLE OF GOD";
        $letter = Letter::all()->count();
        if($letter > 0)
        {                                  
            $results = Letter::orderBy('date', 'desc')->paginate(10);
        }
        return view('frontend.letters', compact('results', 'letter', 'title'));
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
        $title = "LETTER TO THE PEOPLE OF GOD";
        $letter = Letter::all()->count();
        if($letter > 0)
        {                       
            /*
            $result = DB::table('letters')
                       ->join('letters_attachments', 'letters.id', '=', 'letters_attachments.postId')
                       ->select('letters.id','letters.date', 'letters.title', 'letters.body', 'letters.slug')
                       ->where('letters.date', $date)
                       ->where('letters.slug', $slug)
                       ->first();
                       
            $attachments = DB::table('letters')
                       ->join('letters_attachments', 'letters.id', '=', 'letters_attachments.postId')
                       ->select('letters_attachments.fileCategoryId', 'letters_attachments.name', 'letters_attachments.filePosition')
                       ->where('letters.date', $date)
                       ->where('letters.slug', $slug)
                       ->orderBy('letters_attachments.id', 'asc')
                       ->get();
                       */
                       $result = Letter::select('id','date', 'title', 'body', 'slug')
                       ->where('date', $date)
                       ->where('slug', $slug)
                       ->first();
                       
                       $attachments = DB::table('letters')
                       ->join('letters_attachments', 'letters.id', '=', 'letters_attachments.postId')
                       ->select('letters_attachments.fileCategoryId', 'letters_attachments.name', 'letters_attachments.filePosition')
                       ->where('letters.date', $date)
                       ->where('letters.slug', $slug)
                       ->orderBy('letters_attachments.id', 'asc')
                       ->get();
        }
        return view('frontend.letterView', compact('result', 'letter', 'attachments', 'title'));
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
