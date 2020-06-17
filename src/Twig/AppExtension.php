<?php 
namespace App\Twig;

use Exception;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('timeago', [$this, 'timeago']),
            new TwigFilter('sortbyfield', [$this, 'sortbyfield'])
        ];
    }

    public function timeago($datetime){
        
        $timeStamp = $datetime->getTimestamp();
        
            $time = time() - $timeStamp; 
        
            $units = array (
            31536000 => 'an',
            2592000 => 'mois',
            604800 => 'semaine',
            86400 => 'jour',
            3600 => 'heure',
            60 => 'minute',
            1 => 'seconde'
            );
        
            foreach ($units as $unit => $val) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return ($val == 'seconde')? 'Il y a quelques instants' : 
                    ' Il y a '.$numberOfUnits.' '.$val.(($numberOfUnits>1 && $val != 'mois') ? 's' : '');
            }
    }

}