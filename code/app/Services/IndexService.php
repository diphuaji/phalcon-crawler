<?php

namespace App\Services;

use App\Crawlers\BaseCrawler;
use App\Repositories\CrawlResultRepository;
use App\Transformers\CrawlResultTransformer;

class IndexService
{
    const AVG_FIELDS = [
        'title_length',
        'page_load',
        'word_count',
    ];

    private function getCrawlResultRepository(): CrawlResultRepository
    {
        static $repository = null;
        if (!$repository) {
            $repository = new CrawlResultRepository();
        }
        return $repository;
    }

    public function getCrawlResultSummary($data)
    {
        $summary = [];
        foreach ($data as $crawlResult) {
            foreach (BaseCrawler::UNIQUES as $unique) {
                $countKey = "${unique}_count";
                foreach ($crawlResult[$unique] as $key => $value) {
                    if (!key_exists($unique, $summary)) {
                        $summary[$unique] = [$key => $value];
                    }
                    $summary[$unique][$key] = $value;
                }
                $summary[$countKey] = count($summary[$unique]);
            }
        }
        foreach (static::AVG_FIELDS as $avgField) {
            $summary["avg_$avgField"] = array_sum(array_column($data, $avgField)) / count($data);
        }
        return array_diff_key($summary, array_flip(BaseCrawler::UNIQUES));
    }

    public function getResultForIndex()
    {
        $transformer = new CrawlResultTransformer();
        $allResults = $this->getCrawlResultRepository()->all();
        $data = array_map(function ($crawlResult) use ($transformer) {
            return $transformer->setData($crawlResult)->getForIndex();
        }, $allResults);

        return [
            'data' => $data,
            'summary' => $this->getCrawlResultSummary($allResults)
        ];
    }

    public function getHeaders()
    {
        $result = [];
        $headers = [
            'title',
            'title_length',
            'url',
            'status_code',
            'unique_images_count',
            'unique_external_urls_count',
            'unique_internal_urls_count',
            'page_load',
            'word_count'
        ];
        foreach ($headers as $header) {
            $result[$header] = ucwords(str_replace('_', ' ', $header));
        }
        return $result;
    }

    public function getSummaryFieldNames()
    {
        $result = [];
        foreach (static::AVG_FIELDS as $avgField) {
            $result["avg_$avgField"] = ucwords(str_replace('_', ' ', "average $avgField"));
        }
        foreach (BaseCrawler::UNIQUES as $unique) {
            $result["${unique}_count"] = ucwords(str_replace('_', ' ', "${unique}_count"));
        }
        return $result;
    }
}