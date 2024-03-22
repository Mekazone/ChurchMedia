<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\BishopEvent;
use App\BishopEventsAttachment;
use Image;

class BishopEventsController extends Controller
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
        $events = BishopEvent::all()->count();
        if($events > 0)
        {                                  
            $results = BishopEvent::orderBy('date', 'desc')->paginate(10);
        }
        return view('admin.bishopEvents', compact('results', 'events'));
    }

    /**
     * Show the form for creating a BishopEvent resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.bishopEventsCreate');
    }

    /**
     * Store a BishopEvently created resource in storage.
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
                
        $create = BishopEvent::create([
            
            'date' => $date,
            'title' => $request->title,
            'body' => $request->body,
            'category' => 'bishopEvents',
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
        
                $destinationPath = storage_path('app/public/bishopEvents');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/bishopEvents/'.$date);
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
                $imagePath = 'public/bishopEvents/'.$date.'/'.$fileName;
                    
                    $attachment = BishopEventsAttachment::create([
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
        
                $destinationPath = storage_path('app/public/bishopEvents');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/bishopEvents/'.$date);
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
                $imagePath = 'public/bishopEvents/'.$date.'/'.$fileName;
                    
                    $attachment = BishopEventsAttachment::create([
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
        return view('admin.bishopEventsView', compact('result', 'events', 'attachments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $results = BishopEvent::where('id', $id)->first();
        $day = date('d', $results->date);
        $month = date('m', $results->date);
        $year = date('Y', $results->date);
        $attachments = BishopEventsAttachment::where('postId', $id)->get();
        return view('admin.bishopEventsEdit', compact('results', 'attachments', 'id', 'day', 'month', 'year'));
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
        $images = BishopEventsAttachment::where('postId', $id)->get();        
        foreach($images as $image)
        {
            $file = $image->name;
            Storage::delete($file);
        }
        
        $images = BishopEventsAttachment::where('postId', $id);
        $images->delete();

        //upload photos and insert into db
        if($request->topAttachments)
        {
            ini_set('memory_limit','128M');
            foreach($request->topAttachments as $key => $val)
            {   
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $val->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/bishopEvents');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/bishopEvents/'.$date);
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
                $imagePath = 'public/bishopEvents/'.$date.'/'.$fileName;
                    
                    $attachment = BishopEventsAttachment::create([
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
        
                $destinationPath = storage_path('app/public/bishopEvents');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/bishopEvents/'.$date);
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
                $imagePath = 'public/bishopEvents/'.$date.'/'.$fileName;
                    
                    $attachment = BishopEventsAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => $request->fileTypeBottom[$key],
                        'slug' => $request->slug,
                        'name' => $imagePath,
                        'postId' => $id
                    ]);
            }
        }
        
        //update db
        $update = BishopEvent::where('id', $id)->first();
        $update->date = $date;
        $update->title = $request->title;
        $update->body = $request->body;
        $update->slug = $request->slug;
        $update->save();
          
        return redirect('/admin/bishopEvents')->with('success', 'Edit successful');
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