<?php

namespace App;

function search(array $documents, string $search): array
{
    return Engine::search($documents, $search);
}
