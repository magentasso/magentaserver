<?php
namespace MagentaServer\Helpers;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class TwigExtension extends AbstractExtension {
    public function getName(): string {
        return 'magentaserver';
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array {
        return [
            new TwigFunction('L', function($key, ...$args) {
				return \L($key, $args);
			}),
        ];
    }
}
