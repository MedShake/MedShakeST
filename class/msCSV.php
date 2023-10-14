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
 * Gestion du CSV 
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

class msCSV
{

    private $_CSVfile;
    private $_CSVnombreTotalEntreprises;
    private $_CSVnombreTotalSalaries;
    private $_CSVarray;

    public function setCSFfile($CSVfile) {
        if(file_exists($CSVfile)) {
            $this->_CSVfile = $CSVfile;
            return true;
        } else {
            return false;
        }

    }

    public function getNombreTotalEntreprises() {
        return $this->_CSVnombreTotalEntreprises;
    }

    public function getCSVnombreTotalSalaries() {
        return $this->_CSVnombreTotalSalaries;
    }


    public function readCSV()
    {
        if (!isset($this->_CSVfile) or !file_exists($this->_CSVfile)) {
            throw new Exception('Le fichier CSV n\'est pas disponible');
        }

        // BOM as a string for comparison.
        $bom = "\xef\xbb\xbf";

        // Read file from beginning.
        //$fp = fopen("/home/www/file.csv", 'r');
        $fp = fopen($this->_CSVfile, 'r');

        // Progress file pointer and get first 3 characters to compare to the BOM string.
        if (fgets($fp, 4) !== $bom) {
            // BOM not found - rewind pointer to start of file.
            rewind($fp);
        }

        // delimiteur   
        $delimiters = array(
            ';' => 0,
            ',' => 0,
            "\t" => 0,
            "|" => 0
        ); 
        $firstLine = fgets($fp);
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }
        $delimiter = array_search(max($delimiters), $delimiters);
        rewind($fp);

        // Read CSV into an array.
        $lines = array();
        while (!feof($fp) && ($line = fgetcsv($fp, 0, $delimiter, '"')) !== false) {
            
            if(strlen($line[1]) < 5 or strlen($line[1]) > 6) {
                $error = "Le code ".$line[1]." en ligne ".count($lines)." n'est pas un code NAF valide";
            }
            if(strlen($line[1]) == 5) {
                $line[1] = $line[1][0].$line[1][1].'.'.$line[1][2].$line[1][3].$line[1][4];
            }
            if(strlen($line[1]) == 6 and strpos($line[1], '.') !=2 ) {               
                $error = "Le code ".$line[1]." en ligne ".count($lines)." n'est pas un code NAF valide";
            }
            if(!preg_match("#[0-9]{2}\.[0-9]{2}[A-Z]{1}#", $line[1])) {
                $error = "Le code ".$line[1]." en ligne ".count($lines)." n'est pas un code NAF valide";
            }
            if(!empty($line[2]) and !is_numeric($line[2])) {
                $error = "La chaine ".$line[2]." en ligne ".count($lines)." n'est pas un nombre de salariés valide";
            }
            
            
            if (is_array($line)) $lines[] = $line;
        }
        
        unlink($this->_CSVfile);
        if(isset($error)) {
            return $error;
        } else {
            return $this->_CSVarray = $lines;
        }
    }

    public function getSortedCSVdata()
    {
        if (!isset($this->_CSVarray)) {
            throw new Exception('Les datas du CSV ne sont pas disponibles');
        }

        $sortedData=array();
        $this->_CSVnombreTotalEntreprises = 0;
        $this->_CSVnombreTotalSalaries = 0;

        foreach($this->_CSVarray as $k=>$entreprise) {
            
            $this->_CSVnombreTotalEntreprises ++;
            if(is_numeric($entreprise[2])) $this->_CSVnombreTotalSalaries = $this->_CSVnombreTotalSalaries + $entreprise[2];

            $sortedData[$entreprise[1]][] =  array(
                'NAF'=> $entreprise[1],
                'Effectif' => $entreprise[2],
                'entrepriseID' => $entreprise[0],
                'entrepriseNom' => trim($entreprise[3])
            );
        }

        return $sortedData;
    }
}
