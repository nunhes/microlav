<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;

use Spatie\YamlFrontMatter\YamlFrontMatter;

class Post {

    public $title;

    public $excerpt;

    public $date;

    public $body;
    
    public $slug;

    public function __construct($title, $excerpt, $date, $body, $slug)
    {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
        $this->slug = $slug;
    }

    public static function all(){

        // catchin forever
        return cache()->rememberForever('posts.all', function () {

        // collection shorting (isto funciona sen cache, pero coa cache eceleramos o acceso)
        return collect(File::files(resource_path('posts')))
        ->map(fn($file) => YamlFrontMatter::parseFile($file))
        ->map(fn($document) => new Post(
                        $document->title,
                        $document->excerpt,
                        $document->date,
                        $document->body(),
                        $document->slug
                ))
                -> sortByDesc('date');

                /// remate
            });

    }

    public static function find($slug)
    {
    // if(! file_exists($path =resource_path("posts/{$slug}.html"))){
    //     throw new ModelNotFoundException(); 
    // }

    // return cache()->remember("posts.{$slug}", 1200, fn() => file_get_contents($path));

    // de tódolos artigos do blog, atopar o(s) que coincidadn co termo da busca (12)
    return static::all()->firstWhere('slug',$slug);
 
    }
}