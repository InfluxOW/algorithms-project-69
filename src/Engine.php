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

        $regexp = "/\b{$search}\b/iu";

        return collect($documents)
            ->mapWithKeys(function (array $document) use ($regexp): array {
                $term = self::term($document['text']);
                $matches_count = Str::of($term)
                    ->matchAll($regexp)
                    ->count();

                return [$document['id'] => $matches_count];
            })
            ->reject(fn (int $matches_count, string|int $id): bool => $matches_count === 0)
            ->sortByDesc(fn (int $matches_count, string|int $id): int => $matches_count)
            ->keys()
            ->toArray();
    }

    private static function term(string $token): string
    {
        return Str::of($token)->matchAll('/\w+/')->implode(' ');
    }
}
