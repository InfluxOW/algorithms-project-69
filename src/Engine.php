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
            ->map(function (array $document): array {
                $document['text'] = self::term($document['text']);

                return $document;
            })
            ->filter(fn (array $document): bool => preg_match($regexp, $document['text']))
            ->map(fn (array $document): string => $document['id'])
            ->toArray();
    }

    private static function term(string $token): string
    {
        return Str::of($token)->matchAll('/\w+/')->implode(' ');
    }
}
