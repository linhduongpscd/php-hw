<?php

namespace App\Observers;

use DOMDocument;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Illuminate\Support\Facades\Log;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

use App\Models\Job;

class CustomCrawlerObserver extends CrawlObserver {
  
    private $content;
    private $id;

    public function __construct($id) {
        $this->content = NULL;
        $this->id = $id;
    }  
    /**
     * Called when the crawler will crawl the url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     */
    public function willCrawl(UriInterface $url, ?string $linkText): void
    {
        Log::info('willCrawl', ['url'=>$url]);
    }

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null,
    ): void 
    {
        $doc = new DOMDocument();
        @$doc->loadHTML($response->getBody());
        //# save HTML 
        $content = $doc->saveHTML();
        //# convert encoding
        $content1 = mb_convert_encoding($content,'UTF-8',mb_detect_encoding($content,'UTF-8, ISO-8859-1',true));
        //# strip all javascript
        $content2 = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content1);
        //# strip all style
        $content3 = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content2);
        //# strip tags
        $content4 = str_replace('<',' <',$content3);
        $content5 = strip_tags($content4);
        $content6 = str_replace( '  ', ' ', $content5 );
        //# strip white spaces and line breaks
        $content7 = preg_replace('/\s+/S', " ", $content6);
        //# html entity decode - ö was shown as &ouml;
        $html = html_entity_decode($content7);
        //# append
        $this->content .= $html;

        // save to redis
        $jobModel = new Job();
        $save_data = [
            'status' => 'success',
            'content' => $this->content,
        ];
        $jobModel->updateJob($save_data, $this->id);
        
        Log::info("crawled", ['id' => $this->id, 'content'=>$this->content]);
    }

     /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \GuzzleHttp\Exception\RequestException $requestException
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null,
    ): void 
    {
        $jobModel = new Job();
        $save_data = [
            'status' => 'fail',
        ];
        $jobModel->updateJob($save_data, $this->id);

        Log::error('crawlFailed',['url'=>$url,'error'=>$requestException->getMessage()]);
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
        Log::info("finishedCrawling");
        //# store $this->content in DB 
        //# Add logic here
    }
}