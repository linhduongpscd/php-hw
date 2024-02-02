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

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        //Cache::forget('get_members_cache');

        /**
         * Just Enable the database query log to see query log in text
         * And find the query is from db or cache
         */
        DB::connection()->enableQueryLog();
        $jobs = Job::getAll();
        // $log = DB::getQueryLog();
        //print_r($log);

        return new JobResource($jobs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $jobModel = new Job();
        $createJob = $jobModel->addJob($request);
        
        return new JobResource($createJob);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $jobModel = new Job();
        $job = $jobModel->getJob($id);
        
        return new JobResource($job);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $jobModel = new Job();
        $updateJob = $jobModel->updateJob($request);

        return new JobResource($updateJob);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $jobModel = new Job();
        $job = $jobModel->deleteJob($id);
        
        return new JobResource($job);
    }
}
