<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Consecrated;
use App\ConsecratedAttachment;
use Image;

class ConsecratedController extends Controller
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
        $slug = 'consecrated';
        $consecrated = Consecrated::where('slug', $slug)->count();
        if($consecrated > 0)
        {
            $results = Consecrated::where('slug', $slug)->first();
            $topImages = ConsecratedAttachment::where('filePosition', 'top')->where('slug', $slug)->get();
            $bottomImages = ConsecratedAttachment::where('filePosition', 'bottom')->where('slug', $slug)->get();
        }
        return view('admin.'.$slug, compact('results', 'consecrated', 'topImages', 'bottomImages', 'slug'));
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
         
         $bottomPhotos = count($request->bottomImages);
         foreach(range(0, $bottomPhotos) as $bottomImages)
         {
            //$rules['bottomImages.' . $bottomImages] = 'image|mimes:jpeg,bmp,png|max:2000';
         }
         
         $Consecrated = $this->validate(request(), $rules);
        
        //upload photos and insert into db
        if($request->topImages)
        {
            ini_set('memory_limit','128M');
            foreach($request->topImages as $photos)
            {
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $photos->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/'.$request->slug.'Photos');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                //resize image if width is greater than 620 
                $img = Image::make($photos->getRealpath());
                if ($img->width() > 620) {
                    $img->resize(620, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                //save
                $store = $img->save($destinationPath . '/' .$fileName);
                $imagePath = 'public/'.$request->slug.'Photos/'.$fileName;
                    
                    $attachment = ConsecratedAttachment::create([
                        'filePosition' => 'top',
                        'fileCategoryId' => 1,
                        'slug' => $request->slug,
                        'name' => $imagePath
                    ]);               
            }
        } 
        
        if($request->bottomImages)
        {
            ini_set('memory_limit','128M');
            foreach($request->bottomImages as $photos)
            {
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $photos->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/'.$request->slug.'Photos');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                //resize image if width is greater than 620 
                $img = Image::make($photos->getRealpath());
                if ($img->width() > 620) {
                    $img->resize(620, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                //save
                $store = $img->save($destinationPath . '/' .$fileName);
                $imagePath = 'public/'.$request->slug.'Photos/'.$fileName;
                    
                    $attachment = ConsecratedAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => 1,
                        'slug' => $request->slug,
                        'name' => $imagePath
                    ]);
            }
        }  
        
        $createConsecrated = Consecrated::create([
            'title' => $request->title,
            'body' => $request->body,
            'slug' => $request->slug
        ]); 
        return back()->with('success', 'Consecrated has been created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //return View('admin.cathcom');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $results = Consecrated::where('slug', $slug)->first();
        $topImages = ConsecratedAttachment::where('filePosition', 'top')->where('slug', $slug)->get();
        $bottomImages = ConsecratedAttachment::where('filePosition', 'bottom')->where('slug', $slug)->get();
        return view('admin.consecratedEdit', compact('results', 'topImages', 'bottomImages', 'slug'));
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
         
         $bottomPhotos = count($request->bottomImages);
         foreach(range(0, $bottomPhotos) as $bottomImages)
         {
            //$rules['bottomImages.' . $bottomImages] = 'image|mimes:jpeg,bmp,png|max:2000';
         }
         
         $consecrated = $this->validate(request(), $rules);
        
        //delete pevious images (db and files)
        $images = ConsecratedAttachment::where('fileCategoryId', 1)->where('slug', $slug)->get();        
        foreach($images as $image)
        {
            $file = $image->name;
            Storage::delete($file);
        }
        $images = ConsecratedAttachment::where('fileCategoryId', 1)->where('slug', $slug);
        $images->delete();
        //upload photos and insert into db
        if($request->topImages)
        {
            ini_set('memory_limit','128M');
            foreach($request->topImages as $photos)
            {
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $photos->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/'.$request->slug.'Photos');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                //resize image if width is greater than 620 
                $img = Image::make($photos->getRealpath());
                if ($img->width() > 620) {
                    $img->resize(620, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                //save
                $store = $img->save($destinationPath . '/' .$fileName);
                $imagePath = 'public/'.$request->slug.'Photos/'.$fileName;
                    
                    $attachment = ConsecratedAttachment::create([
                        'filePosition' => 'top',
                        'fileCategoryId' => 1,
                        'slug' => $request->slug,
                        'name' => $imagePath
                    ]);
            }
        } 
        
        if($request->bottomImages)
        {
            ini_set('memory_limit','128M');
            foreach($request->bottomImages as $photos)
            {
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $photos->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/'.$request->slug.'Photos');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                //resize image if width is greater than 620 
                $img = Image::make($photos->getRealpath());
                if ($img->width() > 620) {
                    $img->resize(620, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                //save
                $store = $img->save($destinationPath . '/' .$fileName);
                $imagePath = 'public/'.$request->slug.'Photos/'.$fileName;
                    
                    $attachment = ConsecratedAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => 1,
                        'slug' => $request->slug,
                        'name' => $imagePath
                    ]);
            }
        }   
         //update
        $consecrated = Consecrated::where('slug', $slug)->first();
        $consecrated->title = $request->title;
        $consecrated->body = $request->body;
        $consecrated->slug = $request->slug;
        $consecrated->save();
        return redirect('/admin/'.$slug)->with('success', 'Consecrated has been created');
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
