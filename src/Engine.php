<?php

namespace App;

use Illuminate\Support\Collection;
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
        $searches = explode(' ', $search);

        $documents_count = count($documents);

        $word_counts = [];
        foreach ($documents as $document) {
            $word_counts[$document['id']] = str_word_count($document['text']);
        }

        return collect($documents)
            ->mapWithKeys(function (array $document) use ($searches): array {
                $text = self::term($document['text']);

                $matches = collect($searches)
                    ->mapWithKeys(fn (string $search): array => [$search => Str::of($text)->matchAll("/\b{$search}\b/iu")->count()])
                    ->reject(fn (int $matches_count): bool => $matches_count === 0)
                    ->toArray();

                return [$document['id'] => $matches];
            })
            ->reject(fn (array $matches): bool => empty($matches))
            ->reduce(function (Collection $acc, array $matches, string|int $id): Collection {
                foreach ($matches as $search => $matches_count) {
                    $match = $acc->offsetExists($search) ? $acc->offsetGet($search) : [];
                    $match[$id] = $matches_count;

                    $acc->offsetSet($search, $match);
                }

                return $acc;
            }, collect([]))
            ->map(function (array $matches) use ($documents_count, $word_counts): array {
                $idf = log((1 + ($documents_count - count($matches) + 1) / (count($matches) + 0.5)), 2);

                foreach ($matches as $id => $matches_count) {
                    $matches[$id] = $idf * $matches_count / $word_counts[$id];
                }

                return $matches;
            })
            ->reduce(function (Collection $acc, array $matches): Collection {
                foreach ($matches as $id => $weight) {
                    $sum = $acc->offsetExists($id) ? $acc->offsetGet($id) : 0;
                    $sum += $weight;

                    $acc->offsetSet($id, $sum);
                }

                return $acc;
            }, collect([]))
            ->sortByDesc(fn (float $weight, $b): float => $weight)
            ->keys()
            ->toArray();
    }

    private static function term(string $token): string
    {
        return Str::of($token)->matchAll('/\w+/')->implode(' ');
    }
}
