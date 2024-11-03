<?php

const SIZE = 3;

final class Piece {
    private static Piece $empty, $cross, $nought;

    private function __construct(private string $symbol, public int $val) { }

    public static function Empty(): Piece {
        if (!isset(self::$empty))
            self::$empty = new Piece('.', 0);
    
        return self::$empty;
    }

    public static function Cross(): Piece {
        if (!isset(self::$cross))
            self::$cross = new Piece('X', 1);
    
        return self::$cross;
    }

    public static function Nought(): Piece {
        if (!isset(self::$nought))
            self::$nought = new Piece('O', -1);
    
        return self::$nought;
    }

    #[Override]
    public function __toString()
    {
        return $this->symbol;
    }
}

class Board
{
    private array $squares;

    public function __construct()
    {
        $this->squares = array_map(
            fn($_) => Piece::Empty(),
            range(0, SIZE * SIZE)
        );
    }

    private static function _getBest(...$vals) {
        $minMax = 0;

        foreach ($vals as $val)
            $minMax = abs($val) > abs($minMax) ? $val : $minMax;

        return $minMax;
    }

    public function getWinner(): ?Piece {
        return match($this->_getState()) {
            SIZE => Piece::Cross(),
            -1 * SIZE => Piece::Nought(),
            default => null,
        };
    }

    private function _getState(): int {
        $minMax = 0;

        for ($i = 0; $i < SIZE; $i++) {
            $horTotal = 0;
            $verTotal = 0;

            for ($j = 0; $j < SIZE; $j++) {
                $horTotal += $this->squares[$i + $j]->val;
                $verTotal += $this->squares[$i + $j * SIZE]->val;
            }

            $minMax = self::_getBest($minMax, $horTotal, $verTotal);
            $horTotal = 0;
            $verTotal = 0;
        }

        $diagTotal = 0;
        for ($i = 0; $i < SIZE * SIZE; $i += SIZE + 1)
            $diagTotal += $this->squares[$i]->val;
        $minMax = self::_getBest($minMax, $diagTotal);

        $diagTotal = 0;
        for ($i = SIZE - 1; $i < SIZE * SIZE - 1; $i += SIZE - 1) 
            $diagTotal += $this->squares[$i]->val;
        $minMax = self::_getBest($minMax, $diagTotal);

        return $minMax;
    }

    public function move(Piece $piece, int $square): bool {
        if ($this->squares[$square] != Piece::Empty())
            return false;

        $this->squares[$square] = $piece;
        
        return true;
    }

    #[Override]
    public function __toString(): string
    {
        $str = '';

        for ($i = 1; $i <= SIZE * SIZE; $i++)
            $str .= $this->squares[$i - 1] . ($i % SIZE == 0 ? "\n" : '');

        return $str;
    }
}

$board = new Board();
$board->move(Piece::Cross(), 2);
$board->move(Piece::Nought(), 3);
$board->move(Piece::Nought(), 8);
$board->move(Piece::Cross(), 4);
$board->move(Piece::Nought(), 0);
$board->move(Piece::Nought(), 6);

$winner = $board->getWinner();
echo $board;
echo ($winner ? "$winner wins": 'no winner') . "\n";