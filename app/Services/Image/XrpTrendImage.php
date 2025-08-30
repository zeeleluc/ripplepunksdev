<?php

namespace App\Services\Image;

use App\Models\XrpPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickDraw;
use ImagickPixel;

class XrpTrendImage extends AbstractImageProcessor
{
    protected int $size;
    protected string $backgroundColor;
    protected float $strokeWidth;

    public function __construct(
        int $size = 1000,
        string $backgroundColor = '#347AE6',
        float $strokeWidth = 4.0
    ) {
        $this->size = $size;
        $this->backgroundColor = $backgroundColor;
        $this->strokeWidth = $strokeWidth;

        // Initialize Imagick image
        $this->initializeImage($size, $size, $backgroundColor);
    }

    public function process(): void
    {
        if (!$this->image) {
            throw new \Exception('Image not initialized');
        }

        // Timezone NY
        $nyTimezone = 'America/New_York';
        $start = Carbon::yesterday($nyTimezone)->startOfDay(); // 00:00 NY
        $end   = Carbon::yesterday($nyTimezone)->endOfDay();   // 23:59:59 NY

        // Fetch hourly rates for previous day
        $rates = collect();
        $current = $start->copy();
        while ($current <= $end) {
            $hourStart = $current->copy();
            $hourEnd   = $current->copy()->addHour();

            $rate = XrpPrice::whereBetween('created_at', [$hourStart, $hourEnd])
                ->orderBy('created_at', 'asc')
                ->first();

            if ($rate) {
                $rates->push($rate);
            }

            $current->addHour();
        }

        if ($rates->isEmpty()) {
            throw new \Exception('No XRP price data available for previous 24 hours (NY time)');
        }

        // Extract min/max
        $prices = $rates->pluck('price_usd')->map(fn($p) => (float)$p)->toArray();
        $minPrice = min($prices);
        $maxPrice = max($prices);
        $range = $maxPrice - $minPrice ?: 1;

        $width  = $this->image->getImageWidth();
        $height = $this->image->getImageHeight();
        $paddingTop = 100;
        $paddingBottom = 100;
        $paddingLeft = 50;
        $paddingRight = 50;

        $chartWidth = $width - $paddingLeft - $paddingRight;
        $chartHeight = $height - $paddingTop - $paddingBottom;
        $xStep = $chartWidth / (count($rates) - 1);

        // Prepare points
        $points = [];
        foreach ($rates as $index => $rate) {
            $x = $paddingLeft + ($index * $xStep);
            $y = $paddingTop + ($chartHeight - (($rate->price_usd - $minPrice) / $range) * $chartHeight);
            $points[] = ['x' => $x, 'y' => $y];
        }

        // Draw trend line
        $draw = new ImagickDraw();
        $draw->setStrokeColor(new ImagickPixel('white'));
        $draw->setStrokeWidth($this->strokeWidth);
        $draw->setFillColor(new ImagickPixel('none'));

        for ($i = 1; $i < count($points); $i++) {
            $draw->line(
                $points[$i - 1]['x'], $points[$i - 1]['y'],
                $points[$i]['x'], $points[$i]['y']
            );
        }

        $this->image->drawImage($draw);

        // Draw text
        $fontPath = public_path('fonts/arcade-webfont-webfont.ttf');
        if (!file_exists($fontPath)) {
            throw new \Exception("Font file not found at {$fontPath}");
        }

        $textDraw = new ImagickDraw();
        $textDraw->setFont($fontPath);
        $textDraw->setFontSize(36);
        $textDraw->setFillColor(new ImagickPixel('white'));

        // Top-left: LOW price
        $this->image->annotateImage($textDraw, 40, 60, 0, 'LOW $' . number_format($minPrice, 2));

        // Top-right: HIGH price
        $rightText = 'HIGH $' . number_format($maxPrice, 2);
        $metrics = $this->image->queryFontMetrics($textDraw, $rightText);
        $topRightX = $width - $metrics['textWidth'] - 40;
        $topRightY = 60;
        $this->image->annotateImage($textDraw, $topRightX, $topRightY, 0, $rightText);

        // Bottom-left: previous day US format, NY
        $bottomLeftText = $start->format('m/d/Y') . ' NY';
        $this->image->annotateImage($textDraw, 40, $height - 30, 0, $bottomLeftText);

        // Bottom-right: XRP label
        $bottomRightText = 'XRP';
        $metrics = $this->image->queryFontMetrics($textDraw, $bottomRightText);
        $bottomRightX = $width - $metrics['textWidth'] - 40;
        $bottomRightY = $height - 30;
        $this->image->annotateImage($textDraw, $bottomRightX, $bottomRightY, 0, $bottomRightText);
    }

    /**
     * Save chart to Spaces
     */
    public function saveToSpaces(string $filename = null): string
    {
        if (!$this->image) {
            throw new \Exception('No image to save');
        }

        // Default filename with date for previous day
        $filename = $filename ?: 'xrp_trends/xrp_trend_' . Carbon::yesterday()->format('Ymd') . '.png';
        $tempPath = storage_path('app/tmp/' . basename($filename));

        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $this->image->writeImage($tempPath);
        Storage::disk('spaces')->put($filename, file_get_contents($tempPath), 'public');

        return $filename;
    }

    public function getImage(): Imagick
    {
        return $this->image;
    }
}
