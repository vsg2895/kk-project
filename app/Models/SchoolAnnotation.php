<?php

namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SchoolAnnotation
 * @package Jakten\Models
 * @property int school_id
 * @property int annotation_id
 */
class SchoolAnnotation extends Model
{
    protected $fillable = [
        'school_id',
        'annotation_id'
    ];
}
