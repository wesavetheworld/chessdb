<?php


namespace AppBundle\Factory;

use AppBundle\Adapter\ChessAdapter;
use AppBundle\Domain\PgnDate;
use AppBundle\Entity\Game;
use AppBundle\Entity\ImportPgn;

class GameFactory
{
    /** @var ChessAdapter */
    private $chess;

    /**
     * GameFactory constructor.
     * @param ChessAdapter $chess
     */
    public function __construct(ChessAdapter $chess)
    {
        $this->chess = $chess;
    }

    public function createFromImportPgn(ImportPgn $importPgn)
    {
        $pgn = $importPgn->getPgnString();
        
        if (!$this->chess->validatePgn($pgn)) {
            throw new \InvalidArgumentException('Given PGN is not valid!');
        }

        $info = $this->chess->parsePgn($pgn);

        return new Game(
            $info['header']['Event'],
            $info['header']['Site'],
            PgnDate::fromString($info['header']['Date']),
            $info['header']['Round'],
            $info['header']['White'],
            $info['header']['Black'],
            $info['header']['Result'],
            $info['moves'],
            $importPgn
        );
    }
}
