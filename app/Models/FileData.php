<?php
/**
 * FileData
 */
namespace App\Models;


use Jenssegers\Mongodb\Eloquent\Model;

/**
 * FileData
 */
class FileData extends Model {

    protected $connection = 'mongodb';

    protected $fillable = [
        'data'
    ];

}
