<?php
declare(strict_types=1);

namespace MyVendor\Task\Resource\App;

use Aura\Sql\ExtendedPdoInterface;
use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use BEAR\Resource\ResourceObject;
use Koriym\Now\NowInterface;
use Ray\Di\Di\Assisted;

/**
 * with assisted injection + query builder
 */
class TaskQb extends ResourceObject
{
    /**
     * @Assisted({"pdo", "select"})
     */
    public function onGet(string $id = null, ExtendedPdoInterface $pdo = null, SelectInterface $select = null) : ResourceObject
    {
        $select->cols(['id', 'title', 'completed'])->from('task');
        if ($id) {
            return $this->onGetItem($id, $pdo, $select);
        }
        $sql = $select->getStatement();
        $this->body = $pdo->fetchAssoc($sql);

        return $this;
    }

    /**
     * @Assisted({"pdo", "insert", "now"})
     */
    public function onPost(string $title, ExtendedPdoInterface $pdo = null, InsertInterface $insert = null, NowInterface $now = null) : ResourceObject
    {
        $params = [
            'title' => $title,
            'created' => (string) $now,
            'completed' => false
        ];
        $insert
            ->into('task')
            ->cols(['title', 'completed', 'created'])
            ->bindValues($params);
        $pdo->perform($insert->getStatement(), $insert->getBindValues());
        $name = $insert->getLastInsertIdName('id');
        $id = $pdo->lastInsertId($name);
        $this->code = 201;
        $this->headers['Location'] = "/task?id={$id}";

        return $this;
    }

    /**
     * @Assisted({"pdo", "query"})
     */
    public function onPatch(string $id, ExtendedPdoInterface $pdo = null, UpdateInterface $update = null) : ResourceObject
    {
        $values = [
            'id' => $id,
            'completed' => true
        ];
        $update
            ->table('task')
            ->cols(['title', 'completed', 'created'])
            ->where('id = :id')
            ->bindValues($values);
        $pdo->perform($update->getStatement(), $update->getBindValues());

        return $this;
    }

    private function onGetItem(string $id, ExtendedPdoInterface $pdo, SelectInterface $select) : ResourceObject
    {
        $select->where('id = :id')->bindValue('id', $id);
        $this->body = $pdo->fetchOne($select->getStatement(), $select->getBindValues());

        return $this;
    }
}
