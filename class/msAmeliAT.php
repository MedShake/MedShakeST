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
 * Accès data ameli AT 
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

class msAmeliAT
{

    public function getAmeliATdataByNAF($restric = [])
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
        SUM(NbSalariesEnActiviteOuChoPar) as NbSalariesEnActiviteOuChoPar,
        SUM(nbHeuresTrav) as nbHeuresTrav,
        SUM(nbSiret) as nbSiret,
        SUM(AT1erReg) as AT1erReg,
        SUM(AT1erReg4joursArret) as AT1erReg4joursArret,
        SUM(nouvellesIP) as nouvellesIP,
        SUM(nouvellesIPTauxInf10Pct) as nouvellesIPTauxInf10Pct,
        SUM(nouvellesIPTauxSup10Pct) as nouvellesIPTauxSup10Pct,
        SUM(deces) as deces,
        SUM(journeesIT) as journeesIT,
        SUM(sommeTauxIPInf10Pct) as sommeTauxIPInf10Pct,
        SUM(sommeTauxIP) as sommeTauxIP,
        SUM(manutentionManuelle) as manutentionManuelle,
        SUM(chutesPlainPied) as chutesPlainPied,
        SUM(outillageMain) as outillageMain,
        SUM(risqueMachine) as risqueMachine,
        SUM(chutesHauteur) as chutesHauteur,
        SUM(agressions) as agressions,
        SUM(manutentionMecanique) as manutentionMecanique,
        SUM(risquePhysiqueDontRisqueElec) as risquePhysiqueDontRisqueElec,
        SUM(risqueChimique) as risqueChimique,
        SUM(risqueRoutier) as risqueRoutier,
        SUM(autresRisques) as autresRisques,
        SUM(autresVehiculesTransport) as autresVehiculesTransport,
        (SUM(AT1erReg) / SUM(NbSalariesEnActiviteOuChoPar) * 1000) as indicateurIF,
        (SUM(AT1erReg) / SUM(nbHeuresTrav) * 1000000) as indicateurTF,
        (SUM(journeesIT) / SUM(nbHeuresTrav) * 1000) as indicateurTG,
        (SUM(sommeTauxIP) / SUM(nbHeuresTrav) * 1000000) as indicateurIG
        FROM `ameli_at` $where group by NAF, annee", $sqlImplode['execute'])) {
            foreach ($NAFtous as $v) {
                $nafRetour[$v['NAF']][$v['annee']] = $v;
            }

            return $nafRetour;
        } else {
            return [];
        }
    }
}
