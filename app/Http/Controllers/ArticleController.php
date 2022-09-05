<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected static $tittle = 'Articulos';

    private static $rules = array(
        'name' => 'required|string|max:60',
        'unit' => 'required|string|max:1',
        'type' => 'nullable|string|max:30',
        'code' => 'nullable|string|max:16'
    );
    private static $message = array(
        'required' => 'El :attribute es requerido',
        'max' => 'El :attribute no puedo tener mas de :max caracteres'
    );

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
        $request['unit'] = Article::validateUnit($request['unit']);

        $this->validate($request, static::$rules, static::$message);

        $dataArticle = request()->except('_token');

        Article::create($dataArticle);
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
        $request['unit'] = Article::validateUnit($request['unit']);

        $this->validate($request, static::$rules, static::$message);

        $dataArticle = request()->except(['_token', '_method']);

        Article::find($id)->update($dataArticle);
        return redirect('article')->with('mensaje', 'Articulo editado con exito')->with('tittle', static::$tittle);
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

    public function filters()
    {
        //
        $nameTx = request()->get('nameTx');
        $typeTx = request()->get('typeTx');
        $codeTx = request()->get('codeTx');

        $dataArticle = Article::Name($nameTx)
            ->Type($typeTx)
            ->Code($codeTx)
            ->orderBy('name', 'asc')
            ->get();
        foreach ($dataArticle as $key => $value) {
            $dataArticle[$key]->UnitName;
        }
        // $data->UnitName();
        return with($dataArticle);
    }
}
