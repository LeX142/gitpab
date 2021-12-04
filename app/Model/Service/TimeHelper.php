<?php

namespace App\Model\Service;

use App\Model\Entity\Project;
use App\Model\Entity\Namespaces;
use App\Model\Entity\Contributor;

class TimeHelper
{
    public static function getHoursIntervalAsString(float $hoursAndMinutes): string
    {
        $hours = (int)$hoursAndMinutes;
        $minutes = 60 * ($hoursAndMinutes - $hours);
        return sprintf('%d hours %d minutes', $hours, $minutes);
    }

    public static function getFilenameByFilters(array $filters)
    {
        $res = 'spent-report-';
        $res .= !empty($filters['date_start']) ? $filters['date_start'].'_' : '';
        $res .= !empty($filters['date_end']) ? $filters['date_end'].'-' : '';
        foreach (['authors','projects','namespaces'] as $name){
            $res .= self::getNameFromIdList($name, $filters[$name] ?? []);
        }
        return $res;
    }

    public static function getNameFromIdList(string $name,array $idList)
    {
        $dictionary=[
            'authors'=> Contributor::query(),
            'projects'=> Project::query(),
            'namespaces'=> Namespaces::query()
        ];
        if (!empty($idList) && isset($dictionary[$name])){
            $authors = $dictionary[$name]->select(['name'])->whereIn('id',$idList)->cursor();
            $authors = array_column($authors->toArray(),'name');
            return implode('_',array_map(function($item){ return str_replace([' ',',','/','\\'],'_',$item);},$authors)).'-';
        }
        return '';
    }
}