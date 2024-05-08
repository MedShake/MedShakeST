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
 * Analyser CSV
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

$template = "analysercsv";

if(isset($match['params']['csv']) and !is_numeric($match['params']['csv'])) die;
$file = $p['config']['stockageLocation'].'csv/'.$match['params']['csv'].'.csv';
$p['page']['CSVid'] = $match['params']['csv'];

$csvfile = new msCSV;

if(!$csvfile->setCSFfile($file)) {
    $template = "404";
    return;
}

$p['page']['listingCSV'] = $csvfile->readCSV();

if(!is_array($p['page']['listingCSV']) and is_string($p['page']['listingCSV'])) {
    $template = "ErreurCSV";
    $p['page']['error']=$p['page']['listingCSV'];
    return;
}


$sortedCSVdata = $csvfile->getSortedCSVdata();
$restrictionCodesNAF = array_keys($sortedCSVdata);

$naf = new msNAF;
$NAFniveaux = $naf->getNAFniveaux($restrictionCodesNAF);
$p['page']['NAFniveaux'] = $NAFniveaux;

// Data Ameli
$ameliAT = new msAmeliAT;
$p['page']['ameli']['AT'] = $ameliAT->getAmeliATdataByNAF($restrictionCodesNAF);
$ameliMP = new msAmeliMP;
$ameliMPdatas = $ameliMP->getAmeliMPdataByNAF($restrictionCodesNAF);
$p['page']['ameli']['MP'] = $ameliMPdatas [0];
$p['page']['ameli']['MPstats'] = $ameliMPdatas [1];

$ameliT = new msAmeliT;
$p['page']['ameli']['T'] = $ameliT->getAmeliTdataByNAF($restrictionCodesNAF);

// Stats 
$p['page']['stats']['CSV']=array(
    'totalEntreprises' => $csvfile->getNombreTotalEntreprises(),
    'totalSalaries' => $csvfile->getCSVnombreTotalSalaries(),
    'NAFtotaux' => $naf->getNAFtotaux($restrictionCodesNAF)
);
$p['page']['stats']['NAF']=array(
    'NAFtotaux' => $naf->getNAFtotaux()
);


$totauxNAF = array();


foreach ($NAFniveaux as $k => $v) {
    
    $nbEntreprises = count($sortedCSVdata[$v['sousclasse']]);
    $nbSalaries = array_sum(array_column($sortedCSVdata[$v['sousclasse']],'Effectif'));

    if (isset($totauxNAF[$v['section']])) $totauxNAF[$v['section']] =  $totauxNAF[$v['section']] + $nbEntreprises;
    else  $totauxNAF[$v['section']] = $nbEntreprises;
    if (isset($totauxNAF[$v['division']]))  $totauxNAF[$v['division']] =  $totauxNAF[$v['division']] + $nbEntreprises;
    else  $totauxNAF[$v['division']] = $nbEntreprises;
    if (isset($totauxNAF[$v['groupe']]))  $totauxNAF[$v['groupe']] =  $totauxNAF[$v['groupe']] + $nbEntreprises;
    else  $totauxNAF[$v['groupe']] = $nbEntreprises;
    if (isset($totauxNAF[$v['classe']]))  $totauxNAF[$v['classe']] =  $totauxNAF[$v['classe']] + $nbEntreprises;
    else  $totauxNAF[$v['classe']] = $nbEntreprises;
    $totauxNAF[$v['sousclasse']] = $nbEntreprises;

    //Sections    
    if (!isset($p['page']['hierarchieNAF']['sections'][$v['section']])) {
        $p['page']['hierarchieNAF']['sections'][$v['section']] = array(
            'code' => $v['section'],
            'label' => $v['label_section'],
            'nbEntreprises' => $totauxNAF[$v['section']],
            'nbSalaries' => $nbSalaries,
            'divisions' =>  array($v['division'])
        );
    } else {
        $p['page']['hierarchieNAF']['sections'][$v['section']]['nbEntreprises'] = $totauxNAF[$v['section']];
        $p['page']['hierarchieNAF']['sections'][$v['section']]['nbSalaries'] += $nbSalaries;
        if(!in_array($v['division'], $p['page']['hierarchieNAF']['sections'][$v['section']]['divisions'])) {
            $p['page']['hierarchieNAF']['sections'][$v['section']]['divisions'][] = $v['division'];
        }
    }

    //Divisions    
    if (!isset($p['page']['hierarchieNAF']['divisions'][$v['division']])) {
        $p['page']['hierarchieNAF']['divisions'][$v['division']] = array(
            'code' => $v['division'],
            'label' => $v['label_division'],
            'nbEntreprises' => $totauxNAF[$v['division']],
            'nbSalaries' => $nbSalaries,
            'section' => $v['section'],
            'groupes' => array($v['groupe'])
        );
    } else {
        $p['page']['hierarchieNAF']['divisions'][$v['division']]['nbEntreprises'] = $totauxNAF[$v['division']];
        $p['page']['hierarchieNAF']['divisions'][$v['division']]['nbSalaries'] += $nbSalaries;
        if(!in_array($v['groupe'], $p['page']['hierarchieNAF']['divisions'][$v['division']]['groupes'])) {
            $p['page']['hierarchieNAF']['divisions'][$v['division']]['groupes'][] = $v['groupe'];
        }
    }

    //Groupes    
    if (!isset($p['page']['hierarchieNAF']['groupes'][$v['groupe']])) {
        $p['page']['hierarchieNAF']['groupes'][$v['groupe']] = array(
            'code' => $v['groupe'],
            'label' => $v['label_groupe'],
            'nbEntreprises' => $totauxNAF[$v['groupe']],
            'nbSalaries' => $nbSalaries,
            'section' => $v['section'],
            'division' => $v['division'],
            'classes' => array($v['classe'])
        );
    } else {
        $p['page']['hierarchieNAF']['groupes'][$v['groupe']]['nbEntreprises'] = $totauxNAF[$v['groupe']];
        $p['page']['hierarchieNAF']['groupes'][$v['groupe']]['nbSalaries'] += $nbSalaries;
        if(!in_array($v['classe'], $p['page']['hierarchieNAF']['groupes'][$v['groupe']]['classes'])) {
            $p['page']['hierarchieNAF']['groupes'][$v['groupe']]['classes'][] = $v['classe'];
        }
    }

    // Classes
    if (!isset($p['page']['hierarchieNAF']['classes'][$v['classe']])) {
        $p['page']['hierarchieNAF']['classes'][$v['classe']] = array(
            'code' => $v['classe'],
            'label' => $v['label_classe'],
            'nbEntreprises' => $totauxNAF[$v['classe']],
            'nbSalaries' => $nbSalaries,
            'section' => $v['section'],
            'division' => $v['division'],
            'groupe' => $v['groupe'],
            'sousclasses' => array($v['sousclasse'])
        );
    } else {
        $p['page']['hierarchieNAF']['classes'][$v['classe']]['nbEntreprises'] = $totauxNAF[$v['classe']];
        $p['page']['hierarchieNAF']['classes'][$v['classe']]['nbSalaries'] += $nbSalaries;
        if(!in_array($v['sousclasse'], $p['page']['hierarchieNAF']['classes'][$v['classe']]['sousclasses'])) {
            $p['page']['hierarchieNAF']['classes'][$v['classe']]['sousclasses'][] = $v['sousclasse'];
        }
    }

    // Sous classes
    if (!isset($p['page']['hierarchieNAF']['sousclasses'][$v['sousclasse']])) {
        $codeameli = str_replace('.', '', $v['sousclasse']);
        $p['page']['hierarchieNAF']['sousclasses'][$v['sousclasse']] = array(
            'code' => $v['sousclasse'],
            'codeameli' => $codeameli,
            'label' => $v['label_sousclasse'],
            'nbEntreprises' => $nbEntreprises,
            'nbSalaries' => $nbSalaries,
            'section' => $v['section'],
            'division' => $v['division'],
            'groupe' => $v['groupe'],
            'classe' => $v['classe'],
            'entreprises' => $sortedCSVdata[$v['sousclasse']],
            'indicateurIF' => $p['page']['ameli']['AT'][$codeameli][$p['config']['dernieresDataAmeliAT']]['indicateurIF'],
            'indicateurTF' => $p['page']['ameli']['AT'][$codeameli][$p['config']['dernieresDataAmeliAT']]['indicateurTF'],
            'indicateurTG' => $p['page']['ameli']['AT'][$codeameli][$p['config']['dernieresDataAmeliAT']]['indicateurTG'],
            'indicateurIG' => $p['page']['ameli']['AT'][$codeameli][$p['config']['dernieresDataAmeliAT']]['indicateurIG']
        );
    }
    
}


// Calcul des incidences pour les MP et ajouts des datas nécessaires à l'onglet stats > MP
$ameliMP->getAmeliMPstatsDatas($p['page']['ameli']['MPstats'], $p['page']['hierarchieNAF']['sousclasses'],$p['page']['ameli']['AT']);

unset($NAFniveaux, $totauxNAF, $sortedCSVdata, $restrictionCodesNAF, $ameliAT, $ameliMP, $ameliT, $csvfile, $naf, $file); 