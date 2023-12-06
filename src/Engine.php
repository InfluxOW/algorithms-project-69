<?php

namespace App;

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
        $regexp = "/\b{$search}\b/iu";

        $found = [];
        foreach ($documents as $document) {
            if (preg_match($regexp, $document['text'])) {
                $found[] = $document['id'];
            }
        }

        return $found;
    }
}
