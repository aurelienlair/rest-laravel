<?php
namespace App\Repositories;

use App\Entities\Actor;
use Illuminate\Database\ConnectionInterface;
use ArrayIterator;

class SqliteActorRepository implements ActorRepository
{
    private $connection;
    private $entityColumnsName = [
        'uuid',
        'firstname',
        'lastname',
        'country',
    ];
    private $sqlColumnsName = [
        'uuid',
        'first_name',
        'last_name',
        'country',
    ];

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

	public function findAll(): ?iterable
	{
        $columns = implode(',', $this->sqlColumnsName);
$query = <<<EOT
SELECT {$columns}
FROM actors
EOT;

        $data = $this->connection->select($query);

        return new ArrayIterator(
            array_map(
                function($tuple)
                {
                    return json_decode(json_encode($tuple), true);
                }, 
                $data
            )
        );
	}

	public function findBy(Criteria $criteria): ?iterable
	{
        $columns = implode(",", array_values($this->sqlColumnsName));
$query = <<<EOT
SELECT {$columns}
FROM actors
{$criteria->where()}
EOT;
        $data = $this->connection->select($query);

        return new ArrayIterator(
            array_map(
                function($tuple)
                {
                    $current = json_decode(json_encode($tuple), true);
                    return new Actor(
                        $current['first_name'],
                        $current['last_name'],
                        $current['country'],
                        $current['uuid']
                    );
                }, 
                $data
            )
        );
	}

	public function add(Actor $actor): void 
	{
        $columnMap = array_combine($this->entityColumnsName, $this->sqlColumnsName);
        $translatedColumns = [];
        foreach (array_keys($actor->export()) as $value) {
            $translatedColumns[$columnMap[$value]] = null;
        }

        $columns = "'" . implode("','", array_keys($translatedColumns)) . "'";
        $values = "'" . implode("','", array_values($actor->export())) . "'";

$query = <<<EOT
INSERT INTO actors
({$columns})
VALUES
({$values});
EOT;

        $this->connection->insert($query);
	}

	public function update(Actor $actor): void
	{
        $where = 'uuid = "' . $actor->export()['uuid'] . '"';
        $values = []; 
        $columnMap = array_combine($this->entityColumnsName, $this->sqlColumnsName);
        foreach ($columnMap as $entityColumnName => $sqlColumnName) {
            $values[] = "$sqlColumnName= \"" . $actor->export()[$entityColumnName] . "\""; 
        }
        $values = implode(", ", array_values($values));
$query = <<<EOT
UPDATE actors
SET 
{$values}
WHERE
{$where};
EOT;

        $this->connection->update($query);
	}

	public function remove(Actor $actor): void
	{
        $where = 'uuid = "' . $actor->export()['uuid'] . '"';
$query = <<<EOT
DELETE FROM actors
WHERE
{$where};
EOT;
        $this->connection->delete($query);
	}
}
