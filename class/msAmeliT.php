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
 * Accès data ameli Trajet 
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

class msAmeliT
{

    public function getAmeliTdataByNAF($restric = [])
    {
        $where = '';
        $sqlImplode['execute'] = [];
        if (!empty($restric) and is_array($restric)) {

            foreach ($restric as &$valeur) {
                $valeur = str_replace(".", "", $valeur);
            }
            
            $sqlImplode = msSQL::sqlGetTagsForWhereIn($restric);
            $where = "where NAF in (" . $sqlImplode['in'] . ")";
        }

        if ($NAFtous = msSQL::sql2tab("SELECT annee,
        NAF,
        SUM(AT1erReg) as AT1erReg,
        SUM(deces) as deces
        FROM `ameli_t` $where group by NAF, annee", $sqlImplode['execute'])) {
            foreach ($NAFtous as $v) {
                $nafRetour[$v['NAF']][$v['annee']] = $v;
            }

            return $nafRetour;
        } else {
            return [];
        }
    }
}
