<?php
/*
 * This file is part of Twig Gettext Parser.
 * (c) 2013 Micmia
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Micmia\Twig;
use Twig_Loader_Filesystem;

/**
 * Loads template from the filesystem.
 * 
 * @author micmia
 * @see \Twig_Loader_Filesystem
 */
class LoaderFilesystem extends Twig_Loader_Filesystem {
	/**
	 * Find a template by its name or full path
	 * @see Twig_Loader_Filesystem::findTemplate()
	 */
	protected function findTemplate($name) {
		$name = (string) $name;

		// normalize name
		$name = preg_replace('#/{2,}#', '/', strtr($name, '\\', '/'));

		if (isset($this->cache[$name])) {
			return $this->cache[$name];
		}

		$this->validateName($name);

		$namespace = self::MAIN_NAMESPACE;
		if (isset($name[0]) && '@' == $name[0]) {
			if (false === $pos = strpos($name, '/')) {
				throw new Twig_Error_Loader(
						sprintf(
								'Malformed namespaced template name "%s" (expecting "@namespace/template_name").',
								$name));
			}

			$namespace = substr($name, 1, $pos - 1);

			$name = substr($name, $pos + 1);
		}

		if (!isset($this->paths[$namespace])) {
			throw new Twig_Error_Loader(
					sprintf(
							'There are no registered paths for namespace "%s".',
							$namespace));
		}

		foreach ($this->paths[$namespace] as $path) {
			if (is_file($path . '/' . $name)) {
				return $this->cache[$name] = $path . '/' . $name;
			} elseif (is_file($name)) {
				return $this->cache[$name] = $name; // find a template with its full path
			}
		}

		return __DIR__ . '/EmptyTemplate.twig';

		throw new \Twig_Error_Loader(
				sprintf('Unable to find template "%s" (looked into: %s).',
						$name, implode(', ', $this->paths[$namespace])));
	}
}
