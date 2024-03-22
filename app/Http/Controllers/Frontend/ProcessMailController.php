<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Requests\EnquiryFormRequest;

class ProcessMailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(EnquiryFormRequest $request)
    {
        //$to = "info@hanjors.com.ng";
        $to = "preachlove2000@yahoo.co.uk";
        $receipient = "Catholic Diocese of Nnewi";
        
        $data = array(
                'to' => $to,
                'receipient' => $receipient,
                'title' => "New message from " . $request->get('name') . " via www.nnewidiocese.org",
				'name' => $request->get('name'),
				'email' => $request->get('email'),
				'phone'=>$request->get('phone'),
				'body' => $request->get('message')
		);
        
        Mail::send('emails.contactMail', $data, function($message) use ($data) {
            $message->to($data['to'], $data['receipient'])
                    ->from($data['email'], $data['name'])
                    ->replyTo($data['email'])
                    ->subject($data['title']);
        });
        dd('message sent');
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
        //
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
        //
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
