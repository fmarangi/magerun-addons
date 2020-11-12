<?php

declare(strict_types=1);

namespace FMarangi\Magerun\I18n;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CollectPhrasesCommand extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('i18n:collect-phrases');
        $this->setDescription('Discovers phrases in the codebase');
        $this->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Current translation file');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to codebase');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $translations = $this->getTranslations((string) $input->getOption('file'));
        array_map($this->writeCsv($translations), $this->getPhrases($input->getArgument('path')));
    }

    private function writeCsv(array $translations): callable
    {
        return function (string $phrase) use ($translations): void {
            fputcsv(STDOUT, [$phrase, $translations[$phrase] ?? ''], ',');
        };
    }

    function getPhrases(string $path): array
    {
        $phrases = array_unique(array_filter(iterator_to_array(new PhraseProvider($path))));
        sort($phrases);
        return $phrases;
    }

    private function getTranslations(string $file): array
    {
        $handle = $file && is_file($file) && is_readable($file) ? fopen($file, 'r') : tmpfile();
        for ($data = []; ($row = fgetcsv($handle)) !== false;) {
            $data[] = $row;
        }
        fclose($handle);
        return array_filter(array_combine(array_column($data, 0), array_column($data, 1)));
    }
}
