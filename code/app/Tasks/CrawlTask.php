<?php

namespace App\Tasks;

use App\Repositories\CrawlResultRepository;
use Phalcon\Cli\Task;
use App\Crawlers\AgencyAnalyticsCrawler;


class CrawlTask extends Task
{
    private function getCrawlResultRepository(): CrawlResultRepository
    {
        static $repository = null;
        if (!$repository) {
            $repository = new CrawlResultRepository();
        }
        return $repository;
    }

    public function mainAction(int $limit = 5)
    {
        $pagesToCrawl = (int)$limit;
        $crawledCount = 0;
        $this->getCrawlResultRepository()->deleteAll();
        $url = $firstUrl = 'https://agencyanalytics.com/';
        $crawler = new AgencyAnalyticsCrawler();
        $nextUrlPos = 0;
        while ($url && $crawledCount < $pagesToCrawl) {
            if ($crawler->canCrawl($url) && !$crawler->isCrawled($url)) {
                $result = $crawler->crawl($url);
                if ($nextUrlPos === 0) {
                    $internalUrls = array_values($result['unique_internal_urls']);
                    sort($internalUrls);
                }

                if (empty($result)) {
                    var_dump($result);

                }

//                var_dump($crawler->getCrawledUrls());
                $this->storeCrawlResult(sha1($url), $result);
                $crawledCount++;
            }
            $url = $internalUrls[++$nextUrlPos] ?? null;
        }
    }

    private function storeCrawlResult(string $fileName, array $data)
    {
        $this->getCrawlResultRepository()->save($fileName, $data);
    }
}