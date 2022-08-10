<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected static $tittle = 'Articulos';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['articles'] = Article::paginate(20);
        return view('article/index')->with($data)->with('tittle', static::$tittle);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('article/create')->with('tittle', static::$tittle);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $data = [
            'name' => 'required|string|max:60',
            'unit' => 'required|string|max:1'
        ];
        if($request->type != ""){
            $data['type'] = 'string|max:30';
        }
        if($request->code != ""){
            $data['code'] = 'string|max:16';
        }
        $message = [
            'required' => 'El :attribute es requerido',
            'max' => 'El :attribute no puedo tener mas de :max caracteres'
        ];

        $this->validate($request, $data, $message);

        $dataArticle = request()->except('_token');

        Article::insert($dataArticle);
        return redirect('article')->with('mensaje', 'Articulo agregado con exito')->with('tittle', static::$tittle);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $article = Article::findOrFail($id);
        return view('article.show', compact('article'))->with('tittle', static::$tittle);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $article = Article::findOrFail($id);
        return view('article.edit', compact('article'))->with('tittle', static::$tittle);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $data = [
            'name' => 'required|string|max:60',
            'unit' => 'required|string|max:1'
        ];
        if($request->type != ""){
            $data['type'] = 'string|max:30';
        }
        if($request->code != ""){
            $data['code'] = 'string|max:16';
        }
        $message = [
            'required' => 'El :attribute es requerido',
            'max' => 'El :attribute no puedo tener mas de :max caracteres'
        ];

        $this->validate($request, $data, $message);

        $dataArticle = request()->except(['_token', '_method']);

        Article::where('id', '=', $id)->update($dataArticle);

        return redirect('article')->with('mensaje', 'Articulo agregado con exito')->with('tittle', static::$tittle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $article = Article::findOrFail($id);

        Article::destroy($id);

        return redirect('article')->with('mensaje', 'Articulo eliminada')->with('tittle', static::$tittle);
    }
}
