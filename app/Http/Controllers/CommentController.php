<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;

class CommentController extends Controller
{
    //Con este metodo el acceso queda restringido solo a usarios identificados
    public function __construct() {
        $this->middleware('auth');
    }

    public function save(Request $request) {

        //Validacion
        $validate = $this->validate($request, [
            'image_id' => 'integer|required',
            'content' => 'string|required'
        ]);
        //Recoger datos del formulario
        $user = \Auth::user();
        $image_id = $request->input('image_id');
        $content = $request->input('content');

        //Asigno valores al nuevo objeto a guardar
        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->image_id = $image_id;
        $comment->content = $content;

        //Guardar en la BBDD
        $comment->save();

        //Redireccion
        return redirect()->route('image.detail', ['id' => $image_id])
                         ->with([
                             'message' => 'Tu comentario se ha publicado correctamente'
                         ]);
    }

    public function delete($id) {//El id que se le pasa es el id del comentario
        //Conseguir los datos del usuario logueado
        $user = \Auth::user();
        //Conseguir objeto del comentario
        $comment = Comment::find($id);
        //Comprobar si soy el dueño del comentario o de la publicacion
        if($user && ($comment->user_id == $user->id || $comment->image->id == $user->id)) {
            $comment->delete();

            //Redireccion
            return redirect()->route('image.detail', ['id' => $comment->image_id])
                             ->with([
                                 'message' => 'Comentario eliminado correctamente'
                             ]);
        } else {
            return redirect()->route('image.detail', ['id' => $comment->image_id])
                             ->with([
                                 'message' => 'EL COMENTARIO NO SE HA ELIMINADO!!'
                             ]);
        }
    }

}
