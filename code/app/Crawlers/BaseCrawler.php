<?php

namespace App\Crawlers;

use App\Helpers\UrlHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\TransferStats;
use DOMDocument;
use DOMXPath;
use Phalcon\Di\Injectable;
use Psr\Http\Message\ResponseInterface;

abstract class BaseCrawler extends Injectable
{
    /**
     * @var Client
     */
    protected $client;
    protected $baseUrl;
    protected $crawledUrls = [];
    const INVALID_URL_PATH = ['#', '', 'void(0)'];
    const UNIQUES = [
        'unique_images',
        'unique_internal_urls',
        'unique_external_urls'
    ];

    public function __construct()
    {
        $this->client = new Client();
    }

    protected function getResponseAndStats(string $url): ?array
    {
        $result = [];

        try {
            $response = $this->client->get($url, [
                'on_stats' => function (TransferStats $stats) use (&$result) {
                    $result['stats'] = $stats;
                }
            ]);
            $result['response'] = $response;

        } catch (GuzzleException $e) {
            $this->di->get('logger')->error($e->getMessage());
            $result = null;
        }

        return $result;
    }

    public function clearCrawledUrls(): void
    {
        $this->crawledUrls = [];
    }

    public function canCrawl(string $url): bool
    {
        return $this->isInternal($url) || parse_url($url, PHP_URL_HOST) === $this->baseUrl;
    }

    public function isCrawled($url)
    {
        $url = $this->isInternal($url) ? $this->getFullInternalUrl($url) : $url;
        return key_exists($url, array_flip($this->crawledUrls));
    }

    public function getCrawledUrls(): array
    {
        return $this->crawledUrls;
    }

    protected function getFullInternalUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $trimCharacters = " \n\r\t\v\0/";
        return ltrim($this->baseUrl, $trimCharacters) . trim($path, $trimCharacters);
    }

    public final function crawl(string $url): array
    {
        $result = [];
        $url = $this->isInternal($url) ? $this->getFullInternalUrl($url) : $url;


        if ($this->canCrawl($url) && !$this->isCrawled($url)) {
            if ($data = $this->getResponseAndStats($url)) {
                $response = $data['response'];
                $stats = $data['stats'];
                $result = $this->prepareResult($response, $stats);
                $this->crawledUrls[] = $url;
            }
        }
        return $result;
    }

    protected function isInternal($url)
    {
        return UrlHelper::isInternal($this->baseUrl, $url);
    }

    protected function isValidUrlPath(?string $urlPath)
    {
        return !is_null($urlPath) && (array_search($urlPath, static::INVALID_URL_PATH) === false);
    }

    protected function prepareResult(ResponseInterface $response, TransferStats $stats): array
    {
        $result = [];
        $content = $response->getBody()->getContents();
        $xpathDoc = new DOMDocument();
        libxml_use_internal_errors(true);
        if ($xpathDoc->loadHTML($content)) {
            $xpathDoc = new DOMXPath($xpathDoc);
            $title = $xpathDoc->query('/html/head/title')->item(0)->textContent;
            $imageElements = $xpathDoc->query('//img');
            $uniqueImages = [];
            foreach ($imageElements as $el) {
                $src = $el->getAttribute('src');
                $uniqueImages[sha1($src)] = $src;
            }

            $anchors = $xpathDoc->query('//a');
            $uniqueInternalUrls = [];
            $uniqueExternalUrls = [];
            foreach ($anchors as $anchor) {
                $href = $anchor->getAttribute('href');
                if (is_string($href)) {
                    if (
                        UrlHelper::isInternal($this->baseUrl, $href) &&
                        $this->isValidUrlPath(parse_url($href, PHP_URL_PATH))
                    ) {
                        $uniqueInternalUrls[sha1($href)] = $href;
                    } else {
                        $uniqueExternalUrls[sha1($href)] = $href;
                    }
                }
            }


            $pageLoad = $stats->getTransferTime();
            $result = [
                'title' => $title,
                'url' => (string)$stats->getRequest()->getUri(),
                'status_code' => $response->getStatusCode(),
                'unique_images' => $uniqueImages,
                'page_load' => $pageLoad,
                'word_count' => str_word_count($xpathDoc->document->textContent),
                'unique_internal_urls' => $uniqueInternalUrls,
                'unique_external_urls' => $uniqueExternalUrls,
                'title_length' => strlen($title)
            ];
        }
        libxml_clear_errors();

        return $result;

    }
}