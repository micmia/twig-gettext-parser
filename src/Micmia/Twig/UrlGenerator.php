<?php
namespace Micmia\Twig;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
/**
 * @author micmia
 *
 */
class UrlGenerator implements UrlGeneratorInterface {
	protected $context;

	public function generate($name, $parameters = array(), $absolute = false) {
	}

	public function getContext() {
		return $this->context;
	}

	public function setContext(RequestContext $context) {
		$this->context = $context;
	}
}
