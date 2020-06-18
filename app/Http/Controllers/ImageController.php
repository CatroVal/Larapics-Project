<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Image;

class ImageController extends Controller
{
    //Con este metodo el acceso queda restringido solo a usarios identificados
    public function __construct() {
        $this->middleware('auth');
    }

    public function create() {
        return view('image.create');
    }

    public function save(Request $request) {
        //Validacion
        $validate = $this->validate($request, [
            'description' => 'required|max:255',
            'image_path' => 'required|mimes:jpeg,jpg,png,gif'
        ]);

        //Recoger datos
        $image_path = $request->file('image_path');
        $description = $request->input('description');

        //Asignar valores al nuevo objeto
        //Setear el id del usuario que sube la imagen
        $user = \Auth::user();
        $image = new Image();
        $image->user_id = $user->id;
        $image->description = $description;

        //Subir archivo
        if($image_path) {
            $image_path_name = time().$image_path->getClientOriginalName();
            Storage::disk('images')->put($image_path_name, File::get($image_path));
            $image->image_path = $image_path_name;
        }
        $image->save();

        return redirect()->route('home')->with([
            'message' => 'La imagen se ha subido correctamente'
        ]);
    }

    public function getImage($filename) {
        $file = Storage::disk('images')->get($filename);

        return new Response($file, 200);
    }

    public function detail($id) {
        $image = Image::find($id);

        return view('image/detail', [
            'image' => $image
        ]);
    }

    public function delete($id) {
        $user = \Auth::user();
        $image = Image::find($id);

        if($user && $image && $image->user_id == $user->id) {
            //Eliminar los likes
            $image->likes()->delete();
            //Eliminar los Comentarios
            $image->comments()->delete();
            //Eliminar la imagen
            $image->delete();
            //Eliminar archivo del disco
            Storage::disk('images')->delete($image->image_path);

            $message = array('message' => 'La imagen se ha borrado correctamente');
        } else {
            $message = array('message' => 'La imagen no se ha podido eliminar');

        }
        return redirect()->route('home')->with($message);
    }

    public function edit($id) {
        $user = \Auth::user();
        $image = Image::find($id);

        if($user && $image && $image->user->id == $user->id) {
            return view('image.edit', [
                'image' => $image
            ]);
        } else {
            return redirect()->route('home');
        }
    }

    public function update(Request $request) {
        //Validacion
        $validate = $this->validate($request, [
            'description' => 'required|max:100',
            'image_path' => 'mimes:jpeg,jpg,png,gif'
        ]);

        //Recoger datos del formulario
        $image_id = $request->input('image_id');
        $image_path = $request->file('image_path');
        $description = $request->input('description');

        //Conseguir objeto "image" para setear los nuevos datos
        $image = Image::find($image_id);
        $image->description = $description;

        //Subir archivo
        if($image_path) {
            $image_path_name = time().$image_path->getClientOriginalName();
            Storage::disk('images')->put($image_path_name, File::get($image_path));
            $image->image_path = $image_path_name;
        }

        //Actualizar registro
        $image->update();

        return redirect()->route('image.detail', ['id' => $image_id])
                         ->with(['message' => 'Publicación actualizada correctamente']);
    }
}
