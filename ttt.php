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
            range(0, SIZE * SIZE - 1)
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
                $horTotal += $this->squares[$i * SIZE + $j]->val;
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

    public function getAvailableMoves(): array
    {
        return array_keys(array_filter(
            $this->squares,
            fn(Piece $e): bool => $e == Piece::Empty()
        ));
    }

    private function _getMinMax(Piece $piece, int $move): int
    {
        return rand(-1 * SIZE, SIZE);
    }

    public function getBestMove(Piece $piece): int
    {
        $compare = fn(Piece $piece, int $a, int $b): bool
            => $piece == Piece::Cross() ? $a >= $b : $a <= $b;
        $minMax = $piece == Piece::Cross() ? PHP_INT_MIN : PHP_INT_MAX;

        foreach ($this->getAvailableMoves() as $move) {
            $_minMax = $this->_getMinMax($piece, $move);

            if ($compare($piece, $_minMax, $minMax)) {
                $minMax = $_minMax;
                $bestMove = $move;
            }
        }

        assert(isset($bestMove), "$piece ($minMax $_minMax)");

        return $bestMove;
    }

    public function isBoardFull(): bool
    {
        return !count($this->getAvailableMoves());
    }

    #[Override]
    public function __toString(): string
    {
        $str = '';

        for ($i = 1; $i <= SIZE * SIZE; $i++)
            $str .= $this->squares[$i - 1] . ($i % SIZE == 0 ? PHP_EOL : '');

        return $str;
    }
}

final class Game
{
    private function __construct() {}

    private static function printBoard(Board $board): void
    {
        usleep(1000000 * 0.5);
        system('clear');
        echo $board;
    }

    private static function changeTurn(Piece &$turn): void
    {
        $turn = $turn == Piece::Cross() ? Piece::Nought() : Piece::Cross();
    }

    public static function start(): void
    {
        $board = new Board();
        $turn = Piece::Cross();

        do {
            self::printBoard($board);

            $board->move($turn, $board->getBestMove($turn));

            self::changeTurn($turn);
        } while (!($winner = $board->getWinner()) && !$board->isBoardFull());

        self::printBoard($board);

        echo ($winner ? "$winner wins" : 'no winner') . PHP_EOL;
    }
}

do {
    Game::start();
    sleep(1);
} while (true);
