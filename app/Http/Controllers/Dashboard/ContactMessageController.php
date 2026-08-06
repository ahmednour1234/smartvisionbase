<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(15);
        return view('dashboard.contacts.index', compact('messages'));
    }

    public function show(ContactMessage $contactMessage)
    {
        return view('dashboard.contacts.show', compact('contactMessage'));
    }
}


