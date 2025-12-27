<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Refer;
use App\Models\Stock;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArticleService
{
    public function paginateArticles(int $perPage = 20): LengthAwarePaginator
    {
        return Article::paginate($perPage);
    }

    public function createArticle(array $data): Article
    {
        return Article::create($data);
    }

    public function updateArticle(int $id, array $data): bool
    {
        $article = Article::findOrFail($id);
        return $article->update($data);
    }

    public function deleteArticle(int $id): bool
    {
        $article = Article::findOrFail($id);
        return $article->delete();
    }

    public function findArticle(int $id): Article
    {
        return Article::findOrFail($id);
    }

    public function filterArticles(?int $referId, ?string $name, ?string $type, ?string $code): Collection
    {
        $query = Article::query();
        
        if ($name) {
            $query->where('name', 'LIKE', "%{$name}%");
        }
        
        if ($type) {
            $query->where('type', 'LIKE', "%{$type}%");
        }
        
        if ($code) {
            $query->where('code', 'LIKE', "%{$code}%");
        }
        
        $articles = $query->orderBy('name')->get();
        
        if ($referId) {
            $this->addStockInfo($articles, $referId);
        }
        
        $this->addUnitNames($articles);
        
        return $articles;
    }

    private function addStockInfo(Collection $articles, int $referId): void
    {
        $refer = Refer::with('originStockCenter')->find($referId);
        
        if (!$refer || !$refer->originStockCenter) {
            return;
        }
        
        $stockCenterId = $refer->originStockCenter->id;
        $articleIds = $articles->pluck('id');
        
        $stocks = Stock::where('id_stockcenter', $stockCenterId)
            ->whereIn('id_article', $articleIds)
            ->get()
            ->keyBy('id_article');
        
        $articles->each(function ($article) use ($stocks) {
            $article->stock = $stocks->get($article->id)?->quantity ?? "-";
        });
    }

    private function addUnitNames(Collection $articles): void
    {
        $articles->each(function ($article) {
            $article->unit_name = $article->unit_name;
        });
    }
}