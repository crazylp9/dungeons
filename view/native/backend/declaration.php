<?php //>

use dungeons\Config;
use dungeons\Message;
use dungeons\Resource;

$cfg = Config::load('backend');
$labels = Message::load("table/{$action->table()->name()}");
$menus = Resource::loadMenu(explode('|', $cfg['menus']));
$path = preg_replace('/\/backend\/(.*)/', '$1', $action->name());
$node = $menus[$path];

$result['title'] = $node['title'];