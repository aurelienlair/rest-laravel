<?php
namespace App\Repositories;

use Illuminate\Database\Query\Builder;
use PDO;

class SqlCriteria implements Criteria
{
    private $where;

    private function __construct(array $parameters)
    {
        $this->where = substr(
            implode(
                PHP_EOL,
                array_map(
                    function($key, $value)
                    {
                        return "AND {$key} = '{$value}'";
                    },
                    array_keys($parameters),
                    array_values($parameters)
                )
            ),
            4
        );
       
       $this->where .= ';';
    } 

    public static function from(array $parameters)
    {
        return new self($parameters);
    }

    public function where()
    {
        return <<<EOT
WHERE {$this->where}
EOT;
    }
}
