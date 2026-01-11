<?php

namespace App\Services;

use Spatie\Browsershot\Browsershot;
use Spatie\Image\Image;
use Illuminate\Support\Facades\Log;

class ExportEngine
{
    protected RenderLayer $renderLayer;

    public function __construct(RenderLayer $renderLayer)
    {
        $this->renderLayer = $renderLayer;
    }

    /**
     * Export as PDF
     * 
     * @param string $html HTML content to export
     * @param array $options Export options (pageSize, orientation, etc.)
     * @return string PDF binary content
     */
    public function exportAsPdf(string $html, array $options = []): string
    {
        $pageSize = $options['pageSize'] ?? 'A4';
        $orientation = $options['orientation'] ?? 'landscape';

        // Page size dimensions (in mm)
        $pageSizes = [
            'A4' => ['width' => 210, 'height' => 297],
            'A3' => ['width' => 297, 'height' => 420],
            'A2' => ['width' => 420, 'height' => 594],
        ];

        $dimensions = $pageSizes[$pageSize] ?? $pageSizes['A4'];
        if ($orientation === 'landscape') {
            $dimensions = ['width' => $dimensions['height'], 'height' => $dimensions['width']];
        }

        try {
            $browsershot = Browsershot::html($html)
                ->setOption('printBackground', true)
                ->paperSize($dimensions['width'], $dimensions['height'], 'mm')
                ->margins(10, 10, 10, 10, 'mm')
                ->waitUntilNetworkIdle();
            
            // Set Node binary path if available
            $nodePath = $this->getNodePath();
            if ($nodePath) {
                $browsershot->setNodeBinary($nodePath);
            }
            
            // Set Chrome path if available
            $chromePath = $this->getChromePath();
            if ($chromePath) {
                $browsershot->setChromePath($chromePath);
            }
            
            return $browsershot->pdf();
        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export as Image (PNG)
     * 
     * @param string $html HTML content to export
     * @param array $options Export options
     * @return string PNG binary content
     */
    public function exportAsImage(string $html, array $options = []): string
    {
        $width = $options['width'] ?? 1920;
        $height = $options['height'] ?? 1080;

        try {
            $browsershot = Browsershot::html($html)
                ->setOption('fullPage', true)
                ->setOption('waitUntilNetworkIdle', true)
                ->setOption('viewport', [
                    'width' => $width,
                    'height' => $height
                ]);
            
            // Set Node binary path if available
            $nodePath = $this->getNodePath();
            if ($nodePath) {
                $browsershot->setNodeBinary($nodePath);
            }
            
            // Set Chrome path if available
            $chromePath = $this->getChromePath();
            if ($chromePath) {
                $browsershot->setChromePath($chromePath);
            }
            
            return $browsershot->screenshot();
        } catch (\Exception $e) {
            Log::error('Image Export Error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to generate image: ' . $e->getMessage());
        }
    }

    /**
     * Export as SVG (future implementation)
     * 
     * @param string $html HTML content to export
     * @param array $options Export options
     * @return string SVG content
     */
    public function exportAsSvg(string $html, array $options = []): string
    {
        // SVG export implementation
        // This can be enhanced to generate proper vector graphics
        // For now, return HTML as placeholder
        
        return $html;
    }

    /**
     * Generate filename with timestamp
     */
    public function generateFilename(string $format, ?string $prefix = 'organizational-chart'): string
    {
        $timestamp = date('Y-m-d-His');
        return "{$prefix}-{$timestamp}.{$format}";
    }

    /**
     * Get content type for format
     */
    public function getContentType(string $format): string
    {
        return match($format) {
            'pdf' => 'application/pdf',
            'png', 'image' => 'image/png',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream'
        };
    }

    /**
     * Get Node.js executable path
     */
    private function getNodePath(): ?string
    {
        // Try common Node.js locations first
        $nodePaths = [
            'C:\Program Files\nodejs\node.exe',
        ];
        
        foreach ($nodePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        // Try to find node in PATH using where command
        $nodeCommand = shell_exec('where.exe node 2>$null');
        if ($nodeCommand) {
            $nodePath = trim(explode("\n", $nodeCommand)[0]);
            if ($nodePath && file_exists($nodePath)) {
                return $nodePath;
            }
        }

        return null;
    }

    /**
     * Get Chrome executable path
     */
    private function getChromePath(): ?string
    {
        // Check for Puppeteer Chrome (most common location)
        $puppeteerPath = getenv('USERPROFILE') . '\.cache\puppeteer\chrome';
        if (is_dir($puppeteerPath)) {
            $dirs = glob($puppeteerPath . '\*\chrome-win64\chrome.exe');
            if (!empty($dirs) && file_exists($dirs[0])) {
                return $dirs[0];
            }
        }

        // Check system Chrome
        $systemPaths = [
            'C:\Program Files\Google\Chrome\Application\chrome.exe',
            'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
        ];
        
        foreach ($systemPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
