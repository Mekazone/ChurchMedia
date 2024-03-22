<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\Opinion;
use App\OpinionsAttachment;
use Image;

class OpinionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $opinions = Opinion::all()->count();
        if($opinions > 0)
        {                                  
            $results = Opinion::orderBy('date', 'desc')->paginate(10);
        }
        return view('admin.opinions', compact('results', 'opinions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.opinionsCreate');
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
            'day' => 'required',
            'month' => 'required',
            'year' => 'required',
            'title' => 'required',
            'body' => 'required',
            'slug' => 'required',
            'topAttachments' => 'required',
            'fileTypeTop' => 'required'
         ];
         
         $topPhotos = count($request->topAttachments);
         foreach(range(0, $topPhotos) as $topAttachments)
         {
            //$rules['topAttachments.' . $topAttachments] = 'image|mimes:jpeg,bmp,png|max:4000';
         }
         
         $bottomPhotos = count($request->bottomAttachments);
         foreach(range(0, $bottomPhotos) as $bottomAttachments)
         {
            //$rules['bottomAttachments.' . $bottomAttachments] = 'image|mimes:jpeg,bmp,png|max:4000';
         }
         
         $overview = $this->validate(request(), $rules);
        
        $date = mktime(0,0,0,$request->month,$request->day,$request->year);
        
        $create = Opinion::create([
            
            'date' => $date,
            'title' => $request->title,
            'body' => $request->body,
            'category' => 'opinions',
            'slug' => $request->slug
        ]);
        
        //upload photos and insert into db
        if($request->topAttachments)
        {
            ini_set('memory_limit','128M');
            foreach($request->topAttachments as $key => $val)
            {    
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $val->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/Opinions');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/Opinions/'.$date);
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                //resize image if width is greater than 620 
                $img = Image::make($val->getRealpath());
                if ($img->width() > 620) {
                    $img->resize(620, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                //save
                $store = $img->save($destinationPath . '/' .$fileName);
                $imagePath = 'public/Opinions/'.$date.'/'.$fileName;
                    
                    $attachment = OpinionsAttachment::create([
                        'filePosition' => 'top',
                        'fileCategoryId' => $request->fileTypeTop[$key],
                        'slug' => $request->slug,
                        'name' => $imagePath,
                        'postId' => $create->id
                    ]);            
            }
        } 
        
        if($request->bottomAttachments)
        {
            ini_set('memory_limit','128M');
            foreach($request->bottomAttachments as $key => $val)
            {
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $val->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/Opinions');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/Opinions/'.$date);
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                //resize image if width is greater than 620 
                $img = Image::make($val->getRealpath());
                if ($img->width() > 620) {
                    $img->resize(620, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                //save
                $store = $img->save($destinationPath . '/' .$fileName);
                $imagePath = 'public/Opinions/'.$date.'/'.$fileName;
                    
                    $attachment = OpinionsAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => $request->fileTypeBottom[$key],
                        'slug' => $request->slug,
                        'name' => $imagePath,
                        'postId' => $create->id
                    ]);
            }
        }  
        
        return back()->with('success', 'Created');
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
        $opinions = Opinion::all()->count();
        if($opinions > 0)
        {                       
            $result = DB::table('opinions')
                       ->join('opinions_attachments', 'opinions.id', '=', 'opinions_attachments.postId')
                       ->select('opinions.id','opinions.date', 'opinions.title', 'opinions.body', 'opinions.slug')
                       ->where('opinions.date', $date)
                       ->where('opinions.slug', $slug)
                       ->first();
                       
            $attachments = DB::table('opinions')
                       ->join('opinions_attachments', 'opinions.id', '=', 'opinions_attachments.postId')
                       ->select('opinions_attachments.fileCategoryId', 'opinions_attachments.name', 'opinions_attachments.filePosition')
                       ->where('opinions.date', $date)
                       ->where('opinions.slug', $slug)
                       ->orderBy('opinions_attachments.id', 'asc')
                       ->get();
        }
        return view('admin.opinionsView', compact('result', 'opinions', 'attachments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $results = Opinion::where('id', $id)->first();
        $day = date('d', $results->date);
        $month = date('m', $results->date);
        $year = date('Y', $results->date);
        $attachments = OpinionsAttachment::where('postId', $id)->get();
        return view('admin.opinionsEdit', compact('results', 'attachments', 'id', 'day', 'month', 'year'));
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
        //validation rules
         $rules = [
            'day' => 'required',
            'month' => 'required',
            'year' => 'required',
            'title' => 'required',
            'body' => 'required',
            'slug' => 'required',
            'topAttachments' => 'required',
            'fileTypeTop' => 'required'
         ];
         
         $topPhotos = count($request->topAttachments);
         foreach(range(0, $topPhotos) as $topAttachments)
         {
            //$rules['topAttachments.' . $topAttachments] = 'image|mimes:jpeg,bmp,png|max:4000';
         }
         
         $bottomPhotos = count($request->bottomAttachments);
         foreach(range(0, $bottomPhotos) as $bottomAttachments)
         {
            //$rules['bottomAttachments.' . $bottomAttachments] = 'image|mimes:jpeg,bmp,png|max:4000';
         }
         
         $overview = $this->validate(request(), $rules);
        
        $date = mktime(0,0,0,$request->month,$request->day,$request->year);
        
        //delete pevious images, directory (db and files)
        $images = OpinionsAttachment::where('postId', $id)->get();        
        foreach($images as $image)
        {
            $file = $image->name;
            Storage::delete($file);
        }
        
        $images = OpinionsAttachment::where('postId', $id);
        $images->delete();

        //upload photos and insert into db
        if($request->topAttachments)
        {
            ini_set('memory_limit','128M');
            foreach($request->topAttachments as $key => $val)
            {
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $val->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/Opinions');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/Opinions/'.$date);
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                //resize image if width is greater than 620 
                $img = Image::make($val->getRealpath());
                if ($img->width() > 620) {
                    $img->resize(620, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                //save
                $store = $img->save($destinationPath . '/' .$fileName);
                $imagePath = 'public/Opinions/'.$date.'/'.$fileName;
                    
                    $attachment = OpinionsAttachment::create([
                        'filePosition' => 'top',
                        'fileCategoryId' => $request->fileTypeTop[$key],
                        'slug' => $request->slug,
                        'name' => $imagePath,
                        'postId' => $id
                    ]);               
            }
        } 
        
        if($request->bottomAttachments)
        {
            ini_set('memory_limit','128M');
            foreach($request->bottomAttachments as $key => $val)
            {
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $val->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/Opinions');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/Opinions/'.$date);
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                //resize image if width is greater than 620 
                $img = Image::make($val->getRealpath());
                if ($img->width() > 620) {
                    $img->resize(620, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                //save
                $store = $img->save($destinationPath . '/' .$fileName);
                $imagePath = 'public/Opinions/'.$date.'/'.$fileName;
                    
                    $attachment = OpinionsAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => $request->fileTypeBottom[$key],
                        'slug' => $request->slug,
                        'name' => $imagePath,
                        'postId' => $id
                    ]);
            }
        }  
        
        //update db
        $update = Opinion::where('id', $id)->first();
        $update->date = $date;
        $update->title = $request->title;
        $update->body = $request->body;
        $update->slug = $request->slug;
        $update->save();
        
        return redirect('/admin/opinions')->with('success', 'Edit successful');
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
