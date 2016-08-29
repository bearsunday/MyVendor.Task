<?php
declare(strict_types=1);

namespace MyVendor\Task\Resource\App;

use BEAR\Resource\ResourceObject;
use Koriym\Now\NowInject;
use Koriym\QueryLocator\QueryLocatorInject;
use Ray\AuraSqlModule\AuraSqlInject;

class Task extends ResourceObject
{
    use AuraSqlInject;
    use NowInject;
    use QueryLocatorInject;

    public function onGet(string $id = null) : ResourceObject
    {
        $this->body = $id ?
            $this->pdo->fetchOne($this->query['task_item'], ['id' => $id]) :
            $this->pdo->fetchAssoc($this->query['task_list']);

        return $this;
    }

    public function onPost(string $title) : ResourceObject
    {
        $params = [
            'title' => $title,
            'created' => $this->now,
            'completed' => false
        ];
        $this->pdo->perform($this->query['task_insert'], $params);
        $id = $this->pdo->lastInsertId('id');
        $this->code = 201;
        $this->headers['Location'] = "/task?id={$id}";

        return $this;
    }

    public function onPatch(string $id) : ResourceObject
    {
        $params = [
            'id' => $id,
            'completed' => true
        ];
        $this->pdo->perform($this->query['task_update'], $params);

        return $this;
    }
}
