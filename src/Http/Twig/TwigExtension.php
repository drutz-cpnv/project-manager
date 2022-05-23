<?php

namespace App\Http\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
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
            new TwigFunction('days', [$this, 'days'], ['is_safe' => ['html']]),
            new TwigFunction('menu', [$this, 'menu'], ['is_safe' => ['html'], 'needs_context' => true]),
            new TwigFunction('tabActive', [$this, 'tabActive'], ['is_safe' => ['html'], 'needs_context' => true]),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('strpad', [$this, 'strpad']),
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

    public function menu(array $context, string $title, string $pathName, string $icon, string $menuName, string $atlernativeMenuName = null): string
    {
        $url = $this->urlGenerator->generate($pathName);
        $currentPage = "";
        if ((($context['menu'] ?? null) === $menuName) || (($context['menu'] ?? null) === $atlernativeMenuName)) {
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

    public function strpad($number, $pad_length, $pad_string) {
        return str_pad($number, $pad_length, $pad_string, STR_PAD_LEFT);
    }

    public function tabActive(array $context, string $name): string
    {
        if (($context['tab'] ?? null) === $name) {
            return ' aria-current="tab"';
        }
        return '';
    }

    public function days()
    {
        $today = new \DateTime();
        $start = \DateTime::createFromFormat("Y-m-d H:i", "2021-08-01 08:00");
        $end = \DateTime::createFromFormat("Y-m-d H:i", "2022-08-01 17:00");
        $oneDayInterval = new \DateInterval("P1D");

        $out = new ArrayCollection(
            [
                'month' => new ArrayCollection([]),
                'days' => new ArrayCollection([])
            ]
        );

        $out['days'][] = $start;

        $last = $out['days'][0];
        for ($i = 1; $i <= $end->diff($start)->days; $i++) {
            $date = clone $last->add($oneDayInterval);
            $last = $date;
            $out['days'][] = $date;


            if(!$out['month']->containsKey($date->format("M"))) {
                $out['month'][$date->format("M")] = 1;
            }
            else{
                $out['month'][$date->format("M")] += 1;
            }
        }


        return $out;

    }

    /*public function days()
    {
        $today = new \DateTime();
        $start = \DateTime::createFromFormat("Y-m-d H:i", "2021-08-01 08:00");
        $end = \DateTime::createFromFormat("Y-m-d H:i", "2022-07-01 17:00");
        $oneDayInterval = new \DateInterval("P1D");

        $dates = [];

        $dates[$start->format("M")] = [$start];

        $last = $dates[$start->format("M")][0];
        for ($i = 1; $i <= $end->diff($start)->days; $i++) {
            $date = clone $last->add($oneDayInterval);
            $last = $date;

            $dates[$date->format("M")][] = $date;
        }

        $out = new ArrayCollection();

        foreach ($dates as $m => $days) {
            $out[$m] = new ArrayCollection($days);
        }


        return $out;

    }*/

}
