<?php


namespace App\Repositories;

use Phalcon\Di\Injectable;

class CrawlResultRepository extends Injectable
{
    CONST FIELDS_TO_STORE = [
        'title',
        'title_length',
        'url',
        'status_code',
        'unique_images',
        'unique_external_urls',
        'unique_internal_urls',
        'page_load',
        'word_count',
    ];
    private $outputFolder = BASE_PATH . '/storage/crawl_results';

    public function save($fileName, array $data): bool
    {
        $success = false;
        if (!file_exists($this->outputFolder)) {
            mkdir($this->outputFolder, 0777, true);
        }
        $fieldsToStore = array_flip(static::FIELDS_TO_STORE);
        if (($content = json_encode(array_intersect_key($data, $fieldsToStore))) !== false) {
            $outputFile = fopen("{$this->outputFolder}/$fileName.json", "w");
            $this->di->get('logger')->info("Writing crawl result to $fileName.json ...");
            fwrite($outputFile, $content);
            fclose($outputFile);
            $this->di->get('logger')->info("Crawl result saved to $fileName.json");
            $success = true;
        } else {
            $this->di->get('logger')->warning('Failed to serialize craw result!');
        }
        return $success;
    }

    private function getDataFiles(): array
    {
        return glob("$this->outputFolder/*.json");
    }

    public function all(): array
    {
        return array_map(function ($fileName) {
            return json_decode(file_get_contents($fileName), true);
        }, $this->getDataFiles());
    }

    public function deleteAll()
    {
        foreach ($this->getDataFiles() as $file) {
            unlink($file);
        }
    }
}