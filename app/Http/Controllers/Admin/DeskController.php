<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\BishopsDesk;
use App\BishopsDeskAttachment;
use Image;

class DeskController extends Controller
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
        return View('admin.desk');
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
         
         $overview = $this->validate(request(), $rules);
        
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
                    
                    $attachment = BishopsDeskAttachment::create([
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
                    
                    $attachment = BishopsDeskAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => 1,
                        'slug' => $request->slug,
                        'name' => $imagePath
                    ]);
            }
        }  
        
        $create = BishopsDesk::create([
            'title' => $request->title,
            'body' => $request->body,
            'slug' => $request->slug
        ]); 
        return back()->with('success', 'Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        //display results if exists, else display form
        $bishop = BishopsDesk::where('slug', $slug)->count();
        if($bishop > 0)
        {
            $results = BishopsDesk::where('slug', $slug)->first();
            $topImages = BishopsDeskAttachment::where('filePosition', 'top')->where('slug', $slug)->get();
            $bottomImages = BishopsDeskAttachment::where('filePosition', 'bottom')->where('slug', $slug)->get();
        }
        return view('admin.'.$slug, compact('results', 'bishop', 'topImages', 'bottomImages', 'slug'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $results = BishopsDesk::where('slug', $slug)->first();
        $topImages = BishopsDeskAttachment::where('filePosition', 'top')->where('slug', $slug)->get();
        $bottomImages = BishopsDeskAttachment::where('filePosition', 'bottom')->where('slug', $slug)->get();
        return view('admin.deskEdit', compact('results', 'topImages', 'bottomImages', 'slug'));
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
         
         $bishop = $this->validate(request(), $rules);
        
        //delete pevious images (db and files)
        $images = BishopsDeskAttachment::where('fileCategoryId', 1)->where('slug', $slug)->get();        
        foreach($images as $image)
        {
            $file = $image->name;
            Storage::delete($file);
        }
        $images = BishopsDeskAttachment::where('fileCategoryId', 1)->where('slug', $slug);
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
                    
                    $attachment = BishopsDeskAttachment::create([
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
                    
                    $attachment = BishopsDeskAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => 1,
                        'slug' => $request->slug,
                        'name' => $imagePath
                    ]);                
            }
        }   
        //update
        $bishop = BishopsDesk::where('slug', $slug)->first();
        $bishop->title = $request->title;
        $bishop->body = $request->body;
        $bishop->slug = $request->slug;
        $bishop->save();
        return redirect('/admin/desk/'.$slug)->with('success', 'Created');
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