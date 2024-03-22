<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Consecrated;
use App\ConsecratedAttachment;

class ConsecratedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //display results if exists, else display form
        $title = "CONSECRATED MEN AND WOMEN";
        $slug = 'consecrated';
        $consecrated = Consecrated::where('slug', $slug)->count();
        if($consecrated > 0)
        {
            $results = Consecrated::where('slug', $slug)->first();
            $topImages = ConsecratedAttachment::where('filePosition', 'top')->where('slug', $slug)->get();
            $bottomImages = ConsecratedAttachment::where('filePosition', 'bottom')->where('slug', $slug)->get();
        }
        return view('frontend.'.$slug, compact('results', 'consecrated', 'topImages', 'bottomImages', 'slug', 'title'));
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
