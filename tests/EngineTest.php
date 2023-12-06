<?php

namespace App\Tests;

use App\Engine;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider searchDataProvider
     */
    public function it_can_search(array $documents, string $search, array $found): void
    {
        $this->assertEquals(Engine::search($documents, $search), $found);
    }

    public static function searchDataProvider(): array
    {
        $documents = [
            ['id' => 1, 'text' => 'I can\'t shoot straight unless I\'ve had a pint!'],
            ['id' => 2, 'text' => 'Don\'t shoot shoot shoot that thing at me.'],
            ['id' => 3, 'text' => 'I\'m your shooter.'],
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
                [1, 2],
            ]
        ];
    }
}
