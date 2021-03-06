<?php //>

namespace dungeons\db\column;

use PDO;
use dungeons\db\Column;

class Text extends Column {

    public function convert($value) {
        return strval($value);
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
