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
 * Actions ajax > upload CSV
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

if(!isset($_FILES['file']) and !is_int($_POST['randomNumber'])) die;

$fichier=$_FILES['file'];
$mimetype=msTools::getmimetype($fichier['tmp_name']);
echo $mimetype;
$acceptedtypes=array(
    'text/csv'=>'csv',
    'text/plain'=>'csv'
);

if (array_key_exists($mimetype, $acceptedtypes)) {
    $ext=$acceptedtypes[$mimetype];

    //folder
    $folder=$ext;

    //creation folder si besoin
    msTools::checkAndBuildTargetDir($p['config']['stockageLocation']. $folder.'/');

    $destination_file = $p['config']['stockageLocation']. $folder.'/'.$_POST['randomNumber'].'.'.$ext;
    move_uploaded_file($fichier['tmp_name'], $destination_file);
    msTools::convertPlainTextFileToUtf8($destination_file);
}