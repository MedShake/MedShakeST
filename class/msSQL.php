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
 * Fonctions MySQL. Class issue de MedShakeEHR
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 * @contrib fr33z00 <https://github.com/fr33z00>
 */

class msSQL
{

    /**
     * Se connecter à la base
     * @return resource connexion
     */
    public static function sqlConnect()
    {
        global $p;
    
        if (isset($_ENV['SQL_SERVEUR'])) {
            $pdo = new PDO('mysql:host=' . $_ENV['SQL_SERVEUR'] . ';dbname=' . $_ENV['SQL_BASE'] . ';charset=utf8', $_ENV['SQL_USER'], $_ENV['SQL_PASS']);
        } elseif (!empty($p['config']['sqlServeur'])) {
            $pdo = new PDO('mysql:host=' . $p['config']['sqlServeur'] . ';dbname=' . $p['config']['sqlBase'] . ';charset=utf8', $p['config']['sqlUser'], $p['config']['sqlPass']);
        }
        if (!$pdo) {
            die('Echec de connexion à la base de données');
        } else {
            if (isset($p['config']['sqlTimeZone'])) {
                $request = $pdo->prepare('SET time_zone = :sqlTimeZone ');
                $request->execute(['sqlTimeZone' => $p['config']['sqlTimeZone']]);
            }
            return $pdo;
        }
    }

    /**
     * Fonction query de base
     * @param  string $sql commande SQL
     * @param  array $data marqueurs nommés
     * @return resource      résultat mysql
     */
    public static function sqlQuery($sql, $data = [])
    {
        global $pdo;
        $request = $pdo->prepare($sql);
        if ($request->execute($data)) {
            return $request;
        } else {
            return null;
        }
    }

    /**
     * Sortir une ligne unique en Array
     * @param  string $sql commande SQL
     * @param  array $data marqueurs nommés
     * @return array      array
     */
    public static function sqlUnique($sql, $data = [])
    {
        $request = self::sqlQuery($sql, $data);
        if ($request and $request->rowCount() === 1) {
            return $request->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    /**
     * Sortir des lignes en array
     * @param  string $sql commande SQL
     * @param  array $data marqueurs nommés
     * @return array      array
     */
    public static function sql2tab($sql, $data = [])
    {
        $request = self::sqlQuery($sql, $data);
        if ($request and $request->rowCount() > 0) {
            return $request->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    /**
     * Obtenir les éléments pour la requête préparée avec clause WHERE ... IN
     *
     * @param array $data data qui vont passer dans la clause where ... in (...)
     * @param string $prefix prefix à appliquer aux tags à générer
     * @return array in => la chaine à placer dans IN(), execute => le tableau des marqueurs à merger
     */
    public static function sqlGetTagsForWhereIn($data, $prefix = 'tag')
    {

        if (!empty($data)) {
            $executeArray = [];
            foreach ($data as $k => $v) {
                $executeArray[$prefix . $k] = $v;
            }

            $tagsInString = ':' . implode(', :', array_keys($executeArray));
            return ['execute' => $executeArray, 'in' => $tagsInString];
        }
        return ['execute' => [], 'in' => "''"];
    }

}
