<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class MediaLibraryController extends Controller
{
    public function __invoke(): View
    {
        return view('back.pages.media.library', [
            'pageTitle' => 'Media Library',
        ]);
    }
}
