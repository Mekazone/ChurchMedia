<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\PastoralLetter;
use App\PastoralLettersAttachment;

class PastoralLettersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "PASTORAL LETTERS";
        $pastoralLetter = PastoralLetter::all()->count();
        if($pastoralLetter > 0)
        {                                  
            $results = DB::table('pastoral_letters')->join('pastoral_letters_attachments','pastoral_letters.id','=','pastoral_letters_attachments.postId')
                        ->select('pastoral_letters.date','pastoral_letters.title','pastoral_letters.body','pastoral_letters.slug','pastoral_letters_attachments.name')
                        ->where('pastoral_letters_attachments.filePosition','top')
                        ->orderBy('pastoral_letters.date', 'desc')->limit(4)->paginate(10);
            //$results = PastoralLetter::orderBy('date', 'desc')->paginate(10);
        }
        return view('frontend.pastoralLetters', compact('results', 'pastoralLetter', 'title'));
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
        $title = "PASTORAL LETTERS";
        $pastoralLetter = PastoralLetter::all()->count();
        if($pastoralLetter > 0)
        {                       
            /*$result = DB::table('pastoral_letters')
                       ->join('pastoral_letters_attachments', 'pastoral_letters.id', '=', 'pastoral_letters_attachments.postId')
                       ->select('pastoral_letters.id','pastoral_letters.date', 'pastoral_letters.title', 'pastoral_letters.body', 'pastoral_letters.slug')
                       ->where('pastoral_letters.date', $date)
                       ->where('pastoral_letters.slug', $slug)
                       ->first();
                       */
            $result = PastoralLetter::select('id','date', 'title', 'body', 'slug')
                       ->where('date', $date)
                       ->where('slug', $slug)
                       ->first();
                       
            $attachments = DB::table('pastoral_letters')
                       ->join('pastoral_letters_attachments', 'pastoral_letters.id', '=', 'pastoral_letters_attachments.postId')
                       ->select('pastoral_letters_attachments.fileCategoryId', 'pastoral_letters_attachments.name', 'pastoral_letters_attachments.filePosition')
                       ->where('pastoral_letters.date', $date)
                       ->where('pastoral_letters.slug', $slug)
                       ->orderBy('pastoral_letters_attachments.id', 'asc')
                       ->get();
        }
        return view('frontend.pastoralLettersView', compact('result', 'pastoralLetter', 'attachments', 'title'));
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
