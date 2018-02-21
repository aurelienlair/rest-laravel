<?php
namespace App\Repositories;

use Tests\TestCase;

class SqlCriteriaTest extends TestCase
{
    public function testWhereCriteria()
    {
        $criteria = SqlCriteria::from([
            'firstname' => 'Ewan',
            'lastname' => 'McGregor',
            'country' => 'GB',
        ]);
        
$expectedQuery = <<<EOT
WHERE firstname = 'Ewan'
AND lastname = 'McGregor'
AND country = 'GB';
EOT;

        $this->assertEquals($expectedQuery,$criteria->where());
    }
}
