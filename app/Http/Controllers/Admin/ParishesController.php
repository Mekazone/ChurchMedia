<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Parish;
use App\ParishAttachment;
use Image;

class ParishesController extends Controller
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
        $slug = 'parishes';
        $parishes = Parish::where('slug', $slug)->count();
        if($parishes > 0)
        {
            $results = Parish::where('slug', $slug)->first();
            $topImages = ParishAttachment::where('filePosition', 'top')->where('slug', $slug)->get();
            $bottomImages = ParishAttachment::where('filePosition', 'bottom')->where('slug', $slug)->get();
        }
        return view('admin.'.$slug, compact('results', 'parishes', 'topImages', 'bottomImages', 'slug'));
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
         
         $parish = $this->validate(request(), $rules);
        
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
                    
                    $attachment = ParishAttachment::create([
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
                    
                    $attachment = ParishAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => 1,
                        'slug' => $request->slug,
                        'name' => $imagePath
                    ]);
            }
        }  
        
        $createParish = Parish::create([
            'title' => $request->title,
            'body' => $request->body,
            'slug' => $request->slug
        ]); 
        return back()->with('success', 'Parish has been created');
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
        $results = Parish::where('slug', $slug)->first();
        $topImages = ParishAttachment::where('filePosition', 'top')->where('slug', $slug)->get();
        $bottomImages = ParishAttachment::where('filePosition', 'bottom')->where('slug', $slug)->get();
        return view('admin.ParishesEdit', compact('results', 'topImages', 'bottomImages', 'slug'));
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
         
         $parish = $this->validate(request(), $rules);
        
        //delete pevious images (db and files)
        $images = ParishAttachment::where('fileCategoryId', 1)->where('slug', $slug)->get();        
        foreach($images as $image)
        {
            $file = $image->name;
            Storage::delete($file);
        }
        $images = ParishAttachment::where('fileCategoryId', 1)->where('slug', $slug);
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
                    
                    $attachment = ParishAttachment::create([
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
                    
                    $attachment = ParishAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => 1,
                        'slug' => $request->slug,
                        'name' => $imagePath
                    ]);
            }
        }   
        
        //update
        $parish = Parish::where('slug', $slug)->first();
        $parish->title = $request->title;
        $parish->body = $request->body;
        $parish->slug = $request->slug;
        $parish->save();
        return redirect('/admin/'.$slug)->with('success', 'Parish has been created');
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