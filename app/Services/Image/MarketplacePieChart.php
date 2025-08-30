<?php

namespace App\Services\Image;

use App\Models\NftSale;
use Illuminate\Support\Facades\Storage;
use Imagine\Imagick\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\Palette\RGB;
use Imagine\Imagick\Drawer;
use Imagine\Imagick\Font;

class MarketplacePieChart
{
    protected int $size;
    protected string $backgroundColor;
    protected array $colors;

    protected \Imagine\Image\ImageInterface $image;

    public function __construct(
        int $size = 1000,
        string $backgroundColor = '#eeeeee',
        array $colors = []
    ) {
        $this->size = $size;
        $this->backgroundColor = $backgroundColor;

        $this->colors = $colors ?: [
            '#004b99', '#0062bf',
            '#007ae6', '#1a91ff', '#4da9ff',
            '#80c1ff', '#b3d9ff', '#e0f0ff', '#003366',
        ];

        $this->initializeImage();
    }

    protected function initializeImage(): void
    {
        $imagine = new Imagine();
        $palette = new RGB();

        $this->image = $imagine->create(
            new Box($this->size, $this->size),
            $palette->color($this->backgroundColor)
        );
    }

    public function process(): void
    {
        try {
            $marketplaceCounts = NftSale::marketplaceCountsLast24h();
            $marketplaceCounts = array_filter(
                $marketplaceCounts,
                fn($count, $marketplace) => !empty($marketplace),
                ARRAY_FILTER_USE_BOTH
            );

            if (empty($marketplaceCounts)) {
                throw new \Exception('No marketplace sales in last 24 hours');
            }

            // --- sort & slice top 3 ---
            arsort($marketplaceCounts);
            $top3 = array_slice($marketplaceCounts, 0, 3, true);
            $others = array_slice($marketplaceCounts, 3, null, true);

            if (!empty($others)) {
                $top3['others'] = array_sum($others);
            }

            $marketplaceCounts = $top3;
            $total = array_sum($marketplaceCounts);

            // move pie up to make room for legend
            $center = new Point($this->size / 2, $this->size / 2 - 50);
            $radius = $this->size * 0.28;

            $palette = new RGB();
            $drawer = new Drawer($this->image->getImagick());

            $angleStart = 0;
            $i = 0;

            // --- PIE ---
            foreach ($marketplaceCounts as $marketplace => $count) {
                $angleEnd = $angleStart + ($count / $total) * 360;
                $color = $palette->color($this->colors[$i % count($this->colors)]);

                $drawer->pieSlice(
                    $center,
                    new Box($radius * 2, $radius * 2),
                    $angleStart,
                    $angleEnd,
                    $color,
                    true
                );

                $angleStart = $angleEnd;
                $i++;
            }

            // --- FONT SETUP ---
            $titleFontPath = public_path('fonts/ka1_2-webfont-webfont.ttf');
            if (!file_exists($titleFontPath)) {
                throw new \Exception("Title font file not found at {$titleFontPath}");
            }

            // system font for legend + footer
            $systemFontPath = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf";
            if (!file_exists($systemFontPath)) {
                $systemFontPath = "C:/Windows/Fonts/arial.ttf";
            }
            if (!file_exists($systemFontPath)) {
                $systemFontPath = $titleFontPath; // fallback
            }

            $fontColor       = $palette->color('#000000');
            $fontColorTitle  = $palette->color('#222222');

            $titleFont  = new Font($this->image->getImagick(), $titleFontPath, 46, $fontColorTitle);
            $legendFont = new Font($this->image->getImagick(), $systemFontPath, 23, $fontColor);
            $smallFont  = new Font($this->image->getImagick(), $systemFontPath, 26, $fontColor);

            // --- TITLE ---
            $title = "XRPL NFT PULSE";
            $metrics = $this->image->getImagick()->queryFontMetrics(new \ImagickDraw(), $title);
            $textWidth = $metrics['textWidth'];
            $x = 170;
            $y = 40;
            $drawer->text($title, $titleFont, new Point((int) $x, (int) $y));

            // --- LEGEND ---
            $legendX = 35;
            $legendY = $this->size - 220;
            $boxSize = 30;
            $spacingY = 50;

            $i = 0;
            foreach ($marketplaceCounts as $marketplace => $count) {
                $color = $palette->color($this->colors[$i % count($this->colors)]);

                $drawer->polygon([
                    new Point($legendX, $legendY),
                    new Point($legendX + $boxSize, $legendY),
                    new Point($legendX + $boxSize, $legendY + $boxSize),
                    new Point($legendX, $legendY + $boxSize),
                ], $color, true);

                $drawer->text("{$marketplace} ({$count})", $legendFont, new Point($legendX + $boxSize + 15, $legendY - 8));

                $legendY += $spacingY;

                $i++;
            }



        } catch (\Exception $e) {
            \Log::error('MarketplacePieChart process failed: ' . $e->getMessage());
        }
    }

    public function saveToSpaces(string $filename = null): string
    {
        $filename = $filename ?: 'xrp_nft_sales_pulse/marketplace_pie_' . now()->format('Ymd') . '.png';
        $tempPath = storage_path('app/tmp/' . basename($filename));

        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $this->image->save($tempPath);
        Storage::disk('spaces')->put($filename, file_get_contents($tempPath), 'public');

        \Log::info('MarketplacePieChart saved', ['filename' => $filename]);

        return $filename;
    }

    public function getImage(): \Imagine\Image\ImageInterface
    {
        return $this->image;
    }
}
