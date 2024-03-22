<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\Podcast;
use App\PodcastsAttachment;
use Image;

class PodcastsController extends Controller
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
        $podcasts = Podcast::all()->count();
        if($podcasts > 0)
        {                                  
            $results = Podcast::orderBy('date', 'desc')->paginate(10);
        }
        return view('admin.podcasts', compact('results', 'podcasts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.podcastsCreate');
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
        
        $create = Podcast::create([      
            'date' => $date,
            'title' => $request->title,
            'body' => $request->body,
            'category' => 'podcasts',
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
        
                $destinationPath = storage_path('app/public/podcasts');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/podcasts/'.$date);
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
                $imagePath = 'public/podcasts/'.$date.'/'.$fileName;
                    
                    $attachment = PodcastsAttachment::create([
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
        
                $destinationPath = storage_path('app/public/podcasts');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/podcasts/'.$date);
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
                $imagePath = 'public/podcasts/'.$date.'/'.$fileName;
                    
                    $attachment = PodcastsAttachment::create([
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
        $podcasts = Podcast::all()->count();
        if($podcasts > 0)
        {                       
            $result = DB::table('podcasts')
                       ->join('podcasts_attachments', 'podcasts.id', '=', 'podcasts_attachments.postId')
                       ->select('podcasts.id','podcasts.date', 'podcasts.title', 'podcasts.body', 'podcasts.slug')
                       ->where('podcasts.date', $date)
                       ->where('podcasts.slug', $slug)
                       ->first();
                       
            $attachments = DB::table('podcasts')
                       ->join('podcasts_attachments', 'podcasts.id', '=', 'podcasts_attachments.postId')
                       ->select('podcasts_attachments.fileCategoryId', 'podcasts_attachments.name', 'podcasts_attachments.filePosition')
                       ->where('podcasts.date', $date)
                       ->where('podcasts.slug', $slug)
                       ->orderBy('podcasts_attachments.id', 'asc')
                       ->get();
        }
        return view('admin.podcastsView', compact('result', 'podcasts', 'attachments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $results = Podcast::where('id', $id)->first();
        $day = date('d', $results->date);
        $month = date('m', $results->date);
        $year = date('Y', $results->date);
        $attachments = PodcastsAttachment::where('postId', $id)->get();
        return view('admin.podcastsEdit', compact('results', 'attachments', 'id', 'day', 'month', 'year'));
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
        $images = PodcastsAttachment::where('postId', $id)->get();        
        foreach($images as $image)
        {
            $file = $image->name;
            Storage::delete($file);
        }
        
        $images = PodcastsAttachment::where('postId', $id);
        $images->delete();

        //upload photos and insert into db
        if($request->topAttachments)
        {
            ini_set('memory_limit','128M');
            foreach($request->topAttachments as $key => $val)
            {
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $val->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/podcasts');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/podcasts/'.$date);
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
                $imagePath = 'public/podcasts/'.$date.'/'.$fileName;
                    
                    $attachment = PodcastsAttachment::create([
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
        
                $destinationPath = storage_path('app/public/podcasts');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/podcasts/'.$date);
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
                $imagePath = 'public/podcasts/'.$date.'/'.$fileName;
                    
                    $attachment = PodcastsAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => $request->fileTypeBottom[$key],
                        'slug' => $request->slug,
                        'name' => $imagePath,
                        'postId' => $id
                    ]);
            }
        }
        
        //update db
        $update = Podcast::where('id', $id)->first();
        $update->date = $date;
        $update->title = $request->title;
        $update->body = $request->body;
        $update->slug = $request->slug;
        $update->save();
          
        return redirect('/admin/podcasts')->with('success', 'Edit successful');
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