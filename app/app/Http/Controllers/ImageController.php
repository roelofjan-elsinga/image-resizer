<?php

namespace App\Http\Controllers;

use claviska\SimpleImage;
use Illuminate\Http\Request;

class ImageController extends Controller {

    public function __construct() {
    }

    public function convert() {

        $request = Request::capture();

        $arguments = $request->only(['url', 'format', 'sizeX', 'sizeY']);

        if(is_null($arguments['url']) || empty($arguments['url'])) {
            return response()->json(['error' => 'Missing image'], 400);
        }

        list($width, $height) = getimagesize($arguments['url']);

        $new_width = isset($arguments['sizeX']) && !is_null($arguments['sizeX']) && !empty($arguments['sizeX']) ? $arguments['sizeX'] : $width;
        $new_height = isset($arguments['sizeY']) && !is_null($arguments['sizeY']) && !empty($arguments['sizeY']) ? $arguments['sizeY'] : $height;

        $manipulation = new SimpleImage();

        try {
            $image = $manipulation
                ->fromString(file_get_contents($arguments['url']))
                ->thumbnail($new_width, $new_height, 'center')
                ->toScreen("image/{$arguments['format']}", 87);

            return response()->file($image)->withHeaders(['Cache-Control:public, max-age=3000000000000']);

        } catch(\Exception $exception) {
            dd($exception);
        }

    }

}