<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\BishopEvent;
use App\BishopEventsAttachment;

class BishopEventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "BISHOP'S EVENTS";
        $events = BishopEvent::all()->count();
        if($events > 0)
        {                                  
            $results = DB::table('bishop_events')->join('bishop_events_attachments','bishop_events.id','=','bishop_events_attachments.postId')
                        ->select('bishop_events.date','bishop_events.title','bishop_events.body','bishop_events.slug','bishop_events_attachments.name')
                        ->where('bishop_events_attachments.filePosition','top')
                        ->orderBy('bishop_events.date', 'desc')->limit(4)->paginate(10);
            //$results = BishopEvent::orderBy('date', 'desc')->paginate(10);
        }
        return view('frontend.bishopEvents', compact('results', 'events', 'title'));
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
        $title = "BISHOP'S EVENTS";
        $events = BishopEvent::all()->count();
        if($events > 0)
        {                       
            $result = DB::table('bishop_events')
                       ->join('bishop_events_attachments', 'bishop_events.id', '=', 'bishop_events_attachments.postId')
                       ->select('bishop_events.id','bishop_events.date', 'bishop_events.title', 'bishop_events.body', 'bishop_events.slug')
                       ->where('bishop_events.date', $date)
                       ->where('bishop_events.slug', $slug)
                       ->first();
                       
            $attachments = DB::table('bishop_events')
                       ->join('bishop_events_attachments', 'bishop_events.id', '=', 'bishop_events_attachments.postId')
                       ->select('bishop_events_attachments.fileCategoryId', 'bishop_events_attachments.name', 'bishop_events_attachments.filePosition')
                       ->where('bishop_events.date', $date)
                       ->where('bishop_events.slug', $slug)
                       ->orderBy('bishop_events_attachments.id', 'asc')
                       ->get();
        }
        return view('frontend.bishopEventsView', compact('result', 'events', 'attachments', 'title'));
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
