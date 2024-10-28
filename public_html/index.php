<?php
/*
* This file is part of MedShakeST.
*
* Copyright (c) 2022
* Bertrand Boutillier <b.boutillier@gmail.com>
* http://www.medshake.net
*
* MedShakeST is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* any later version.
*
* MedShakeST is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with MedShakeST. If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * index
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */


ini_set('display_errors', 0);
setlocale(LC_ALL, "fr_FR.UTF-8");

$homepath = dirname(__DIR__);
$homepath.=$homepath[strlen($homepath)-1]=='/'?'':'/';

/////////// Composer class auto-upload
require $homepath.'vendor/autoload.php';

/////////// Class MedShakeST auto-upload
spl_autoload_register(function ($class) {
    global $homepath;
    if (is_file($homepath.'class/' . $class . '.php')) {
        include $homepath.'class/' . $class . '.php';
    }
});

/////////// Config loader
$p['config']=yaml_parse_file($homepath.'config/config.yml');

/////////// SQL connexion
$pdo=msSQL::sqlConnect();

/////////// correction pour host non présent (IP qui change)
if (empty($p['config']['host'])) {
    $p['config']['host']=$_SERVER['HTTP_HOST'];
    $p['config']['cookieDomain']=$_SERVER['HTTP_HOST'];
}
$p['homepath']=$homepath;

////////// Routes
$match = msSystem::getRoutes();

///////// Controler
if ($match and is_file($homepath.'controlers/'.$match['target'].'.php')) {
    include $homepath.'controlers/'.$match['target'].'.php';
}  else {
    $template ='404';
}

//////// View if defined
if (isset($template)) {
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    if($template=='404') {
      http_response_code(404);
    }
    if($template=='forbidden') {
      http_response_code(403);
    }

    //générer et sortir le html
    $getHtml = new msGetHtml();
    $getHtml->set_template($template);
    echo $getHtml->genererHtml();
}
