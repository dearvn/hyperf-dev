<?php

declare(strict_types=1);

namespace App\Model\Website;

use App\Model\Model;

/**
 * Character model class
 * Class LandingPageTheme
 * @package App\Model\Website
 * @Author donald
 * @Date: 2023/5/05
 */
class LandingPageTheme extends Model
{
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'landingpage_themes';

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    protected $guarded = [];  
}