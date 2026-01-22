<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ArticleController extends Controller
{
    protected string $title = 'Artículos';
    protected ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(): View
    {
        $articles = $this->articleService->paginateArticles();
        return view('article.index', compact('articles'))->with('title', $this->title);
    }

    public function create(): View
    {
        return view('article.create')->with('title', $this->title);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        $this->articleService->createArticle($request->validated());
        return redirect()->route('article.index')
            ->with('success', 'Artículo agregado con éxito')
            ->with('title', $this->title);
    }

    public function show(int $id): View
    {
        $article = $this->articleService->findArticle($id);
        return view('article.show', compact('article'))->with('title', $this->title);
    }

    public function edit(int $id): View
    {
        $article = $this->articleService->findArticle($id);
        return view('article.edit', compact('article'))->with('title', $this->title);
    }

    public function update(ArticleRequest $request, int $id): RedirectResponse
    {
        $this->articleService->updateArticle($id, $request->validated());
        return redirect()->route('article.index')
            ->with('success', 'Artículo editado con éxito')
            ->with('title', $this->title);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->articleService->deleteArticle($id);
        return redirect()->route('article.index')
            ->with('success', 'Artículo eliminado')
            ->with('title', $this->title);
    }

    public function filters(): JsonResponse
    {
        $articles = $this->articleService->filterArticles(
            referId: request('refer'),
            name: request('nameTx'),
            type: request('typeTx'),
            code: request('codeTx')
        );

        return response()->json($articles);
    }
}