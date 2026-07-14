<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = ContactMessage::when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q
                ->where('first_name', 'like', "%{$s}%")
                ->orWhere('last_name',  'like', "%{$s}%")
                ->orWhere('email',      'like', "%{$s}%")
                ->orWhere('subject',    'like', "%{$s}%")
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'new'     => ContactMessage::where('status', 'new')->count(),
            'read'    => ContactMessage::where('status', 'read')->count(),
            'replied' => ContactMessage::where('status', 'replied')->count(),
            'closed'  => ContactMessage::where('status', 'closed')->count(),
        ];

        return view('admin.contact_us', compact('messages', 'counts'));
    }

    public function show(ContactMessage $contactMessage)
    {
        if ($contactMessage->status === 'new') {
            $contactMessage->update(['status' => 'read']);
        }

        return view('admin.contact_show', compact('contactMessage'));
    }

    public function reply(Request $request, ContactMessage $contactMessage)
    {
        $request->validate(['admin_reply' => 'required|string']);

        $contactMessage->update([
            'admin_reply' => $request->admin_reply,
            'status'      => 'replied',
            'replied_at'  => now(),
        ]);

        return back()->with('success', 'Reply saved.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->update(['status' => 'closed']);

        return back()->with('success', 'Message closed.');
    }
}
