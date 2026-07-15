<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\StatsService;

class DashboardController extends Controller
{
    public function __construct(private StatsService $stats) {}

    public function index()
    {
        $stats = $this->stats->adminStats();

        $recentContacts = ContactMessage::where('status', 'new')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentContacts'));
    }
}
