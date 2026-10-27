<?php

namespace Tests\Feature;

use App\Models\Movement;
use App\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesStockScenario;
use Tests\TestCase;

class ReferCancelStockTest extends TestCase
{
    use RefreshDatabase;
    use CreatesStockScenario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpStockScenario();
    }

    public function test_cancelar_un_remito_emitido_deja_el_stock_como_antes_de_emitir()
    {
        $deposit = $this->createStockcenter('Depósito Central', 'D');
        $mobile = $this->createStockcenter('Móvil 3', 'M');
        $cable = $this->createArticle('Cable UTP', 'C001');
        $connector = $this->createArticle('Conector RJ45', 'R001');

        $this->setStock($deposit, $cable, 100);
        $this->setStock($deposit, $connector, 40);
        $this->setStock($mobile, $cable, 5);

        $refer = $this->createRefer($deposit, $mobile, [[$cable, 30], [$connector, 10]]);

        $this->actingAsDispatcher()
            ->get("/refer/emited/{$refer->id}")
            ->assertRedirect(route('refer.index'))
            ->assertSessionHas('success', 'Remito emitido');

        $this->assertSame('E', $refer->fresh()->status);
        $this->assertSame(70, $this->stockOf($deposit, $cable));
        $this->assertSame(30, $this->stockOf($deposit, $connector));
        $this->assertSame(5, $this->stockOf($mobile, $cable));
        $this->assertTrue(Movement::where('id_refer', $refer->id)->get()->every->transit);

        $this->actingAsDispatcher()
            ->delete("/refer/{$refer->id}")
            ->assertRedirect(route('refer.index'))
            ->assertSessionHas('success', 'Remito cancelado');

        $this->assertSame('C', $refer->fresh()->status);

        // El origen recupera lo que se descontó al emitir.
        $this->assertSame(100, $this->stockOf($deposit, $cable));
        $this->assertSame(40, $this->stockOf($deposit, $connector));

        // El destino no recibe nada: ni se modifica ni se crea stock nuevo.
        $this->assertSame(5, $this->stockOf($mobile, $cable));
        $this->assertNull($this->stockOf($mobile, $connector));

        $this->assertFalse(Movement::where('id_refer', $refer->id)->get()->contains->transit);
    }

    public function test_cancelar_un_remito_emitido_desde_un_proveedor_no_toca_el_stock()
    {
        $supplier = $this->createStockcenter('Proveedor', 'P');
        $deposit = $this->createStockcenter('Depósito Central', 'D');
        $cable = $this->createArticle('Cable UTP', 'C001');

        $this->setStock($deposit, $cable, 10);

        $refer = $this->createRefer($supplier, $deposit, [[$cable, 50]]);

        $this->actingAsDispatcher()->get("/refer/emited/{$refer->id}");
        $this->assertSame('E', $refer->fresh()->status);

        $this->actingAsDispatcher()
            ->delete("/refer/{$refer->id}")
            ->assertSessionHas('success', 'Remito cancelado');

        $this->assertSame('C', $refer->fresh()->status);
        $this->assertSame(10, $this->stockOf($deposit, $cable));
        $this->assertSame(0, Stock::where('id_stockcenter', $supplier->id)->count());
    }

    public function test_finalizar_un_remito_emitido_sigue_acreditando_el_destino()
    {
        $deposit = $this->createStockcenter('Depósito Central', 'D');
        $mobile = $this->createStockcenter('Móvil 3', 'M');
        $cable = $this->createArticle('Cable UTP', 'C001');

        $this->setStock($deposit, $cable, 100);
        $this->setStock($mobile, $cable, 5);

        $refer = $this->createRefer($deposit, $mobile, [[$cable, 30]]);

        $this->actingAsDispatcher()->get("/refer/emited/{$refer->id}");
        $this->actingAsDispatcher()
            ->patch("/refer/finalized/{$refer->id}")
            ->assertSessionHas('success', 'Remito finalizado');

        $this->assertSame('F', $refer->fresh()->status);
        $this->assertSame(70, $this->stockOf($deposit, $cable));
        $this->assertSame(35, $this->stockOf($mobile, $cable));
        $this->assertFalse(Movement::where('id_refer', $refer->id)->get()->contains->transit);
    }
}
