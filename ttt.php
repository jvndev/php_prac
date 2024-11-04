<?php

const SIZE = 3;

final class Piece
{
    private static Piece $empty, $cross, $nought;

    private function __construct(private string $symbol, public int $val) {}

    public static function Empty(): Piece
    {
        if (!isset(self::$empty))
            self::$empty = new Piece('.', 0);

        return self::$empty;
    }

    public static function Cross(): Piece
    {
        if (!isset(self::$cross))
            self::$cross = new Piece('X', 1);

        return self::$cross;
    }

    public static function Nought(): Piece
    {
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

    private static function _getBest(...$vals)
    {
        $minMax = 0;

        foreach ($vals as $val)
            $minMax = abs($val) > abs($minMax) ? $val : $minMax;

        return $minMax;
    }

    private function _getState(): int
    {
        $minMax = 0;

        for ($i = 0; $i < SIZE; $i++) {
            $horTotal = 0;
            $verTotal = 0;

            for ($j = 0; $j < SIZE; $j++) {
                $horTotal += $this->squares[$i + $j]->val;
                $verTotal += $this->squares[$i + $j * SIZE]->val;
            }

            $minMax = self::_getBest($minMax, $horTotal, $verTotal);
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

    public function getWinner(): ?Piece
    {
        return match ($this->_getState()) {
            SIZE => Piece::Cross(),
            -1 * SIZE => Piece::Nought(),
            default => null,
        };
    }

    public function move(Piece $piece, int $square): bool
    {
        if ($this->squares[$square] != Piece::Empty())
            return false;

        $this->squares[$square] = $piece;

        return true;
    }

    public function getAvailableMoves(): array {
        return array_keys(array_filter(
            $this->squares,
            fn(Piece $e): bool => $e == Piece::Empty()
        ));
    }

    public function isBoardFull(): bool {
        return !count($this->getAvailableMoves());
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

function printBoard(Board $board): void
{
    sleep(1);
    system('clear');
    echo $board;
}

function changeTurn(Piece $turn) {
    return $turn == Piece::Cross() ? Piece::Nought() : Piece::Cross();
}

$board = new Board();
$turn = Piece::Cross();

do {
    printBoard($board);
    $moves = $board->getAvailableMoves();
    $board->move($turn, $moves[rand(0, count($moves))]);

    $turn = changeTurn($turn);
} while (!($winner = $board->getWinner()) && !$board->isBoardFull());

printBoard($board);

echo ($winner ? "$winner wins" : 'no winner') . "\n";