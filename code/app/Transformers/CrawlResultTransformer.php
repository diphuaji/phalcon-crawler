<?php

namespace App\Transformers;

use App\Crawlers\BaseCrawler;

class CrawlResultTransformer
{
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getForIndex()
    {
        foreach (BaseCrawler::UNIQUES as $unique) {
            $key = "${unique}_count";
            $uniqueData = $this->data[$unique] ?? [];
            $this->data[$key] = is_array($uniqueData) ? count($uniqueData) : 0;
        }
        return array_diff_key($this->data, array_flip(BaseCrawler::UNIQUES));
    }
}