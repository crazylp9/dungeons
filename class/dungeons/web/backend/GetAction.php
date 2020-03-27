<?php //>

namespace dungeons\web\backend;

use dungeons\web\UserAction;

class GetAction extends UserAction {

    public function __construct() {
        parent::__construct();

        $this->view('backend/view.php');
    }

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}[\w-]+$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $args = $this->args();
        $data = (count($args) === 1) ? $this->table()->model()->get($args[0]) : null;

        if (!$data) {
            return ['error' => 'error.DataNotFound'];
        }

        return ['success' => true, 'data' => $data];
    }

}
