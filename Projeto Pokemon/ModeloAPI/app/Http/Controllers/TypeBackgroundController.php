<?php

namespace App\Http\Controllers;

use App\Support\PokemonTypes;
use Illuminate\Http\Request;

class TypeBackgroundController extends Controller
{
    public function __invoke(Request $request)
    {
        $types = PokemonTypes::normalizeMany(explode(',', (string) $request->query('types', 'grass')));
        $primary = $types[0] ?? 'normal';
        $secondary = $types[1] ?? null;

        $svg = $this->buildSvg($primary, $secondary);

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    private function buildSvg(string $primary, ?string $secondary): string
    {
        $main = PokemonTypes::backgroundMeta($primary);
        $mix = $secondary ? PokemonTypes::backgroundMeta($secondary) : $main;
        $accent = PokemonTypes::color($secondary ?: $primary);
        $scene = $this->sceneLayer($main['scene'], $main, $accent);
        $mixLayer = $secondary ? $this->sceneLayer($mix['scene'], $mix, PokemonTypes::color($primary), 0.38) : '';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1920" height="1080" viewBox="0 0 1920 1080" preserveAspectRatio="xMidYMid slice">
  <defs>
    <linearGradient id="sky" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="{$main['sky']}"/>
      <stop offset="0.55" stop-color="{$mix['horizon']}"/>
      <stop offset="1" stop-color="{$main['deep']}"/>
    </linearGradient>
    <radialGradient id="glow" cx="50%" cy="38%" r="55%">
      <stop offset="0" stop-color="{$accent}" stop-opacity="0.42"/>
      <stop offset="0.48" stop-color="{$accent}" stop-opacity="0.12"/>
      <stop offset="1" stop-color="#05070c" stop-opacity="0.72"/>
    </radialGradient>
    <filter id="softBlur">
      <feGaussianBlur stdDeviation="8"/>
    </filter>
  </defs>
  <rect width="1920" height="1080" fill="url(#sky)"/>
  <rect width="1920" height="1080" fill="url(#glow)"/>
  <circle cx="1510" cy="170" r="95" fill="#fff6bb" opacity="0.45"/>
  <path d="M0 650 C230 575 420 610 640 565 C880 515 1015 570 1210 530 C1455 480 1660 520 1920 470 L1920 1080 L0 1080 Z" fill="{$mix['ground']}" opacity="0.78"/>
  <path d="M0 745 C270 690 480 745 780 685 C1040 635 1260 695 1505 650 C1715 610 1810 630 1920 590 L1920 1080 L0 1080 Z" fill="{$main['deep']}" opacity="0.86"/>
  {$scene}
  {$mixLayer}
  <rect width="1920" height="1080" fill="#03070d" opacity="0.18"/>
  <rect width="1920" height="1080" fill="none" stroke="#ffffff" stroke-opacity="0.04" stroke-width="24"/>
</svg>
SVG;
    }

    private function sceneLayer(string $scene, array $meta, string $accent, float $opacity = 1): string
    {
        $o = max(0, min(1, $opacity));

        return match ($scene) {
            'volcano' => <<<SVG
<g opacity="$o">
  <path d="M1120 330 L1390 820 L840 820 Z" fill="#2b1515"/>
  <path d="M1120 330 L1220 820 L1005 820 Z" fill="#6b1f1c" opacity="0.88"/>
  <path d="M1086 375 C1118 420 1150 420 1195 375 L1220 470 C1165 495 1107 490 1057 463 Z" fill="$accent"/>
  <path d="M760 820 C900 710 1050 760 1195 702 C1370 630 1500 685 1660 625 L1920 1080 L0 1080 Z" fill="#171018" opacity="0.62"/>
  <circle cx="1130" cy="438" r="22" fill="#ffd56a" opacity="0.84"/>
</g>
SVG,
            'lake' => <<<SVG
<g opacity="$o">
  <path d="M0 690 C330 640 510 735 840 680 C1160 625 1375 715 1920 630 L1920 1080 L0 1080 Z" fill="#0d5b8f" opacity="0.65"/>
  <path d="M200 770 C500 715 760 790 1060 725 C1310 670 1540 715 1760 670" fill="none" stroke="#d8fbff" stroke-width="11" opacity="0.28"/>
  <path d="M1180 620 C1260 560 1350 545 1440 600 C1325 602 1235 640 1180 620 Z" fill="#0d3355" opacity="0.45"/>
</g>
SVG,
            'storm' => <<<SVG
<g opacity="$o">
  <path d="M440 275 L690 275 L610 435 L740 435 L455 760 L535 520 L395 520 Z" fill="#ffe66d" opacity="0.82"/>
  <path d="M1040 190 L1220 190 L1165 330 L1270 330 L1020 610 L1092 400 L980 400 Z" fill="#fff7a8" opacity="0.58"/>
  <path d="M0 625 C280 545 510 615 780 560 C1140 488 1380 575 1920 498 L1920 715 C1540 675 1230 705 960 682 C600 652 330 720 0 680 Z" fill="#111827" opacity="0.72"/>
</g>
SVG,
            'forest' => <<<SVG
<g opacity="$o">
  <path d="M120 720 L260 420 L400 720 Z M315 740 L500 360 L695 740 Z M1320 730 L1490 355 L1685 730 Z M1530 750 L1710 430 L1900 750 Z" fill="#102f20"/>
  <path d="M0 820 C220 690 410 785 600 660 C795 535 1045 710 1240 600 C1470 470 1660 620 1920 548 L1920 1080 L0 1080 Z" fill="#173b28" opacity="0.75"/>
</g>
SVG,
            'glacier' => <<<SVG
<g opacity="$o">
  <path d="M210 790 L430 420 L650 790 Z M840 820 L1060 350 L1320 820 Z M1280 790 L1510 455 L1745 790 Z" fill="#dffaff" opacity="0.74"/>
  <path d="M430 420 L540 790 L330 790 Z M1060 350 L1175 820 L960 820 Z" fill="#78bfd7" opacity="0.45"/>
  <path d="M0 820 C350 760 560 830 940 780 C1240 740 1510 800 1920 735 L1920 1080 L0 1080 Z" fill="#e8fdff" opacity="0.36"/>
</g>
SVG,
            'arena' => <<<SVG
<g opacity="$o">
  <ellipse cx="960" cy="770" rx="520" ry="150" fill="#5f2f24" opacity="0.72"/>
  <ellipse cx="960" cy="770" rx="350" ry="92" fill="#d09a65" opacity="0.34"/>
  <path d="M320 580 L420 520 L520 585 L420 650 Z M1400 580 L1500 520 L1600 585 L1500 650 Z" fill="$accent" opacity="0.55"/>
</g>
SVG,
            'swamp' => <<<SVG
<g opacity="$o">
  <ellipse cx="610" cy="790" rx="390" ry="105" fill="#5f7d49" opacity="0.52"/>
  <ellipse cx="1180" cy="770" rx="430" ry="125" fill="#6b4a8b" opacity="0.42"/>
  <circle cx="760" cy="610" r="34" fill="$accent" opacity="0.4"/>
  <circle cx="1280" cy="570" r="52" fill="$accent" opacity="0.22"/>
  <path d="M120 720 C330 640 470 725 635 630 C835 515 990 700 1190 610 C1390 520 1650 640 1830 560" fill="none" stroke="#c891d4" stroke-width="8" opacity="0.18"/>
</g>
SVG,
            'desert' => <<<SVG
<g opacity="$o">
  <path d="M0 780 C270 585 470 795 720 615 C980 430 1210 730 1465 580 C1675 455 1820 560 1920 505 L1920 1080 L0 1080 Z" fill="#9b6135" opacity="0.58"/>
  <path d="M220 680 L365 440 L510 680 Z M1370 700 L1520 450 L1670 700 Z" fill="#704223" opacity="0.66"/>
</g>
SVG,
            'clouds' => <<<SVG
<g opacity="$o" filter="url(#softBlur)">
  <ellipse cx="410" cy="345" rx="210" ry="70" fill="#ffffff" opacity="0.34"/>
  <ellipse cx="620" cy="305" rx="170" ry="58" fill="#ffffff" opacity="0.25"/>
  <ellipse cx="1350" cy="310" rx="245" ry="82" fill="#ffffff" opacity="0.30"/>
  <ellipse cx="1550" cy="365" rx="180" ry="62" fill="#ffffff" opacity="0.22"/>
</g>
SVG,
            'aurora' => <<<SVG
<g opacity="$o">
  <path d="M0 260 C380 100 480 455 820 250 C1160 45 1390 395 1920 170 L1920 315 C1460 510 1155 210 850 410 C520 625 345 270 0 460 Z" fill="$accent" opacity="0.24"/>
  <path d="M0 170 C440 330 610 40 950 205 C1250 350 1495 90 1920 250" fill="none" stroke="#e9fff6" stroke-width="15" opacity="0.26"/>
</g>
SVG,
            'meadow' => <<<SVG
<g opacity="$o">
  <path d="M0 790 C300 650 505 800 780 640 C1030 495 1240 760 1500 595 C1680 480 1825 575 1920 520 L1920 1080 L0 1080 Z" fill="#3f692b" opacity="0.65"/>
  <circle cx="450" cy="700" r="18" fill="$accent" opacity="0.66"/>
  <circle cx="1010" cy="685" r="13" fill="$accent" opacity="0.58"/>
  <circle cx="1395" cy="735" r="17" fill="$accent" opacity="0.55"/>
</g>
SVG,
            'canyon' => <<<SVG
<g opacity="$o">
  <path d="M160 790 L300 430 L450 790 Z M430 805 L590 360 L760 805 Z M1260 800 L1430 390 L1610 800 Z" fill="#5d4b32" opacity="0.74"/>
  <path d="M0 830 C360 720 600 815 920 710 C1230 610 1495 760 1920 650 L1920 1080 L0 1080 Z" fill="#32271e" opacity="0.56"/>
</g>
SVG,
            'mist' => <<<SVG
<g opacity="$o" filter="url(#softBlur)">
  <ellipse cx="470" cy="675" rx="390" ry="80" fill="#c8c1ff" opacity="0.18"/>
  <ellipse cx="1110" cy="700" rx="520" ry="100" fill="#d8d7ff" opacity="0.16"/>
  <ellipse cx="1540" cy="620" rx="320" ry="70" fill="#fff" opacity="0.10"/>
</g>
SVG,
            'mountain' => <<<SVG
<g opacity="$o">
  <path d="M120 810 L500 300 L860 810 Z M720 820 L1090 215 L1490 820 Z M1220 810 L1550 355 L1900 810 Z" fill="#182247" opacity="0.78"/>
  <path d="M500 300 L620 810 L385 810 Z M1090 215 L1240 820 L955 820 Z" fill="#314275" opacity="0.44"/>
</g>
SVG,
            'night' => <<<SVG
<g opacity="$o">
  <circle cx="1460" cy="170" r="82" fill="#e7e2bc" opacity="0.7"/>
  <circle cx="1492" cy="146" r="82" fill="{$meta['sky']}" opacity="0.95"/>
  <path d="M110 755 L260 430 L410 755 Z M1440 775 L1590 420 L1740 775 Z" fill="#080b0d" opacity="0.85"/>
  <circle cx="610" cy="250" r="3" fill="#fff" opacity="0.7"/>
  <circle cx="910" cy="155" r="2.5" fill="#fff" opacity="0.62"/>
  <circle cx="1215" cy="250" r="3.2" fill="#fff" opacity="0.72"/>
</g>
SVG,
            'foundry' => <<<SVG
<g opacity="$o">
  <path d="M220 785 L220 510 L330 510 L330 785 Z M1420 790 L1420 450 L1550 450 L1550 790 Z" fill="#28313f" opacity="0.72"/>
  <path d="M120 810 L120 620 L1820 620 L1820 810 Z" fill="#313948" opacity="0.56"/>
  <path d="M245 490 C230 430 285 405 260 355 C340 405 320 455 330 510 Z" fill="$accent" opacity="0.28"/>
  <path d="M1460 430 C1450 365 1525 352 1495 290 C1585 350 1570 398 1550 450 Z" fill="$accent" opacity="0.22"/>
</g>
SVG,
            'glade' => <<<SVG
<g opacity="$o">
  <path d="M0 800 C350 660 560 785 835 650 C1095 525 1330 780 1580 620 C1760 510 1870 565 1920 540 L1920 1080 L0 1080 Z" fill="#496f45" opacity="0.62"/>
  <circle cx="590" cy="540" r="9" fill="#fff7d6" opacity="0.8"/>
  <circle cx="790" cy="640" r="7" fill="#fff7d6" opacity="0.72"/>
  <circle cx="1270" cy="560" r="10" fill="#fff7d6" opacity="0.76"/>
  <circle cx="1480" cy="700" r="6" fill="#fff7d6" opacity="0.68"/>
</g>
SVG,
            default => <<<SVG
<g opacity="$o">
  <path d="M0 810 C300 650 565 810 860 645 C1130 495 1390 760 1635 610 C1780 520 1870 550 1920 520 L1920 1080 L0 1080 Z" fill="#30492e" opacity="0.58"/>
</g>
SVG,
        };
    }
}
