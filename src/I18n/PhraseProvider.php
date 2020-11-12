<?php

declare(strict_types=1);

namespace FMarangi\Magerun\I18n;

use FilesystemIterator;
use FMarangi\Magerun\I18n\SourceFile\PhpSourceFile;
use FMarangi\Magerun\I18n\SourceFile\XmlSourceFile;
use IteratorAggregate;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use SplFileInfo;

class PhraseProvider implements IteratorAggregate
{
    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return SourceFile[]
     */
    public function getIterator()
    {
        $dir = new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS);
        foreach (new RegexIterator(new RecursiveIteratorIterator($dir), '#\.(php|phtml|xml)$#') as $file) {
            yield from $this->loadFile($file)->getPhrases();
        }
    }

    private function loadFile(SplFileInfo $file): SourceFile
    {
        switch ($file->getExtension()) {
            case 'xml':
                return new XmlSourceFile($this->readFile($file));
            default:
                return new PhpSourceFile($this->readFile($file));
        }
    }

    private function readFile(SplFileInfo $file): string
    {
        $resource = $file->openFile('r');
        return $resource->fread($resource->getSize());
    }
}
