<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use function App\search;

class EngineTest extends TestCase
{
    /**
     * @dataProvider searchDataProvider
     */
    public function testItCanSearch(array $documents, string $search, array $found): void
    {
        $this->assertEquals(search($documents, $search), $found);
    }

    public static function searchDataProvider(): array
    {
        $documents = [
            ['id' => 'document #1', 'text' => 'I can\'t shoot straight unless I\'ve had a pint!'],
            ['id' => 'document #2', 'text' => 'Don\'t shoot shoot shoot that thing at me.'],
            ['id' => 'document #3', 'text' => 'I\'m your shooter.'],
        ];

        return [
            [
                [],
                'shoot',
                [],
            ],
            [
                $documents,
                'shoot',
                ['document #2', 'document #1'],
            ],
            [
                $documents,
                'pint',
                ['document #1'],
            ],
            [
                $documents,
                'shoot at me',
                ['document #2', 'document #1'],
            ],
        ];
    }
}
