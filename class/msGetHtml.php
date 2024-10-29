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
 * Pilotage du moteur de template. Class issue de MedShakeEHR, remaniée pour l'usage
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 * @contrib fr33z00 <https://github.com/fr33z00>
 */
class msGetHtml
{
   
	/**
	 * template à utiliser
	 * @var string
	 */
	private $_template;
	/**
	 * extension du nom de fichier de template à utiliser
	 * @var string
	 */
	private $_templateFileExt = '.html.twig';
	/**
	 * répertoire(s) ou le template doit être trouvé
	 * @var array
	 */
	private $_templatesDirectories;


	/**
	 * Définir le template
	 * @param string $_template
	 *
	 * @return static
	 */
	public function set_template($_template)
	{
		$_template = str_ireplace($this->_templateFileExt, '', $_template);
		$this->_template = $_template;
		return $this;
	}

	/**
	 * Générer le HTML et le retourner
	 * @return string HTML générer par le moteur de template
	 */
	public function genererHtml()
	{
		global $p;
        
		if (!isset($this->_template)) {
			throw new Exception('Template is not defined');
		}

		if (!isset($this->_templatesDirectories)) {
			$this->_construcDefaultTemplatesDirectories();
		}

		// les variables d'environnement twig
		if (isset($p['config']['twigEnvironnementCache'])) {
			$twigEnvironment['cache'] = $p['config']['twigEnvironnementCache'];
		} else {
			$twigEnvironment['cache'] = false;
		}
		if (isset($p['config']['twigEnvironnementAutoescape'])) {
			$twigEnvironment['autoescape'] = $p['config']['twigEnvironnementAutoescape'];
		} else {
			$twigEnvironment['autoescape'] = false;
		}

		if (isset($p['config']['twigDebug'])) {
			$twigEnvironment['debug'] = $p['config']['twigDebug'];
		} else {
			$twigEnvironment['debug'] = false;
		}

		// Lancer Twig
		$loader = new \Twig\Loader\FilesystemLoader($this->_templatesDirectories);
		$twig = new \Twig\Environment($loader, $twigEnvironment);
        $twig->addExtension(new Twig\Extra\Intl\IntlExtension());
		$twig->getExtension(\Twig\Extension\CoreExtension::class)->setDateFormat('d/m/Y', '%d days');
		$twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Paris');

		if ($twigEnvironment['debug'])
			$twig->addExtension(new \Twig\Extension\DebugExtension());

		// filtre pour la verification de l'existence d'un fichier (utile dans la surcharge du menu principal)
		$fileExist = new \Twig\TwigFilter('file_exist', function ($string) {
			foreach ($this->_templatesDirectories as $template) {
				if (file_exists($template.$string))
					return true;
			}
			return false;
		});

		$twig->addFilter($fileExist);

		return $twig->render($this->_template . $this->_templateFileExt, $p);
	}

	/**
	 * Construire les répertoires par défaut à interroger pour obtenir le template
	 * @return array Tableau des répertoires
	 */
	private function _construcDefaultTemplatesDirectories()
	{
		global $p;

		$this->_templatesDirectories = [];

		//templates base
		$baseFolder = $p['config']['templatesFolder'];
		if (is_dir($baseFolder)) {
			$this->_templatesDirectories[] = $baseFolder;
			$this->_templatesDirectories = array_merge($this->_templatesDirectories, msTools::getAllSubDirectories($baseFolder, '/'));
		}
		return $this->_templatesDirectories;
	}
}
