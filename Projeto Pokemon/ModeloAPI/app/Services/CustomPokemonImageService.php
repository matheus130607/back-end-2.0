<?php

namespace App\Services;

use App\Support\PokemonTypes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomPokemonImageService
{
    public function storeUploadedImage(UploadedFile $file): string
    {
        $originalPath = $file->store('pokemons/original', 'public');
        $processedPath = $this->removeBackground($originalPath);

        return $processedPath ?: $originalPath;
    }

    public function generateOrFallback(array $pokemonData): string
    {
        $generatedPath = $this->generateWithAi($pokemonData);

        return $generatedPath ?: $this->createFallbackSvg($pokemonData);
    }

    private function removeBackground(string $relativePath): ?string
    {
        $apiKey = config('services.removebg.key');

        if (!$apiKey || !Storage::disk('public')->exists($relativePath)) {
            return null;
        }

        $absolutePath = Storage::disk('public')->path($relativePath);
        $handle = fopen($absolutePath, 'rb');

        if (!$handle) {
            return null;
        }

        try {
            $response = Http::timeout(35)
                ->withHeaders(['X-Api-Key' => $apiKey])
                ->attach('image_file', $handle, basename($absolutePath))
                ->post('https://api.remove.bg/v1.0/removebg', [
                    'size' => 'auto',
                    'format' => 'png',
                ]);
        } catch (\Throwable) {
            return null;
        } finally {
            fclose($handle);
        }

        if (!$response->successful()) {
            return null;
        }

        $path = 'pokemons/processed/' . Str::uuid() . '.png';
        Storage::disk('public')->put($path, $response->body());

        return $path;
    }

    private function generateWithAi(array $pokemonData): ?string
    {
        $prompt = $this->buildPrompt($pokemonData);
        $endpoint = rtrim((string) config('services.pollinations.image_endpoint'), '/');

        if ($endpoint === '') {
            return null;
        }

        $apiKey = config('services.pollinations.key');

        if (!$apiKey && str_contains($endpoint, 'gen.pollinations.ai')) {
            return null;
        }

        $url = $endpoint . '/' . rawurlencode($prompt);
        $query = [
            'width' => 768,
            'height' => 768,
            'model' => config('services.pollinations.image_model', 'flux'),
            'nologo' => 'true',
            'safe' => 'true',
            'seed' => crc32((string) ($pokemonData['name'] ?? Str::uuid())),
        ];

        $request = Http::timeout(60)->retry(1, 600);
        if ($apiKey) {
            $request = $request->withHeaders(['Authorization' => 'Bearer ' . $apiKey]);
            $query['key'] = $apiKey;
        }

        try {
            $response = $request->get($url, $query);
        } catch (\Throwable) {
            return null;
        }

        if (!$response->successful() || !str_starts_with((string) $response->header('Content-Type'), 'image/')) {
            return null;
        }

        $path = 'pokemons/generated/' . Str::slug((string) ($pokemonData['name'] ?? 'pokemon')) . '-' . Str::random(8) . '.jpg';
        Storage::disk('public')->put($path, $response->body());

        return $path;
    }

    private function buildPrompt(array $pokemonData): string
    {
        $types = PokemonTypes::normalizeMany($pokemonData['types'] ?? ['normal']);
        $typeNames = implode(' and ', array_map(fn ($type) => PokemonTypes::label($type), $types));
        $name = (string) ($pokemonData['name'] ?? 'new Pokemon');
        $ability = (string) ($pokemonData['ability'] ?? 'special ability');
        $height = (string) ($pokemonData['height'] ?? '1.0');
        $weight = (string) ($pokemonData['weight'] ?? '10.0');

        return "Original cute monster creature inspired by classic 1990s handheld monster games, name {$name}, {$typeNames} type, ability {$ability}, height {$height} meters, weight {$weight} kg, full body centered, clean silhouette, official creature concept art feel, bright friendly expression, no text, no watermark";
    }

    private function createFallbackSvg(array $pokemonData): string
    {
        $types = PokemonTypes::normalizeMany($pokemonData['types'] ?? ['normal']);
        $primary = $types[0];
        $secondary = $types[1] ?? $primary;
        $color1 = PokemonTypes::color($primary);
        $color2 = PokemonTypes::color($secondary);
        $name = e((string) ($pokemonData['name'] ?? 'Pokemon'));
        $slug = Str::slug((string) ($pokemonData['name'] ?? 'pokemon')) ?: 'pokemon';
        $path = 'pokemons/generated/' . $slug . '-' . Str::random(8) . '.svg';

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="768" height="768" viewBox="0 0 768 768">
  <defs>
    <radialGradient id="body" cx="38%" cy="28%" r="70%">
      <stop offset="0" stop-color="#ffffff" stop-opacity="0.8"/>
      <stop offset="0.34" stop-color="$color1"/>
      <stop offset="1" stop-color="$color2"/>
    </radialGradient>
    <filter id="shadow">
      <feDropShadow dx="0" dy="22" stdDeviation="20" flood-color="#09111c" flood-opacity="0.35"/>
    </filter>
  </defs>
  <rect width="768" height="768" rx="72" fill="#f4f7ef"/>
  <circle cx="384" cy="384" r="310" fill="$color1" opacity="0.12"/>
  <circle cx="456" cy="250" r="90" fill="$color2" opacity="0.18"/>
  <ellipse cx="384" cy="610" rx="205" ry="42" fill="#172033" opacity="0.18"/>
  <g filter="url(#shadow)">
    <path d="M236 398 C236 275 310 190 395 190 C508 190 592 298 566 430 C546 533 470 588 377 588 C290 588 236 512 236 398 Z" fill="url(#body)" stroke="#142033" stroke-width="16"/>
    <path d="M278 265 L210 160 L330 214 Z" fill="$color1" stroke="#142033" stroke-width="14" stroke-linejoin="round"/>
    <path d="M488 220 L596 142 L550 292 Z" fill="$color2" stroke="#142033" stroke-width="14" stroke-linejoin="round"/>
    <circle cx="340" cy="360" r="28" fill="#172033"/>
    <circle cx="455" cy="360" r="28" fill="#172033"/>
    <circle cx="350" cy="350" r="8" fill="#ffffff"/>
    <circle cx="465" cy="350" r="8" fill="#ffffff"/>
    <path d="M350 455 C386 490 432 490 468 455" fill="none" stroke="#172033" stroke-width="15" stroke-linecap="round"/>
    <path d="M250 444 C200 458 166 494 152 552" fill="none" stroke="#142033" stroke-width="18" stroke-linecap="round"/>
    <path d="M526 444 C584 454 626 492 646 552" fill="none" stroke="#142033" stroke-width="18" stroke-linecap="round"/>
  </g>
  <text x="384" y="706" text-anchor="middle" font-family="Arial, sans-serif" font-weight="800" font-size="42" fill="#172033">$name</text>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
