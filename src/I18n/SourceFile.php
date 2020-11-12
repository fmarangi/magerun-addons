<?php

declare(strict_types=1);

namespace FMarangi\Magerun\I18n;

interface SourceFile
{
    public function getPhrases(): iterable;
}
