<?php //>

namespace dungeons\model;

use Exception;
use dungeons\db\{Criteria,Model};

class User extends Model {

    public function queryById($id) {
        return $this->queryValidUser(['id' => $id]);
    }

    public function queryByUsername($username) {
        return $this->queryValidUser(['username' => $username]);
    }

    protected function before($type, $prev, $curr) {
        switch ($type) {
            case self::DELETE:
                if ($prev['id'] === 1) {
                    throw new Exception('Permission denied.');
                }
                break;
            case self::INSERT:
                $encrypt = isset($curr['password']);
                break;
            case self::UPDATE:
                if (isset($curr['password'])) {
                    $encrypt = ($curr['password'] !== $prev['password']);
                } else {
                    $curr['password'] = $prev['password'];
                }
                break;
        }

        if (!empty($encrypt)) {
            $curr['password'] = md5("{$curr['id']}::{$curr['password']}");
        }

        return $curr;
    }

    private function queryValidUser($conditions) {
        $begin = $this->table->begin_date;
        $expire = $this->table->expire_date;

        $today = date($begin->pattern());

        $conditions[] = $begin->notNull();
        $conditions[] = $begin->lessThanOrEqual($today);

        $conditions[] = Criteria::createOr($expire->isNull(), $expire->greaterThan($today));

        $conditions['disabled'] = false;

        return $this->find($conditions);
    }

}
