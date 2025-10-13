<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    /**
     * Display the blog index page
     */
    public function index()
    {
        return Inertia::render('Web/Blog/Index');
    }

    /**
     * Display the about page
     */
    public function about()
    {
        return Inertia::render('Web/Blog/About');
    }

    /**
     * Display the vision page
     */
    public function vision()
    {
        return Inertia::render('Web/Blog/Vision');
    }

    /**
     * Display the policy page
     */
    public function policy()
    {
        return Inertia::render('Web/Blog/Policy');
    }
}
