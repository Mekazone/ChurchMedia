<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Donate;
use App\DonateAttachment;

class DonateController extends Controller
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
        //display results if exists, else display form
        $slug = 'donate';
        $donate = Donate::where('slug', $slug)->count();
        if($donate > 0)
        {
            $results = Donate::where('slug', $slug)->first();
            $topImage = DonateAttachment::where('filePosition', 'top')->where('slug', $slug)->get();
            $bottomAttachment = DonateAttachment::where('filePosition', 'bottom')->where('slug', $slug)->get();
        }
        return view('admin.'.$slug, compact('results', 'donate', 'topImage', 'bottomAttachment', 'slug'));
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
        //validation rules
         $rules = [
            'title' => 'required',
            'body' => 'required'
         ];
         
         $topPhotos = count($request->topImages);
         foreach(range(0, $topPhotos) as $topImages)
         {
            //$rules['topImages.' . $topImages] = 'image|mimes:jpeg,bmp,png|max:2000';
         }
         
         $bottomPhotos = count($request->bottomAttachments);
         foreach(range(0, $bottomPhotos) as $bottomAttachments)
         {
            //$rules['bottomAttachments.' . $bottomAttachments] = 'image|mimes:jpeg,bmp,png|max:2000';
         }
         
         $overview = $this->validate(request(), $rules);
        
        //upload photos and insert into db
        if($request->topImages)
        {
            ini_set('memory_limit','128M');
            foreach($request->topImages as $photos)
            {
                //fileCategoryId (1 = image, 2 = doc)
                $filename = $photos->store('public/'.$request->slug);
                
                $attachment = DonateAttachment::create([
                    'filePosition' => 'top',
                    'fileCategoryId' => 1,
                    'slug' => $request->slug,
                    'name' => $filename
                ]);             
            }
        } 
        
        if($request->bottomAttachments)
        {
            ini_set('memory_limit','128M');
            foreach($request->bottomAttachments as $key=>$val)
            {
                $store = $val->store('public/donate/attachment');
                DonateAttachment::create([
                    'filePosition' => 'bottom',
                    'fileCategoryId' => $request->fileTypeBottom[$key],
                    'slug' => $request->slug,
                    'name' => $store
                ]);
            }
        }  
        
        $createOverview = Donate::create([
            'title' => $request->title,
            'body' => $request->body,
            'slug' => $request->slug
        ]); 
        return back()->with('success', 'Overview has been created');
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
    public function edit($slug)
    {
        $results = Donate::where('slug', $slug)->first();
        $topImages = DonateAttachment::where('filePosition', 'top')->where('slug', $slug)->get();
        $bottomImages = DonateAttachment::where('filePosition', 'bottom')->where('slug', $slug)->get();
        return view('admin.donateEdit', compact('results', 'topImages', 'bottomImages', 'slug'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $rules = [
            'title' => 'required',
            'body' => 'required'
         ];
         
         $topPhotos = count($request->topImages);
         foreach(range(0, $topPhotos) as $topImages)
         {
            //$rules['topImages.' . $topImages] = 'image|mimes:jpeg,bmp,png|max:2000';
         }
         
         $bottomPhotos = count($request->bottomAttachments);
         foreach(range(0, $bottomPhotos) as $bottomAttachments)
         {
            //$rules['bottomAttachments.' . $bottomAttachments] = 'image|mimes:jpeg,bmp,png|max:2000';
         }
         
         $donate = $this->validate(request(), $rules);
        
        //delete pevious images (db and files)
        $images = DonateAttachment::where('slug', $slug)->get();        
        foreach($images as $image)
        {
            $file = $image->name;
            Storage::delete($file);
        }
        $images = DonateAttachment::where('slug', $slug);
        $images->delete();
        //upload photos and insert into db
        if($request->topImages)
        {
            ini_set('memory_limit','128M');
            foreach($request->topImages as $photos)
            {
                //fileCategoryId (1 = image, 2 = doc)
                $filename = $photos->store('public/'.$request->slug.'Photos');
                DonateAttachment::create([
                    'filePosition' => 'top',
                    'fileCategoryId' => 1,
                    'slug' => $request->slug,
                    'name' => $filename
                ]);
            }
        } 
        
        if($request->bottomAttachments)
        {
            ini_set('memory_limit','128M');
            foreach($request->bottomAttachments as $key => $val)
            {
                $store = $val->store('public/donate/attachment');
                DonateAttachment::create([
                    'filePosition' => 'bottom',
                    'fileCategoryId' => $request->fileTypeBottom[$key],
                    'slug' => $request->slug,
                    'name' => $store
                ]);
            }
        }  
         //update
        $donate = Donate::where('slug', $slug)->first();
        $donate->title = $request->title;
        $donate->body = $request->body;
        $donate->slug = $request->slug;
        $donate->save(); 
        return redirect('/admin/'.$slug)->with('success', 'Edit successful');
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
