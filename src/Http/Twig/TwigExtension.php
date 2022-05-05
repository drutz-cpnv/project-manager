<?php

namespace App\Http\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

class TwigExtension extends AbstractExtension
{

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('material', [$this, 'material'], ['is_safe' => ['html']]),
            new TwigFunction('feather', [$this, 'feather'], ['is_safe' => ['html']]),
            new TwigFunction('menu', [$this, 'menu'], ['is_safe' => ['html'], 'needs_context' => true]),
            new TwigFunction('tabActive', [$this, 'tabActive'], ['is_safe' => ['html'], 'needs_context' => true]),
        ];
    }



    public function material(string $name, bool $rounded = true): string
    {
        $class = 'icon material-icons';

        if ($rounded) {
            $class .= "-round";
        }

        return <<<HTML
<span class="$class">
$name
</span>
HTML;

    }

    public function feather(string $name): string
    {
        return <<<HTML
<i data-feather="$name"></i>
HTML;

    }

    public function menu(array $context, string $title, string $pathName, string $icon, string $menuName): string
    {
        $url = $this->urlGenerator->generate($pathName);
        $currentPage = "";
        if (($context['menu'] ?? null) === $menuName) {
            $currentPage = ' aria-current="page"';
        }
        return <<<HTML
<li class="nav-item">
    <a class="nav-link" href="$url"$currentPage>
        <span data-feather="$icon"></span>
        $title
    </a>
</li>
HTML;
    }

    public function tabActive(array $context, string $name): string
    {
        if (($context['tab'] ?? null) === $name) {
            return ' aria-current="tab"';
        }
        return '';
    }

}
