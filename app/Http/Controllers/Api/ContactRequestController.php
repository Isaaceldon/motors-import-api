<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use App\Models\User;
use App\Notifications\NewContactRequestAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ContactRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'spare_part_id' => ['nullable', 'exists:spare_parts,id'],
        ]);

        $contactRequest = ContactRequest::create($data);

        Notification::send(User::where('role', 'admin')->get(), new NewContactRequestAdminNotification($contactRequest));

        return response()->json($contactRequest, 201);
    }
}
