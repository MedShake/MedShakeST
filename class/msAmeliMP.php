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
 * Accès data ameli MP
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

class msAmeliMP
{

    public function getAmeliMPdataByNAF($restric = [])
    {
        $where = '';
        $sqlImplode['execute'] =[];
        if (!empty($restric) and is_array($restric)) {
            
            foreach ($restric as &$valeur) {
                $valeur = str_replace(".", "", $valeur);
            }
            
            $sqlImplode = msSQL::sqlGetTagsForWhereIn($restric);
            $where = "where NAF in (" . $sqlImplode['in'] . ")";
        }

        if ($NAFtous = msSQL::sql2tab("SELECT 
            NAF, SUM(MP1erReg) as nb, SUM(deces) as deces, MP, MPlabel, annee
            from `ameli_mp` 
            $where 
            group by MP, MPlabel, annee, NAF 
            order by annee, NAF, nb desc ", $sqlImplode['execute'])) 
        {
            foreach($NAFtous as $v) {
                $nafRetour[$v['NAF']][$v['annee']]['mpliste'][] = array(
                    'MP'=>$v['MP'],
                    'nb'=>$v['nb'],
                    'MPlabel'=>$v['MPlabel']
                );
                if(!isset($nafRetour[$v['NAF']][$v['annee']]['MP1erReg'])) $nafRetour[$v['NAF']][$v['annee']]['MP1erReg'] = 0;
                $nafRetour[$v['NAF']][$v['annee']]['MP1erReg'] += $v['nb'];

                if(!isset($nafRetour[$v['NAF']][$v['annee']]['deces'])) $nafRetour[$v['NAF']][$v['annee']]['deces'] = 0;
                $nafRetour[$v['NAF']][$v['annee']]['deces'] += $v['deces'];
            }

            return $nafRetour;
        } else {
            return [];
        }
    }
}
