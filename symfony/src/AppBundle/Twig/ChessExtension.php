<?php

namespace AppBundle\Twig;

use Symfony\Component\Translation\TranslatorInterface;

class ChessExtension extends \Twig_Extension
{
    const PGN_CLASS = 'pgn';
    const PIECE_NAMES = 'pgn.piece_names';

    /** @var  TranslatorInterface */
    private $translator;

    /**
     * ChessExtension constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('pgn', [$this, 'renderPgn'], ['is_safe' => ['html']]),
        ];
    }

    public function renderPgn($pgn, $options = [])
    {
        $attributes = '';
        foreach ($this->parseOptions($options) as $key => $value) {
            $attributes .= sprintf("%s='%s' ", $key, $value);
        }

        return sprintf('<div %s>%s</div>', $attributes, $pgn);
    }

    private function parseOptions($options = [])
    {
        $defaultOptions = [
            'data-show-buttons' => "true",
            'data-show-moves' => "false",
            'data-show-header' => "true",
            'data-label-next' => "&gt;&gt;",
            'data-label-back' => "&lt;&lt;",
            'data-label-reset' => "start",
            'data-label-turn' => "flip",
            'data-piece-names' => $this->translator->trans(self::PIECE_NAMES),
            'class' => 'pgn',
        ];

        return array_merge($defaultOptions, $options);
    }

    public function getName()
    {
        return 'app_chess';
    }
}
