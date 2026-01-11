<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\View\View;

class RenderLayer
{
    /**
     * Render organizational chart as HTML
     * 
     * @param Collection $rootUnits
     * @param Collection $allUnits
     * @param array $metadata Export metadata (effectiveDate, watermark, generatedBy, showLegend)
     * @return string HTML content
     */
    public function renderAsHtml(
        Collection $rootUnits,
        Collection $allUnits,
        array $metadata = []
    ): string {
        $effectiveDate = $metadata['effectiveDate'] ?? date('Y-m-d');
        $watermark = $metadata['watermark'] ?? 'OFFICIAL';
        $generatedBy = $metadata['generatedBy'] ?? config('app.name', 'MOE Chart System');
        $showLegend = $metadata['showLegend'] ?? true;

        return view('org-chart.export', compact(
            'rootUnits',
            'allUnits',
            'effectiveDate',
            'watermark',
            'generatedBy',
            'showLegend'
        ))->render();
    }

    /**
     * Render organizational chart as SVG
     * 
     * @param Collection $rootUnits
     * @param Collection $allUnits
     * @param array $metadata Export metadata
     * @return string SVG content
     */
    public function renderAsSvg(
        Collection $rootUnits,
        Collection $allUnits,
        array $metadata = []
    ): string {
        // SVG rendering can be implemented here for vector graphics
        // For now, we'll use HTML rendering which can be converted to SVG
        // This is a placeholder for future SVG implementation
        
        $html = $this->renderAsHtml($rootUnits, $allUnits, $metadata);
        
        // Convert HTML to SVG (simplified - in production, use proper SVG generation)
        return $this->htmlToSvg($html);
    }

    /**
     * Convert HTML to SVG (placeholder - implement proper SVG generation)
     */
    private function htmlToSvg(string $html): string
    {
        // This is a placeholder
        // In production, implement proper SVG generation from organizational data
        // or use a library like mPDF or similar for HTML to SVG conversion
        
        return $html; // For now, return HTML as-is
    }

    /**
     * Get export view with all data
     */
    public function getExportView(
        Collection $rootUnits,
        Collection $allUnits,
        array $metadata = []
    ): View {
        $effectiveDate = $metadata['effectiveDate'] ?? date('Y-m-d');
        $watermark = $metadata['watermark'] ?? 'OFFICIAL';
        $generatedBy = $metadata['generatedBy'] ?? config('app.name', 'MOE Chart System');
        $showLegend = $metadata['showLegend'] ?? true;

        return view('org-chart.export', compact(
            'rootUnits',
            'allUnits',
            'effectiveDate',
            'watermark',
            'generatedBy',
            'showLegend'
        ));
    }
}
