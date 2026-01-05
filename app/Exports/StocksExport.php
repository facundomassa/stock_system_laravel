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
use PhpOffice\PhpSpreadsheet\Style\Border;

class StocksExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents, WithCustomStartCell
{
    private array $filters;
    private ReportService $reportService;
    private array $summary;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->reportService = app(ReportService::class);
        $this->summary = $this->reportService->getReportSummary($filters, 'stock');
    }

    public function collection()
    {
        return $this->reportService->getStockReport($this->filters);
    }

    public function startCell(): string
    {
        return 'A8'; // Dejamos espacio para el encabezado
    }

    public function headings(): array
    {
        return [
            'ID',
            'Centro de Stock',
            'Artículo',
            'Código',
            'Tipo',
            'Cantidad',
            'Alerta',
            'Unidad',
            'Estado',
            'Última Actualización'
        ];
    }

    public function map($stock): array
    {
        return [
            $stock->id,
            $stock->stockCenter->name ?? 'N/A',
            $stock->article->name ?? 'N/A',
            $stock->article->code ?? '',
            $stock->article->type ?? '',
            $stock->quantity,
            $stock->quantity_alert ?? 0,
            $stock->article->unit_name ?? '',
            $stock->warning ? 'ALERTA' : 'OK',
            $stock->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function title(): string
    {
        return 'Reporte de Stock';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->addReportHeader($event);
                $this->applyStyles($event);
            },
        ];
    }

    private function addReportHeader(AfterSheet $event): void
    {
        $event->sheet->mergeCells('A1:J1');
        $event->sheet->setCellValue('A1', 'REPORTE DE STOCK');
        $event->sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $event->sheet->mergeCells('A2:J2');
        $event->sheet->setCellValue('A2', 'Generado: ' . $this->summary['generated_at'] . ' por ' . $this->summary['generated_by']);
        $event->sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Filtros aplicados
        $row = 3;
        if (!empty($this->filters)) {
            $event->sheet->mergeCells('A3:J3');
            $event->sheet->setCellValue('A3', 'Filtros aplicados:');
            $event->sheet->getStyle('A3')->applyFromArray([
                'font' => ['bold' => true],
            ]);
            
            $row = 4;
            foreach ($this->filters as $key => $value) {
                if (!empty($value)) {
                    $event->sheet->mergeCells("A{$row}:J{$row}");
                    $event->sheet->setCellValue("A{$row}", ucfirst($key) . ': ' . (is_array($value) ? implode(', ', $value) : $value));
                    $row++;
                }
            }
        }

        // Resumen
        $event->sheet->mergeCells("A{$row}:J{$row}");
        $event->sheet->setCellValue("A{$row}", 'Resumen:');
        $event->sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true],
        ]);
        
        $row++;
        $event->sheet->mergeCells("A{$row}:J{$row}");
        $event->sheet->setCellValue("A{$row}", "Total registros: {$this->summary['total_records']} | Stock total: {$this->summary['total_quantity']} | Alertas: {$this->summary['alerts_count']}");
    }

    private function applyStyles(AfterSheet $event): void
    {
        // Estilo del encabezado de la tabla
        $headerRange = 'A8:J8';
        $event->sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '4F81BD'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Estilo de las filas de datos
        $dataRange = 'A9:J' . (8 + $this->summary['total_records']);
        $event->sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Resaltar alertas
        $highestRow = $event->sheet->getHighestRow();
        for ($row = 9; $row <= $highestRow; $row++) {
            $status = $event->sheet->getCell("I{$row}")->getValue();
            if ($status === 'ALERTA') {
                $event->sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFE5E5'],
                    ],
                ]);
            }
        }

        // Congelar encabezado
        $event->sheet->freezePane('A9');
    }
}