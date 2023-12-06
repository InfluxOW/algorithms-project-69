<?php

namespace App;

use Illuminate\Support\Str;

final class Engine
{
    /**
     * @param array<int, array{id: string, text: string}> $documents
     * @param string                                      $search
     *
     * @return array
     */
    public static function search(array $documents, string $search): array
    {
        $search = self::term($search);

        $regexps = Str::of($search)
            ->explode(' ')
            ->map(fn (string $search) => "/\b{$search}\b/iu")
            ->toArray();

        return collect($documents)
            ->mapWithKeys(function (array $document) use ($regexps): array {
                $text = self::term($document['text']);

                $matches = collect($regexps)
                    ->mapWithKeys(fn (string $regexp, int $i) => [$i => Str::of($text)->matchAll($regexp)->count()])
                    ->reject(fn (int $matches_count): bool => $matches_count === 0)
                    ->toArray();

                return [$document['id'] => $matches];
            })
            ->reject(fn (array $matches): bool => empty($matches))
            ->sortByDesc(fn (array $matches): array => [count($matches), array_sum($matches)])
            ->keys()
            ->toArray();
    }

    private static function term(string $token): string
    {
        return Str::of($token)->matchAll('/\w+/')->implode(' ');
    }
}
