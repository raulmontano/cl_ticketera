<?php

namespace App\Http\Controllers;

use App\Idea;
use App\Rules\ValidRepository;
use Illuminate\Support\Facades\Storage;
use App\Attachment;

class AttachmentsController extends Controller
{
    public function show(Attachment $attachment)
    {

        $class = (new \ReflectionClass($attachment->attachable))->getShortName();

        $basepath = strtolower($class).'_'.$attachment->attachable->id;

        return Storage::download("$basepath/{$attachment->path}");
    }
}
