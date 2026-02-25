<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* Estilos mínimos */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        /* Encabezado */
        .header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            height: 50px;
            background: #2c3e50;
            color: white;
            padding: 10px 15px 0px 15px;
            border-bottom: 3px solid #3498db;
        }
        
        .header h1 {
            font-size: 16px;
            margin: 0;
            padding: 0;
        }
        
        .header-info {
            font-size: 9px;
            opacity: 0.9;
            margin-top: 3px;
            margin-bottom: 0px;
        }
        
        /* Pie de página - FIJADO ABAJO */
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 30px;
            background: #f8f9fa;
            border-top: 2px solid #dee2e6;
            padding: 6px 15px;
            font-size: 9px;
            color: #666;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer .page:after {
            content: counter(page);
            font-weight: bold;
        }
        
        /* Filtros compactos */
        .filtros {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
            font-size: 10px;
        }
        
        .filtros-titulo {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .filtros-linea {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .filtro-item {
            margin-bottom: 3px;
        }
        
        .filtro-label {
            font-weight: bold;
            color: #555;
        }
        
        /* Tabla compacta */
        .tabla-stock {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 5px;
        }
        
        .tabla-stock thead {
            background: #2c3e50;
            color: white;
        }
        
        .tabla-stock th {
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            border: none;
        }
        
        .tabla-stock td {
            padding: 4px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        
        .tabla-stock tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Estados */
        .alerta {
            background-color: #fff3cd !important;
            border-left: 3px solid #ffc107;
        }
        
        .agotado {
            background-color: #f8d7da !important;
            border-left: 3px solid #dc3545;
        }
        
        .ok {
            background-color: #d4edda !important;
            border-left: 3px solid #28a745;
        }
        
        /* Utilidades */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        
        /* Contenido principal - margen para header y footer */
        .content {
            margin-top: 15px;
            margin-bottom: 20px;
        }
        
        /* Sin borde en última fila */
        .tabla-stock tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <!-- Encabezado fijo -->
    <div class="header">
        <h1>□ Reporte de Stock</h1>
        <div class="header-info">
            Generado: {{ $fecha ?? now()->format('d/m/Y H:i') }} | 
            Usuario: {{ auth()->user()->name ?? 'Sistema' }}
        </div>
    </div>
    
    <!-- Pie de página fijo -->
    <div class="footer">
        <div>Sistema de Stock - Página <span class="page"></span></div>
        <div>{{ date('d/m/Y') }}</div>
    </div>
    
    <!-- Contenido principal -->
    <div class="content">
        <!-- Estadísticas en columnas usando tabla -->
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 15px; border-collapse: collapse;">
            <tr>
                <td width="33%" style="padding: 0 5px;">
                    <div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; border-radius: 4px; text-align: center;">
                        <div style="font-weight: bold; font-size: 14px;">{{ $stats['total'] ?? 0 }}</div>
                        <div style="font-size: 10px; color: #666;">Total Items</div>
                    </div>
                </td>
                <td width="33%" style="padding: 0 5px;">
                    <div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; border-radius: 4px; text-align: center;">
                        <div style="font-weight: bold; font-size: 14px; color: #ffc107;">{{ $stats['alerta'] ?? 0 }}</div>
                        <div style="font-size: 10px; color: #666;">En Alerta</div>
                    </div>
                </td>
                <td width="34%" style="padding: 0 5px;">
                    <div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; border-radius: 4px; text-align: center;">
                        <div style="font-weight: bold; font-size: 14px; color: #dc3545;">{{ $stats['agotado'] ?? 0 }}</div>
                        <div style="font-size: 10px; color: #666;">Agotados</div>
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Filtros aplicados -->
        <div class="filtros">
            <div class="filtros-titulo">Filtros aplicados:</div>
            <div class="filtros-linea">
                @if($filters['stockselect'])
                <div class="filtro-item">
                    <span class="filtro-label">Centro:</span> {{ $filters['stockselect'] }}
                </div>
                @endif
                @if($filters['code'])
                <div class="filtro-item">
                    <span class="filtro-label">Código:</span> {{ $filters['code'] }}
                </div>
                @endif
                @if($filters['articlename'])
                <div class="filtro-item">
                    <span class="filtro-label">Artículo:</span> {{ $filters['articlename'] }}
                </div>
                @endif
                @if($filters['type'])
                <div class="filtro-item">
                    <span class="filtro-label">Tipo:</span> {{ $filters['type'] }}
                </div>
                @endif
            </div>
        </div>
        
        <!-- Tabla principal -->
        <table class="tabla-stock">
            <thead>
                <tr>
                    <th width="25%">Centro</th>
                    <th width="15%">Código</th>
                    <th width="30%">Artículo</th>
                    <th width="10%">Unidad</th>
                    <th width="10%" class="text-center">Mínimo</th>
                    <th width="10%" class="text-right">Actual</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                    @php
                        // Determinar clase según estado
                        if($stock->quantity <= 0) {
                            $clase = 'agotado';
                        } elseif($stock->warning) {
                            $clase = 'alerta';
                        } else {
                            $clase = 'ok';
                        }
                    @endphp
                    
                    <tr class="{{ $clase }}">
                        <td>{{ $stock->StockCenter->name ?? '-' }}</td>
                        <td class="bold">{{ $stock->Article->code ?? '-' }}</td>
                        <td>{{ $stock->Article->name ?? '-' }}</td>
                        <td>{{ $stock->Article->UnitName ?? '-' }}</td>
                        <td class="text-center">{{ number_format($stock->quantity_alert, 0) }}</td>
                        <td class="text-right bold">{{ number_format($stock->quantity, 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 20px;">
                            No hay registros con los filtros aplicados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Leyenda pequeña -->
        @if($stocks->isNotEmpty())
        <div style="margin-top: 10px; font-size: 9px; color: #666; border-top: 1px solid #eee; padding-top: 5px;">
            <strong>Leyenda:</strong> 
            <span style="color:#28a745;">■</span> Stock OK | 
            <span style="color:#ffc107;">■</span> Bajo stock | 
            <span style="color:#dc3545;">■</span> Agotado
        </div>
        @endif
    </div>
</body>
</html>