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

    public function sortbyfield($content, $sort_by = null, $direction = 'asc'){
        if (is_a($content, 'Doctrine\Common\Collections\Collection')) {
            $content = $content->toArray();
        }

        if (!is_array($content)) {
            throw new \InvalidArgumentException('Variable passed to the sortByField filter is not an array');
        } elseif (count($content) < 1) {
            return $content;
        } elseif ($sort_by === null) {
            throw new Exception('No sort by parameter passed to the sortByField filter');
        } elseif (!self::isSortable(current($content), $sort_by)) {
            throw new Exception('Entries passed to the sortByField filter do not have the field "' . $sort_by . '"');
        } else {
            // Unfortunately have to suppress warnings here due to __get function
            // causing usort to think that the array has been modified:
            // usort(): Array was modified by the user comparison function
            @usort($content, function ($a, $b) use ($sort_by, $direction) {
                $flip = ($direction === 'desc') ? -1 : 1;

                if (is_array($a))
                    $a_sort_value = $a[$sort_by];
                else if (method_exists($a, 'get' . ucfirst($sort_by)))
                    $a_sort_value = $a->{'get' . ucfirst($sort_by)}();
                else
                    $a_sort_value = $a->$sort_by;

                if (is_array($b))
                    $b_sort_value = $b[$sort_by];
                else if (method_exists($b, 'get' . ucfirst($sort_by)))
                    $b_sort_value = $b->{'get' . ucfirst($sort_by)}();
                else
                    $b_sort_value = $b->$sort_by;

                if ($a_sort_value == $b_sort_value) {
                    return 0;
                } else if ($a_sort_value > $b_sort_value) {
                    return (1 * $flip);
                } else {
                    return (-1 * $flip);
                }
            });
        }
        return $content;
    }

    private static function isSortable($item, $field) {
        if (is_array($item))
            return array_key_exists($field, $item);
        elseif (is_object($item))
            return isset($item->$field) || property_exists($item, $field);
        else
            return false;
    }



}