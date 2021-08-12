<?php


namespace Hsvisus\Attendance;

use Illuminate\Support\Facades\Facade;

class FacadeService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'attendance';
    }
}
