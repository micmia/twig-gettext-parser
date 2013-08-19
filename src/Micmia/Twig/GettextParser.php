<?php
/*
 * This file is part of Twig Gettext Parser.
 * (c) 2013 Micmia
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Micmia\Twig;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\Translator;
use Symfony\Bridge\Twig\Extension\CodeExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\Extension\SecurityExtension;
use Symfony\Bridge\Twig\Extension\YamlExtension;
use Symfony\Bundle\TwigBundle\Extension\AssetsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
/**
 * GettextParser extracts the translation strings from twig templates for Poedit.
 *
 * @author micmia
 */
class GettextParser {
	/**
	 * The cache directory.
	 *
	 * @var string
	 */
	const CACHE_PATH = '/tmp/cache';

	/**
	 * The full path of xgettext (shell) command.
	 *
	 * @var string
	 */
	const XGETTEXT_PATH = '/usr/local/bin/xgettext';

	/**
	 * Stores the Twig configuration.
	 *
	 * @var \Twig_Environment
	 */
	private $twig;

	function __construct() {
		$templateDir = '/';
		$loader = new LoaderFilesystem($templateDir);

		// configure twig
		$this->twig = new \Twig_Environment($loader,
				array('cache' => self::CACHE_PATH . '/' . uniqid(),
						'auto_reload' => true, 'strict_variables' => false));
		$this->twig->addExtension(new \Twig_Extensions_Extension_I18n());
		// $this->twig->addExtension(new TranslationExtension(new Translator(null)));
		$this->twig->addExtension(new CodeExtension(null, null, null));
		$this->twig
				->addExtension(
						new FormExtension(
								new TwigRenderer(new TwigRendererEngine())));
		$this->twig->addExtension(new RoutingExtension(new UrlGenerator()));
		$this->twig->addExtension(new SecurityExtension());
		$this->twig->addExtension(new YamlExtension());
		$this->twig->addExtension(new AssetsExtension(new ContainerBuilder()));
		// add extensions to let twig run the right way without any exception
		// append other extensions when unknown function errors occur
	}

	/**
	 * Get twig environment.
	 * 
	 * @return \Twig_Environment
	 */
	function getTwigEnvironment() {
		return $this->twig;
	}

	/**
	 * Read command line arguments.
	 * 
	 * @param array $xgettextArgs
	 * @param array $twigTemplates
	 */
	function readCommandLineArgs(&$xgettextArgs, &$twigTemplates) {
		$argv = $_SERVER['argv'];
		$fileArgTagBegins = false;
		for ($i = 1; $i < count($argv); $i++) {
			$arg = $argv[$i];
			if ($arg == '--files') {
				$fileArgTagBegins = true;
			} else if ($fileArgTagBegins) {
				$twigTemplates[] = trim($arg, '"');
			} else {
				$xgettextArgs[] = $arg;
			}
		}
	}

	/**
	 * Load Twig templates.
	 * 
	 * @param array $twigTemplates
	 * @return array
	 */
	function loadTemplate(array $twigTemplates) {
		$phpTemplates = array();
		foreach ($twigTemplates as $twigTemplate) {
			// force compilation
			if (is_file($twigTemplate)) {
				$this->twig->loadTemplate($twigTemplate);
				$phpTemplates[] = $this->twig->getCacheFilename($twigTemplate);
			}
		}
		return $phpTemplates;
	}

	/**
	 * Handle command line arguments and write output to specified file.
	 */
	function handle() {
		$xgettextArgs = array();
		$twigTemplates = array();
		$this->readCommandLineArgs($xgettextArgs, $twigTemplates);
		$phpTemplates = $this->loadTemplate($twigTemplates);
		$command = implode(' ', $xgettextArgs);
		$phpTemplates = implode(' ', $phpTemplates);
		$command = self::XGETTEXT_PATH . " $command $phpTemplates";
		system($command);
	}

	function __destruct() {
		$filesystem = new Filesystem();
		// 		$filesystem->remove($this->twig->getCache());
	}
}
