<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use App\Video;
use App\VideosAttachment;
use App\Subscriber;
use Image;

class VideosController extends Controller
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
        $videos = Video::all()->count();
        if($videos > 0)
        {                                  
            $results = Video::orderBy('date', 'desc')->paginate(10);
        }
        return view('admin.videos', compact('results', 'videos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.videosCreate');
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
        
        $create = Video::create([
            
            'date' => $date,
            'title' => $request->title,
            'body' => $request->body,
            'category' => 'videos',
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
        
                $destinationPath = storage_path('app/public/videos');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/videos/'.$date);
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
                $imagePath = 'public/videos/'.$date.'/'.$fileName;
                    
                    $attachment = VideosAttachment::create([
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
        
                $destinationPath = storage_path('app/public/videos');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/videos/'.$date);
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
                $imagePath = 'public/videos/'.$date.'/'.$fileName;
                    
                    $attachment = VideosAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => $request->fileTypeBottom[$key],
                        'slug' => $request->slug,
                        'name' => $imagePath,
                        'postId' => $create->id
                    ]);
            }
        } 
        
        /*
        //inform all registered members about message
        //get number of members that mail was sent to
		$mail_sent = 0;
        $count = Subscriber::select('email')->count();
        if($count > 0){
			//pear mail with smtp
            require_once "/home/knowandn/php/Mail.php";
            require_once "/home/knowandn/etc/articles/article_pass.php";
            require_once "/home/knowandn/php/Mail/mime.php";
            
            //include mail header and footer
			//require_once "includes/article_mail_header.php";
			//require_once "includes/article_mail_footer.php";
            
            $from = "$sender_name <$sender_email>";
            $subject = "$blog_title";
            
            $text_version = "Copy link to browser to view: $main_link/view_article.php?article_id=$blog_id";
            $html_version = "
					       $header
					       <h4>$blog_title</h4>
					       <p><a href='$main_link/view_article.php?article_id=$blog_id'>Click to view article.</a></p>
					       $footer";
                           
            $crlf = "\n";
            $host = $sender_host;
            $username = $sender_email;
            $password = $article_pass;

            $subscribers = Subscriber::select('email')->get();
            foreach($subscribers as $subscriber)
			{
				$email = $subscriber->email;
 
                // recipients info
                $to = "<$email>";
                //$to = "$first_name $last_name <test@knowandnet.com>";

                $headers = array ('From' => $from,   'To' => $to,   'Subject' => $subject);
                
                $mime = new Mail_mime(array('eol' => $crlf));
                $mime->setTXTBody($text_version);
                $mime->setHTMLBody($html_version);
                
                $body = $mime->get();
                $headers = $mime->headers($headers);
                
                $smtp = Mail::factory('smtp',   array ('host' => $host,     'auth' => true,     'username' => $username,     'password' => $password));
                $mail = $smtp->send($to, $headers, $body);

                if (PEAR::isError($mail)) 
                {   
                    echo("<p>" . $mail->getMessage() . "</p>");  
                } 
                else
                {
                    //increment members
                    $mail_sent++;
                }
			}
		} 
        */
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
        $videos = Video::all()->count();
        if($videos > 0)
        {                       
            $result = DB::table('videos')
                       ->join('videos_attachments', 'videos.id', '=', 'videos_attachments.postId')
                       ->select('videos.id','videos.date', 'videos.title', 'videos.body', 'videos.slug')
                       ->where('videos.date', $date)
                       ->where('videos.slug', $slug)
                       ->first();
                       
            $attachments = DB::table('videos')
                       ->join('videos_attachments', 'videos.id', '=', 'videos_attachments.postId')
                       ->select('videos_attachments.fileCategoryId', 'videos_attachments.name', 'videos_attachments.filePosition')
                       ->where('videos.date', $date)
                       ->where('videos.slug', $slug)
                       ->orderBy('videos_attachments.id', 'asc')
                       ->get();
        }
        return view('admin.videosView', compact('result', 'videos', 'attachments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $results = Video::where('id', $id)->first();
        $day = date('d', $results->date);
        $month = date('m', $results->date);
        $year = date('Y', $results->date);
        $attachments = VideosAttachment::where('postId', $id)->get();
        return view('admin.videosEdit', compact('results', 'attachments', 'id', 'day', 'month', 'year'));
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
        $images = VideosAttachment::where('postId', $id)->get();        
        foreach($images as $image)
        {
            $file = $image->name;
            Storage::delete($file);
        }
        
        $images = VideosAttachment::where('postId', $id);
        $images->delete();

        //upload photos and insert into db
        if($request->topAttachments)
        {
            ini_set('memory_limit','128M');
            foreach($request->topAttachments as $key => $val)
            {
                //fileCategoryId (1 = image, 2 = doc)
                $fileName = time() . rand(000, 999) . '.' . $val->getClientOriginalExtension();
        
                $destinationPath = storage_path('app/public/videos');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/videos/'.$date);
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
                $imagePath = 'public/videos/'.$date.'/'.$fileName;
                    
                    $attachment = VideosAttachment::create([
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
        
                $destinationPath = storage_path('app/public/videos');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath);
                }
                $destinationPath = storage_path('app/public/videos/'.$date);
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
                $imagePath = 'public/videos/'.$date.'/'.$fileName;
                    
                    $attachment = VideosAttachment::create([
                        'filePosition' => 'bottom',
                        'fileCategoryId' => $request->fileTypeBottom[$key],
                        'slug' => $request->slug,
                        'name' => $imagePath,
                        'postId' => $id
                    ]);
            }
        }  
        //update db
        $update = Video::where('id', $id)->first();
        $update->date = $date;
        $update->title = $request->title;
        $update->body = $request->body;
        $update->slug = $request->slug;
        $update->save();
        
        return redirect('/admin/videos')->with('success', 'Edit successful');
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