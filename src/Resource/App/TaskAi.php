<?php
declare(strict_types=1);

namespace MyVendor\Task\Resource\App;

use Aura\Sql\ExtendedPdoInterface;
use BEAR\Resource\ResourceObject;
use Koriym\Now\NowInterface;
use Koriym\QueryLocator\QueryLocatorInterface;
use Ray\Di\Di\Assisted;

/**
 * with assisted injection
 */
class TaskAi extends ResourceObject
{
    /**
     * @Assisted({"pdo", "query"})
     */
    public function onGet(string $id = null, ExtendedPdoInterface $pdo = null, QueryLocatorInterface $query = null) : ResourceObject
    {
        $this->body = $id ?
            $pdo->fetchOne($query['task_item'], ['id' => $id]) :
            $pdo->fetchAssoc($query['task_list']);

        return $this;
    }

    /**
     * @Assisted({"pdo", "query", "now"})
     */
    public function onPost(string $title, ExtendedPdoInterface $pdo = null, QueryLocatorInterface $query = null, NowInterface $now = null) : ResourceObject
    {
        $params = [
            'title' => $title,
            'created' => (string) $now,
            'completed' => false
        ];
        $pdo->perform($query['task_insert'], $params);
        $id = $pdo->lastInsertId('id');
        $this->code = 201;
        $this->headers['Location'] = "/task?id={$id}";

        return $this;
    }

    /**
     * @Assisted({"pdo", "query"})
     */
    public function onPatch(string $id, ExtendedPdoInterface $pdo = null, QueryLocatorInterface $query = null) : ResourceObject
    {
        $params = [
            'id' => $id,
            'completed' => true
        ];
        $pdo->perform($query['task_update'], $params);

        return $this;
    }
}
