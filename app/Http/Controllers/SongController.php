<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SongModel;

class SongController extends Controller
{
    public function getSongs()
    {
        $data = SongModel::get();
        return $data;
    }
}
