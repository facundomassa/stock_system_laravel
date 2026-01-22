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

class ReportRpFySExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents, WithCustomStartCell
{
    private array $filters;
    private ReportService $reportService;
    private array $summary;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->reportService = app(ReportService::class);
        $this->summary = $this->reportService->getReportSummary($filters, 'consumption');
    }

    public function collection()
    {
        return $this->reportService->getConsumptionReport($this->filters);
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function headings(): array
    {
        return [
            'ID Artículo',
            'Código',
            'Artículo',
            'Tipo',
            'Unidad',
            'Cantidad Total Consumida',
            'Última Actualización'
        ];
    }

    public function map($item): array
    {
        return [
            $item->id_article,
            $item->article->code ?? '',
            $item->article->name ?? 'N/A',
            $item->article->type ?? '',
            $item->article->unit_name ?? '',
            $item->total_quantity,
            $item->article->updated_at->format('d/m/Y H:i') ?? '',
        ];
    }

    public function title(): string
    {
        return 'Consumo por Artículo';
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
        $event->sheet->mergeCells('A1:G1');
        $event->sheet->setCellValue('A1', 'REPORTE DE CONSUMO POR ARTÍCULO');
        $event->sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $event->sheet->mergeCells('A2:G2');
        $event->sheet->setCellValue('A2', 'Generado: ' . $this->summary['generated_at'] . ' por ' . $this->summary['generated_by']);
        $event->sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Filtros aplicados
        $row = 3;
        if (!empty($this->filters)) {
            $event->sheet->mergeCells('A3:G3');
            $event->sheet->setCellValue('A3', 'Filtros aplicados:');
            $event->sheet->getStyle('A3')->applyFromArray([
                'font' => ['bold' => true],
            ]);
            
            $row = 4;
            foreach ($this->filters as $key => $value) {
                if (!empty($value) && !in_array($key, ['date_start', 'date_end'])) {
                    $label = match($key) {
                        'check_origin' => 'Orígenes:',
                        'check_destiny' => 'Destinos:',
                        default => ucfirst($key) . ':'
                    };
                    
                    $event->sheet->mergeCells("A{$row}:G{$row}");
                    $event->sheet->setCellValue("A{$row}", $label . ' ' . (is_array($value) ? implode(', ', $value) : $value));
                    $row++;
                }
            }
            
            if (!empty($this->filters['date_start']) && !empty($this->filters['date_end'])) {
                $event->sheet->mergeCells("A{$row}:G{$row}");
                $event->sheet->setCellValue("A{$row}", 'Período: ' . $this->filters['date_start'] . ' - ' . $this->filters['date_end']);
                $row++;
            }
        }

        // Resumen
        $event->sheet->mergeCells("A{$row}:G{$row}");
        $event->sheet->setCellValue("A{$row}", "Total artículos: {$this->summary['total_records']} | Consumo total: {$this->summary['total_quantity']}");
    }

    private function applyStyles(AfterSheet $event): void
    {
        $headerRange = 'A8:G8';
        $event->sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '00B050'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Formato para cantidades
        $dataRange = 'A9:G' . (8 + $this->summary['total_records']);
        $event->sheet->getStyle("F9:F" . (8 + $this->summary['total_records']))->applyFromArray([
            'numberFormat' => [
                'formatCode' => '#,##0'
            ],
        ]);

        // Congelar encabezado
        $event->sheet->freezePane('A9');
    }
}