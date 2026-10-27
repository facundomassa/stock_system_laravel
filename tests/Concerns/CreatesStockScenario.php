<?php

namespace Tests\Concerns;

use App\Models\Article;
use App\Models\Direction;
use App\Models\Movement;
use App\Models\Operation;
use App\Models\Refer;
use App\Models\Stock;
use App\Models\Stockcenter;
use App\Models\User;
use Spatie\Permission\Models\Permission;

/**
 * Arma los datos mínimos para operar remitos: una operación con su permiso,
 * centros de stock, artículos, stock inicial y un usuario despachante.
 */
trait CreatesStockScenario
{
    protected Operation $operation;
    protected Direction $direction;
    protected User $user;

    protected function setUpStockScenario(string $operationName = 'POP36'): void
    {
        // Las notificaciones de stock buscan usuarios con un permiso llamado como la operación.
        Permission::findOrCreate($operationName);

        $this->operation = Operation::create(['name' => $operationName]);
        $this->direction = Direction::create([
            'country' => 'Argentina',
            'state' => 'Buenos Aires',
            'city' => 'CABA',
            'locality' => 'Palermo',
            'street' => 'Av. Santa Fe',
            'number' => 1234,
            'cp' => 1425,
        ]);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo($operationName);
    }

    protected function createStockcenter(string $name, string $type): Stockcenter
    {
        return Stockcenter::forceCreate([
            'id_operation' => $this->operation->id,
            'name' => $name,
            'type' => $type,
            'id_direction' => $this->direction->id,
        ]);
    }

    protected function createArticle(string $name, string $code): Article
    {
        return Article::create(['name' => $name, 'code' => $code, 'unit' => 'U', 'type' => 'Red']);
    }

    protected function setStock(Stockcenter $stockcenter, Article $article, int $quantity): Stock
    {
        return Stock::create([
            'id_stockcenter' => $stockcenter->id,
            'id_article' => $article->id,
            'quantity' => $quantity,
        ]);
    }

    protected function stockOf(Stockcenter $stockcenter, Article $article): ?int
    {
        return Stock::where('id_stockcenter', $stockcenter->id)
            ->where('id_article', $article->id)
            ->value('quantity');
    }

    /**
     * Crea un remito ingresado (estado I) con un movimiento por cada [artículo, cantidad].
     *
     * @param  array<int, array{0: Article, 1: int}>  $lines
     */
    protected function createRefer(Stockcenter $origin, Stockcenter $destiny, array $lines): Refer
    {
        $refer = Refer::create([
            'origen_id_stockcenter' => $origin->id,
            'destiny_id_stockcenter' => $destiny->id,
            'date_up' => now(),
            'status' => 'I',
            'id_user' => $this->user->id,
        ]);

        foreach ($lines as [$article, $quantity]) {
            Movement::create([
                'id_refer' => $refer->id,
                'id_article' => $article->id,
                'quantity' => $quantity,
            ]);
        }

        return $refer;
    }

    protected function actingAsDispatcher(): static
    {
        return $this->actingAs($this->user)
            ->withSession(['selected_operation' => $this->operation->name]);
    }
}
