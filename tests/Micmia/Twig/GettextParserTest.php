<?php
namespace Micmia\Twig\Test;
use Micmia\Twig\GettextParser;

class GettextParserTest extends \PHPUnit_Framework_TestCase {
	protected $gettextParser;

	function setUp() {
		$this->gettextParser = new GettextParser();
	}

	function testTwigEnvironmentEmpty() {
		$this->assertNotEmpty($this->gettextParser->getTwigEnvironment());
	}

	function testLoadTemplate() {
		$twigTemplates = array(__DIR__ . '/singular.twig',
				__DIR__ . '/plural.twig');
		$cache = $this->gettextParser->loadTemplate($twigTemplates);
		foreach ($cache as $value) {
			printf("\nLoad template: %s", $value);
		}
	}
}
