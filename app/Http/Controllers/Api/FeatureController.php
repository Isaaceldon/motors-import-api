<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feature;

class FeatureController extends Controller
{
    public function index()
    {
        return Feature::orderBy('name')->get(['id', 'name', 'slug']);
    }
}
