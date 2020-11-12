<?php

declare(strict_types=1);

namespace FMarangi\Magerun\I18n\SourceFile;

use FMarangi\Magerun\I18n\SourceFile;

class PhpSourceFile implements SourceFile
{
    /** @var string */
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getPhrases(): iterable
    {
        yield from [];

        $translate = false;
        foreach (array_filter(token_get_all($this->content), 'is_array') as $token) {
            if ($translate && $token[0] === T_CONSTANT_ENCAPSED_STRING) {
                yield eval("return {$token[1]};");
                $translate = false;
            }

            if ($token[0] === T_STRING && $token[1] === '__') {
                $translate = true;
            }
        }
    }
}
