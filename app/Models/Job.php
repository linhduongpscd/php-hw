<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Cache;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
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
        return [];
    }

    /**
     * @param $postData
     * @return bool
     */
    public function addJob($postData)
    {
        $id = substr(uniqid('', true), -4);
        $data = $postData;
        $data['id'] = $id;

        Redis::set('JOB_' . $id, json_encode($data));
        return $data;
    }

    /**
     * @return mixed
     */
    public static function getJob($id)
    {
        $job = Redis::get('JOB_' . $id);
        return json_decode($job, true);
    }

    /**
     * @param $postData
     * @return bool
     */
    public function updateJob($postData, $id)
    {
        $data = Redis::get('JOB_' . $id);
        $getData = json_decode($data, true);

        if(!empty($getData) || $getData !== null){
            Redis::del('JOB_' . $id);

            $data = $postData;
            $data['id'] = $id;

            Redis::set('JOB_' . $id, json_encode($data));
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public static function deleteJob($id)
    {
        $delete = Redis::del('JOB_' . $id);

        return !!$delete ? true : false;
    }
}
