<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMessageRequest;
use App\Models\AboutUs;
use App\Models\ContactMessage;

class PageController extends Controller
{
    public function about()
    {
        $about = AboutUs::active();
        abort_if(!$about, 404);
        return view('site.about', compact('about'));
    }

    public function contact()
    {
        return view('site.contact');
    }

    public function sendContact(ContactMessageRequest $request)
    {
        ContactMessage::create($request->validated());
        return redirect()->route('contact')->with('success', 'Message sent successfully.');
    }
}
