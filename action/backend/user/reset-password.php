<?php //>

use dungeons\Message;
use dungeons\web\Session;

return new class() extends dungeons\web\UserAction {

    public function __construct() {
        parent::__construct();

        $this->validationView('backend/validation.php');
        $this->view('backend/user/reset-password-success.twig');
    }

    protected function process($form) {
        $user = $this->user();
        $user['password'] = $form['password'];

        $user = model('User')->update($user);

        if (is_null($user)) {
            return ['error' => 'backend-login.error.UserNotFound'];
        }

        if ($user === false) {
            return ['error' => 'backend-login.error.ResetPasswordFailed'];
        }

        Session::set('User', $user);

        return ['success' => true];
    }

    protected function validate($form) {
        $errors = [];
        $user = $this->user();

        $current = @$form['current'];

        if (is_null($current)) {
            $errors[] = ['name' => 'current', 'type' => 'required'];
        } else if ($user['password'] !== md5($user['id'] . '::' . $current)) {
            $errors[] = ['name' => 'current', 'message' => Message::get('backend-login.error.PasswordNotMatched')];
        }

        $password = @$form['password'];

        if (is_null($password)) {
            $errors[] = ['name' => 'password', 'type' => 'required'];
        } else if ($password === $current) {
            $errors[] = ['name' => 'password', 'message' => Message::get('backend-login.error.PasswordNotChanged')];
        }

        $confirm = @$form['confirm'];

        if (is_null($confirm)) {
            $errors[] = ['name' => 'confirm', 'type' => 'required'];
        } else if ($confirm !== $password) {
            $errors[] = ['name' => 'confirm', 'message' => Message::get('backend-login.error.PasswordNotConfirmed')];
        }

        return $errors;
    }

};
