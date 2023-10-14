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
 * Système. 
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */
class msSystem
{


    /**
     * Routes
     * @return array                  match routeur
     */
    public static function getRoutes()
    {
        global $p, $routes;

        $router = new AltoRouter();
        $file = $p['homepath'] . 'config/routes.yml';
        if (is_file($file)) {
            $routes = yaml_parse_file($file);
            $router->addRoutes($routes);
        }

        $router->setBasePath($p['config']['urlHostSuffixe']);
        return $router->match();
    }
}
