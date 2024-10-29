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
 * Outils divers. Class issue de MedShakeEHR
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */
class msTools
{


    /**
     * Vérifier l'existence d'une arbo et la construire sinon
     * @param  string $dirName arborescence
     * @param  string $rights  droits
     * @return void
     */
    public static function checkAndBuildTargetDir($dirName, $rights = 0777)
    {
        if (!is_dir($dirName)) {
            mkdir($dirName, $rights, true);
        }
    }

    /**
     * Obtenir le mimetype d'un fichier
     * @param  string $file le fichier avec son chemin
     * @return array
     */
    public static function getmimetype($file)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($file);
    }

    /**
     * Convertir un ficher texte iso en UTF8 sur lui même ou vers un autre fichier
     * @param  string $source      fichier source
     * @param  string $destination fichier destination
     * @return bool              true/false
     */
    public static function convertPlainTextFileToUtf8($source, $destination = '')
    {
        if (!$destination) $destination = $source;
        $contenu = file_get_contents($source);
        if (!mb_detect_encoding($contenu, 'UTF-8', true)) {
            $contenu = mb_convert_encoding($contenu, 'UTF-8', mb_detect_encoding($contenu, null, false));
            return (bool)file_put_contents($destination, $contenu);
        } elseif ($destination != $source) {
            return (bool)file_put_contents($destination, $contenu);
        } else {
            return true;
        }
    }

    /**
     * Obtenir tous les sous repertoires d'un répertoire, avec récursivité
     * @param  string $directory           répertoire racine
     * @param  string $directory_seperator séparateur de répertoire dans le chemin (/)
     * @return array                      array des répertoires et sous répertoires
     */
    public static function getAllSubDirectories($directory, $directory_seperator)
    {
        $dirs = array_map(function ($item) use ($directory_seperator) {
            return $item . $directory_seperator;
        }, array_filter(glob($directory . '*'), 'is_dir'));

        foreach ($dirs as $dir) {
            $dirs = array_merge($dirs, msTools::getAllSubDirectories($dir, $directory_seperator));
        }

        return $dirs;
    }
}
