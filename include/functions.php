<?php //>

use Monolog\Logger;
use Monolog\Handler\{FirePHPHandler,RotatingFileHandler};
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use dungeons\Resource;

function base64_urldecode($data) {
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
}

function base64_urlencode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

function create_folder($path) {
    if (is_dir($path) && is_writable($path)) {
        return $path;
    }

    if (file_exists($path)) {
        return false;
    }

    $parent = create_folder(dirname($path));

    if ($parent === false) {
        return false;
    }

    $mask = umask(0);
    mkdir($path, 0777);
    umask($mask);

    return $path;
}

function isolate_require() {
    return require func_get_arg(0);
}

function logger($name) {
    static $loggers = [];

    if (!key_exists($name, $loggers)) {
        $handlers = [];

        if (defined('APP_LOG') && is_dir(APP_LOG) && is_writable(APP_LOG)) {
            $handlers[] = new RotatingFileHandler(APP_LOG . $name);
        }

        if (defined('DEBUG') && DEBUG) {
            $handlers[] = new FirePHPHandler();
        }

        $loggers[$name] = new Logger($name, $handlers);
    }

    return $loggers[$name];
}

function model($name) {
    return table($name)->model();
}

function render($template, $data) {
    $twig = new Environment(new ArrayLoader(['template' => $template]));

    return $twig->render('template', $data);
}

function table($name) {
    $table = Resource::load("table/{$name}.php");

    if ($table) {
        return $table->name($name);
    }

    throw new Exception("Table `{$name}` not found.");
}

function validate($value, $options) {
    foreach (preg_split('/\|/', $options->validation(), 0, PREG_SPLIT_NO_EMPTY) as $type) {
        if (!Resource::load("validator/{$type}.php")->validate($value, $options)) {
            return $type;
        }
    }

    return null;
}
