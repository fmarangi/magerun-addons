<?php

declare(strict_types=1);

namespace FMarangi\Magerun\I18n\SourceFile;

use FMarangi\Magerun\I18n\SourceFile;

class XmlSourceFile implements SourceFile
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
        foreach (simplexml_load_string($this->content)->xpath('//*[@translate]') as $node) {
            $attributes = array_map('trim', explode(' ', (string) $node->attributes()->translate));
            foreach ($attributes as $attribute) {
                yield (string) $node->{$attribute};
            }
        }
    }
}
