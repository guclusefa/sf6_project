<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CustomExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('defaultImage', [$this, 'defaultImage']),
        ];
    }

    public function defaultImage(string $path) : string
    {
        if (file_exists($path)) {
            return $path;
        } else {
            return 'default.jpg';
        }
    }
}
