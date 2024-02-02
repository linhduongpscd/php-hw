<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class Job extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    public $table = 'jobs';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'description'
    ];

    /**
     * StudentsModel constructor.
     */
    public function __construct()
    {
        //$this->storage = Redis::connection();
    }

    /**
     * @return mixed
     */
    public static function getAll()
    {
        $result = Cache::remember('get_jobs', 10, function (){
            // return self::orderBy('id', 'DESC')->get();
            return [];
        });

        return $result;
    }

    /**
     * @param $postData
     * @return bool
     */
    public function addJob($postData)
    {
        $this->id = substr(uniqid('', true), -4);;
        $this->name = $postData['name'];
        $this->description = $postData['description'];

        if ($this->save() === true) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public static function getJob($id)
    {
        $result = Cache::remember('get_job_details', 10, function () use ($id){
            return self::where('id', $id)->first();
        });

        return $result;
    }

    /**
     * @param $postData
     * @return bool
     */
    public function updateJob($postData)
    {
        //Cache::forget('get_member_id');
        $getData = Redis::get('get_job_id');

        if(!empty($getData) || $getData !== null){
            DB::connection()->enableQueryLog();
            $job = $this->find($postData->id);
            $log = DB::getQueryLog();
            //print_r($log);

            Redis::set('get_job_id', $job->id);

            $jobInfo = $this->find($job->id);
            $jobInfo->name = $postData->name;
            $jobInfo->description = $postData->description;

            if ($jobInfo->save() === true) {
                Redis::set('get_job_details', $jobInfo);
                return true;
            }

            return false;
        }
    }

    /**
     * @return mixed
     */
    public static function deleteJob($id)
    {
        $result = Cache::remember('get_job_details', 10, function () use ($id){
            return self::where('id', $id)->first();
        });

        return $result;
    }
}
