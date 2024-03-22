<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\homeGallery;

class homeGalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gallery = homeGallery::all()->count();
        if($gallery > 0)
        {                                  
            $results = homeGallery::orderBy('created_at', 'asc')->get();
        }
        return view('admin.homeGallery', compact('results', 'gallery'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.homeGalleryCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
         //validation rules
         $rules = [
            'image' => 'required',
            'title' => 'required'
         ];
         $overview = $this->validate(request(), $rules);
         
         //upload and create in db
         ini_set('memory_limit','128M');
         $store = $request->image->store('public/homeGallery/'.$request->title);
         $create = homeGallery::create([
            'title' => $request->title,
            'name' => $store
        ]);
        
        return back()->with('success', 'Created');
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
        //delete pevious image and db record)
        $image = homeGallery::where('id', $id)->first();        
        $file = $image->name;
        Storage::delete($file);
        
        $images = homeGallery::where('id', $id);
        $images->delete();
        return back()->with('success', 'Success');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
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
