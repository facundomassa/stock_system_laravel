<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;

class MovementExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents, WithCustomStartCell
{
    private array $filters;
    private ReportService $reportService;
    private array $summary;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->reportService = app(ReportService::class);
        $this->summary = $this->reportService->getReportSummary($filters, 'movement');
    }

    public function collection()
    {
        return $this->reportService->getMovementReport($this->filters);
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function headings(): array
    {
        return [
            'ID Movimiento',
            'N° Remito',
            'Origen',
            'Destino',
            'Artículo',
            'Código',
            'Tipo',
            'Unidad',
            'Cantidad',
            'Fecha Movimiento',
            'Estado Remito',
            'Usuario'
        ];
    }

    public function map($movement): array
    {
        return [
            $movement->id,
            $movement->refer->id ?? 'N/A',
            $movement->refer->name_origin ?? 'N/A',
            $movement->refer->name_destiny ?? 'N/A',
            $movement->article->name ?? 'N/A',
            $movement->article->code ?? '',
            $movement->article->type ?? '',
            $movement->article->unit_name ?? '',
            $movement->quantity,
            $movement->created_at->format('d/m/Y H:i'),
            $movement->refer->status_name ?? 'N/A',
            $movement->refer->full_name_user ?? 'N/A',
        ];
    }

    public function title(): string
    {
        return 'Movimientos';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->addReportHeader($event, 'REPORTE DE MOVIMIENTOS');
                $this->applyStyles($event);
            },
        ];
    }

    private function addReportHeader(AfterSheet $event, string $title): void
    {
        $event->sheet->mergeCells('A1:L1');
        $event->sheet->setCellValue('A1', $title);
        $event->sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $event->sheet->mergeCells('A2:L2');
        $event->sheet->setCellValue('A2', 'Generado: ' . $this->summary['generated_at'] . ' por ' . $this->summary['generated_by']);
        $event->sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Filtros aplicados
        $row = 3;
        if (!empty($this->filters['date_start']) && !empty($this->filters['date_end'])) {
            $event->sheet->mergeCells('A3:L3');
            $event->sheet->setCellValue('A3', 'Período: ' . $this->filters['date_start'] . ' - ' . $this->filters['date_end']);
            $event->sheet->getStyle('A3')->applyFromArray([
                'font' => ['bold' => true],
            ]);
            $row = 4;
        }

        // Resumen
        $event->sheet->mergeCells("A{$row}:L{$row}");
        $event->sheet->setCellValue("A{$row}", "Total movimientos: {$this->summary['total_records']} | Cantidad total: {$this->summary['total_quantity']} | Artículos únicos: {$this->summary['unique_articles']}");
    }

    private function applyStyles(AfterSheet $event): void
    {
        $headerRange = 'A8:L8';
        $event->sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '2E75B6'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Congelar encabezado
        $event->sheet->freezePane('A9');
    }
}