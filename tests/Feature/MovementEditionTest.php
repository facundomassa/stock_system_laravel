<?php

namespace Tests\Feature;

use App\Models\Movement;
use App\Models\Refer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesStockScenario;
use Tests\TestCase;

class MovementEditionTest extends TestCase
{
    use RefreshDatabase;
    use CreatesStockScenario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpStockScenario();
    }

    public function test_no_se_pueden_guardar_movimientos_de_un_remito_emitido()
    {
        [$refer, $movement, $cable, $connector, $deposit] = $this->emittedRefer();

        $this->actingAsDispatcher()
            ->post('/movement', [
                'id_refer' => $refer->id,
                $cable->id => ['id' => $movement->id, 'id_article' => $cable->id, 'quantity' => 80],
                $connector->id => ['id' => '', 'id_article' => $connector->id, 'quantity' => 5],
            ])
            ->assertRedirect(route('refer.show', $refer->id))
            ->assertSessionHas('error', fn ($message) => str_contains($message, 'EMITIDO'));

        $this->assertSame(30, $movement->fresh()->quantity);
        $this->assertSame(1, Movement::where('id_refer', $refer->id)->count());
        $this->assertSame(70, $this->stockOf($deposit, $cable));
    }

    public function test_no_se_pueden_borrar_movimientos_de_un_remito_emitido()
    {
        [$refer, $movement, $cable] = $this->emittedRefer();

        $this->actingAsDispatcher()
            ->post('/movement', [
                'id_refer' => $refer->id,
                $cable->id => ['id' => $movement->id, 'id_article' => $cable->id, 'quantity' => 30, 'delete' => 'on'],
            ])
            ->assertRedirect(route('refer.show', $refer->id))
            ->assertSessionHas('error');

        $this->assertNotNull($movement->fresh());
    }

    public function test_no_se_puede_abrir_la_carga_de_articulos_de_un_remito_emitido()
    {
        [$refer] = $this->emittedRefer();

        foreach (["/movement/create/{$refer->id}", "/movement/{$refer->id}/edit"] as $url) {
            $this->actingAsDispatcher()
                ->get($url)
                ->assertRedirect(route('refer.show', $refer->id))
                ->assertSessionHas('error');
        }
    }

    public function test_el_detalle_solo_ofrece_editar_articulos_en_remitos_ingresados()
    {
        [$refer] = $this->emittedRefer();

        $this->actingAsDispatcher()
            ->get("/refer/{$refer->id}")
            ->assertOk()
            ->assertDontSee('Editar Articulos');

        $deposit = $this->createStockcenter('Depósito Norte', 'D');
        $mobile = $this->createStockcenter('Móvil 4', 'M');
        $draft = $this->createRefer($deposit, $mobile, []);

        $this->actingAsDispatcher()
            ->get("/refer/{$draft->id}")
            ->assertOk()
            ->assertSee('Editar Articulos');
    }

    public function test_se_pueden_guardar_movimientos_de_un_remito_ingresado()
    {
        $deposit = $this->createStockcenter('Depósito Central', 'D');
        $mobile = $this->createStockcenter('Móvil 3', 'M');
        $cable = $this->createArticle('Cable UTP', 'C001');
        $connector = $this->createArticle('Conector RJ45', 'R001');

        $refer = $this->createRefer($deposit, $mobile, [[$cable, 30]]);
        $movement = Movement::where('id_refer', $refer->id)->first();

        $this->actingAsDispatcher()
            ->get("/movement/create/{$refer->id}")
            ->assertOk();

        $this->actingAsDispatcher()
            ->post('/movement', [
                'id_refer' => $refer->id,
                $cable->id => ['id' => $movement->id, 'id_article' => $cable->id, 'quantity' => 80],
                $connector->id => ['id' => '', 'id_article' => $connector->id, 'quantity' => 5],
            ])
            ->assertRedirect(route('refer.show', $refer->id))
            ->assertSessionMissing('error')
            ->assertSessionHas('mensaje');

        $this->assertSame(80, $movement->fresh()->quantity);
        $this->assertSame(5, Movement::where('id_refer', $refer->id)->where('id_article', $connector->id)->value('quantity'));
    }

    /**
     * Remito Depósito → Móvil con 30 cables, ya emitido (el depósito pasó de 100 a 70).
     */
    private function emittedRefer(): array
    {
        $deposit = $this->createStockcenter('Depósito Central', 'D');
        $mobile = $this->createStockcenter('Móvil 3', 'M');
        $cable = $this->createArticle('Cable UTP', 'C001');
        $connector = $this->createArticle('Conector RJ45', 'R001');

        $this->setStock($deposit, $cable, 100);

        $refer = $this->createRefer($deposit, $mobile, [[$cable, 30]]);

        $this->actingAsDispatcher()
            ->get("/refer/emited/{$refer->id}")
            ->assertSessionHas('success', 'Remito emitido');

        $this->assertSame('E', Refer::find($refer->id)->status);

        $movement = Movement::where('id_refer', $refer->id)->first();

        return [$refer, $movement, $cable, $connector, $deposit];
    }
}
