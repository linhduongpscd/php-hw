<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Redis\Connection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

use App\Models\Job;
use App\Http\Resources\JobResource;
use App\Http\Requests\JobRequest;

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
        $jobModel = new Job();
        $createJob = $jobModel->addJob($request);
        
        return new JobResource($createJob);
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/job/{id}",
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
     *             maximum=10,
     *             minimum=1
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
     *     path="/api/job/{id}",
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
     *             maximum=10,
     *             minimum=1
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
        $job = $jobModel->deleteJob($id);
        
        return new JobResource($job);
    }
}
