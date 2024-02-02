<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Contracts\Redis\Connection;
// use Illuminate\Support\Facades\Cache;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Redis;
// use Illuminate\Support\Facades\Session;
use GuzzleHttp\RequestOptions;

use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

use App\Models\Job;
use App\Http\Resources\JobResource;
use App\Http\Requests\JobRequest;

use App\Observers\CustomCrawlerObserver;

class JobController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/jobs",
     *     tags={"job"},
     *     summary="Create job",
     *     operationId="store",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/JobRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\RequestBody(
     *         description="job placed for purchasing th pet",
     *         required=true,
     *     )
     * )
     * @param JobRequest $request
     */
    public function store(JobRequest $request)
    {
        //
        $urls = $request->get('urls', []);
        $ids = [];
        foreach ($urls as $url) {
            $jobModel = new Job();
            $createJob = $jobModel->addJob([
                'status' => 'pending'
            ]);

            $id = $createJob["id"];
            $ids[] = $id;

            Crawler::create([RequestOptions::ALLOW_REDIRECTS => true, RequestOptions::TIMEOUT => 30])
            ->acceptNofollowLinks()
            ->ignoreRobots()
            // ->setParseableMimeTypes(['text/html', 'text/plain'])
            ->setCrawlObserver(new CustomCrawlerObserver($id))
            ->setCrawlProfile(new CrawlInternalUrls($url))
            ->setMaximumResponseSize(1024 * 1024 * 2) // 2 MB maximum
            ->setTotalCrawlLimit(1) // limit defines the maximal count of URLs to crawl
            // ->setConcurrency(1) // all urls will be crawled one by one
            ->setDelayBetweenRequests(100)
            ->startCrawling($url);
        }

        return $ids;
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/jobs/{id}",
     *     tags={"job"},
     *     description="Get Job by Id",
     *     summary="Get detail job",
     *     operationId="show",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of pet that needs to be fetched",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        //
        $jobModel = new Job();
        $job = $jobModel->getJob($id);
        
        return new JobResource($job);
    }


    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/jobs/{id}",
     *     tags={"job"},
     *     description="Delete Job by Id",
     *     summary="Delete job",
     *     operationId="destroy",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of pet that needs to be fetched",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        //
        $jobModel = new Job();
        $jobModel->deleteJob($id);
        
        return true;
    }
}
