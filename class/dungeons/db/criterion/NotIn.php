<?php //>

namespace dungeons\db\criterion;

class NotIn extends AbstractCriterion {

    public function make() {
        $count = count($this->values);

        switch ($count) {
            case 0:
                return false;

            case 1:
                return "{$this->columnName()} <> ?";

            default:
                $values = implode(',', array_fill(0, $count, '?'));
                return "{$this->columnName()} NOT IN ({$values})";
        }
    }

}
