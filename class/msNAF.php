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
 * Accès classification NAF 
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

class msNAF
{

    public function getNAFniveaux($restric = [])
    {
        $where = '';
        $sqlImplode['execute'] = [];
        if (!empty($restric) and is_array($restric)) {
            $sqlImplode = msSQL::sqlGetTagsForWhereIn($restric);
            $where = "where t.sousclasse in (" . $sqlImplode['in'] . ")";
        }

        if ($NAFtous = msSQL::sql2tab("SELECT t.*,
                s.label as label_section,
                d.label as label_division,
                g.label as label_groupe,
                c.label as label_classe,
                sc.label as label_sousclasse
                from naf_tousniveaux as t 
                left join naf_sections as s on t.section = s.code
                left join naf_divisions as d on t.division = d.code
                left join naf_groupes as g on t.groupe = g.code
                left join naf_classes as c on t.classe = c.code
                left join naf_sousclasses as sc on t.sousclasse = sc.code
                $where 
                order by t.section asc, t.division asc, t.groupe asc, t.classe asc, t.sousclasse", $sqlImplode['execute'])) {
            return $NAFtous;
        } else {
            return [];
        }
    }

    public function getNAFtotaux($restric = [])
    {
        $where = '';
        $sqlImplode['execute'] = [];
        if (!empty($restric) and is_array($restric)) {
            $sqlImplode = msSQL::sqlGetTagsForWhereIn($restric);
            $where = "where sousclasse in (" . $sqlImplode['in'] . ")";
        }

        if ($NAFtotaux = msSQL::sqlUnique("SELECT 
                count(distinct(section)) as sections,  
                count(distinct(division)) as divisions, 
                count(distinct(groupe)) as groupes, 
                count(distinct(classe)) as classes, 
                count(distinct(sousclasse)) as sousclasses  
                FROM naf_tousniveaux $where", $sqlImplode['execute'])) {
            return $NAFtotaux;
        } else {
            return [];
        }
    }
}
