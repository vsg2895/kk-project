<?php namespace Jakten\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use \Closure;

/**
 * Class Search
 * @package Jakten\Helpers
 */
class Search
{
    /**
     * @param $query
     * @param Builder $queryBuilder
     * @param string $table
     * @param Closure $handler
     * @return Builder
     *
     */
    public static function search($query, Builder $queryBuilder, $table, Closure $handler, $searchNames = false)
    {
        if (!$query) {
            return $queryBuilder;
        }

        $identifiers = [];
        $names = [];
        $terms = [];

        // extract queries in quotation marks
        preg_match_all('/"(.*?)"/', $query, $quoteQueries);
        for ($i=0; $i < count($quoteQueries[0]); $i++) { 
            $query = str_replace($quoteQueries[0][$i], "", $query);
            $terms[] = $quoteQueries[1][$i];
        }

        // split the remaining query in single terms
        $query = str_replace(',', ' ', $query);
        $query = explode(' ', $query);

        // parse input terms
        for ($i=0; $i < count($query); $i++) {
            $q = trim($query[$i]);
            if ($q == '') { continue; }

            if (preg_match('/^[#]\d*$/', $q)) {
                $identifiers[] = intval(substr($q, 1));
            }elseif (preg_match('/^[@]\w*$/', $q) && $i < count($query) - 1) {
                $names[] = [substr($q, 1), trim($query[$i + 1])];
                $i++;
            }elseif (preg_match('/^[@]\w*$/', $q)) {
                $names[] = [substr($q, 1)];
            }else {
                $terms[] = $q;
            }
        }

        // filter by string terms
        if (count($terms) > 0) {
            $handler($queryBuilder, $terms);
        }

        // filter by order id
        if (count($identifiers)) {
            $queryBuilder->whereIn($table . '.id', $identifiers);
        }

        // filter by names
        if (count($names) && $searchNames) {
            // try joining the user table if it's not joined yet
            if ($table != 'users' && !self::checkIfJoinExists($queryBuilder, 'users')) {
                $queryBuilder->join('users', $table . '.user_id', '=', 'users.id');
            }

            foreach ($names as $name) {
                if (count($name) == 1) {
                    $queryBuilder->where(function($query) use ($name) {
                        $query->where('users.given_name', 'like', '%' . $name[0] . '%')
                            ->orWhere('users.family_name', 'like', '%' . $name[0] . '%');
                    });
                }else {
                    $queryBuilder->where(function($query) use ($name) {
                        $query->where('users.given_name', 'like', '%' . $name[0] . '%')
                            ->where('users.family_name', 'like', '%' . $name[1] . '%');
                    });
                }
            }
        }

        return $queryBuilder;
    }

    /**
     * @param Builder $query
     * @param string $table
     * @return bool
     */
    public static function checkIfJoinExists(Builder $query, string $table) {
        if (!$query->getQuery()->joins) {
            return false;
        }

        foreach ($query->getQuery()->joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Request $request
     */
    public static function notifyIfNameSearchUsed(Request $request) {
        if (preg_match('/[@]\w*/', $request->get('s'))) {
            $request->session()->flash('info', 'The @handle doesn\'t work in this context and was ignored.');
        }
    }
}
