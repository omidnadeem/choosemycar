<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid as Generator;

trait Uuid
{
    /**
     * @return string
     */
    public static function getUuidKeyName()
    {
        return 'uuid';
    }

    /**
     * Boot uuid trait.
     *
     * @return void
     */
    protected static function bootUuid()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getUuidKeyName()})) {
                $model->{$model->getUuidKeyName()} = Generator::uuid4();
            }
        });

        static::saving(function ($model) {
           
            if (empty($model->{$model->getUuidKeyName()})) {
                $model->{$model->getUuidKeyName()} = Generator::uuid4();
            }
        });
    }
}