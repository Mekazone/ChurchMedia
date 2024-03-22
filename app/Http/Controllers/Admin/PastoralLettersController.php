<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\PastoralLetter;
use App\PastoralLettersAttachment;
use Image;

class PastoralLettersController extends Controller
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
        $pastoralLetter = PastoralLetter::all()->count();
        if($pastoralLetter > 0)
        {                                  
            $results = PastoralLetter::orderBy('date', 'desc')->paginate(10);
        }
        return view('admin.pastoralLetters', compact('results', 'pastoralLetter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pastoralLettersCreate');
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
            //'topAttachments' => 'required',
            //'fileTypeTop' => 'required'
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
                
        $create = PastoralLetter::create([
            
            'date' => $date,
            'title' => $request->title,
            'body' => $request->body,
            'category' => 'pastoralLetters',
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
        
                $destinationPath = storage_path('app/public/pastoralLetters');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/pastoralLetters/'.$date);
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
                $imagePath = 'public/pastoralLetters/'.$date.'/'.$fileName;
                    
                    $attachment = PastoralLettersAttachment::create([
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
        
                $destinationPath = storage_path('app/public/pastoralLetters');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/pastoralLetters/'.$date);
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
                $imagePath = 'public/pastoralLetters/'.$date.'/'.$fileName;
                    
                    $attachment = PastoralLettersAttachment::create([
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
        $pastoralLetter = PastoralLetter::all()->count();
        if($pastoralLetter > 0)
        {                       
            /*$result = DB::table('pastoral_letters')
                       ->join('pastoral_letters_attachments', 'pastoral_letters.id', '=', 'pastoral_letters_attachments.postId')
                       ->select('pastoral_letters.id','pastoral_letters.date', 'pastoral_letters.title', 'pastoral_letters.body', 'pastoral_letters.slug')
                       ->where('pastoral_letters.date', $date)
                       ->where('pastoral_letters.slug', $slug)
                       ->first();
                       */
            $result = PastoralLetter::select('id','date', 'title', 'body', 'slug')
                       ->where('date', $date)
                       ->where('slug', $slug)
                       ->first();
                       
            $attachments = DB::table('pastoral_letters')
                       ->join('pastoral_letters_attachments', 'pastoral_letters.id', '=', 'pastoral_letters_attachments.postId')
                       ->select('pastoral_letters_attachments.fileCategoryId', 'pastoral_letters_attachments.name', 'pastoral_letters_attachments.filePosition')
                       ->where('pastoral_letters.date', $date)
                       ->where('pastoral_letters.slug', $slug)
                       ->orderBy('pastoral_letters_attachments.id', 'asc')
                       ->get();
        }
        return view('admin.pastoralLettersView', compact('result', 'pastoralLetter', 'attachments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $results = PastoralLetter::where('id', $id)->first();
        $day = date('d', $results->date);
        $month = date('m', $results->date);
        $year = date('Y', $results->date);
        $attachments = PastoralLettersAttachment::where('postId', $id)->get();
        return view('admin.pastoralLettersEdit', compact('results', 'attachments', 'id', 'day', 'month', 'year'));
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
            //'topAttachments' => 'required',
            //'fileTypeTop' => 'required'
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
        $images = PastoralLettersAttachment::where('postId', $id)->get();        
        foreach($images as $image)
        {
            $file = $image->name;
            Storage::delete($file);
        }
        
        $images = PastoralLettersAttachment::where('postId', $id);
        $images->delete();

        //upload photos and insert into db
        if($request->topAttachments)
        {
            ini_set('memory_limit','128M');
            foreach($request->topAttachments as $key => $val)
            {  
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $val->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/pastoralLetters');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/pastoralLetters/'.$date);
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
                $imagePath = 'public/pastoralLetters/'.$date.'/'.$fileName;
                    
                    $attachment = PastoralLettersAttachment::create([
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
        
                $destinationPath = storage_path('app/public/pastoralLetters');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/pastoralLetters/'.$date);
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
                $imagePath = 'public/pastoralLetters/'.$date.'/'.$fileName;
                    
                    $attachment = PastoralLettersAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => $request->fileTypeBottom[$key],
                        'slug' => $request->slug,
                        'name' => $imagePath,
                        'postId' => $id
                    ]);
            }
        } 
        
        //update db
        $update = PastoralLetter::where('id', $id)->first();
        $update->date = $date;
        $update->title = $request->title;
        $update->body = $request->body;
        $update->slug = $request->slug;
        $update->save();
         
        return redirect('/admin/pastoralLetters')->with('success', 'Edit successful');
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