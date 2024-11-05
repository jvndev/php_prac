<?php

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
    private readonly int $boardSize;
    private array $squares;

    public function __construct(int $boardSize)
    {
        if ($boardSize < 2)
            throw new InvalidArgumentException("Invalid boardsize");

        $this->boardSize = $boardSize;
        $this->squares = array_map(
            fn($_) => Piece::Empty(),
            range(0, $boardSize * $boardSize - 1)
        );
    }

    private static function _getBest(...$vals)
    {
        $minMax = 0;

        foreach ($vals as $val)
            $minMax = abs($val) > abs($minMax) ? $val : $minMax;

        return $minMax;
    }

    public static function switchPiece(Piece $piece): Piece
    {
        return $piece == Piece::Cross()
            ? Piece::Nought()
            : Piece::Cross();
    }

    private function _getState(): int
    {
        $size = $this->boardSize;
        $minMax = 0;

        for ($i = 0; $i < $size; $i++) {
            $horTotal = 0;
            $verTotal = 0;

            for ($j = 0; $j < $size; $j++) {
                $horTotal += $this->squares[$i * $size + $j]->val;
                $verTotal += $this->squares[$i + $j * $size]->val;
            }

            $minMax = self::_getBest($minMax, $horTotal, $verTotal);
        }

        $diagTotal = 0;
        for ($i = 0; $i < $size * $size; $i += $size + 1)
            $diagTotal += $this->squares[$i]->val;
        $minMax = self::_getBest($minMax, $diagTotal);

        $diagTotal = 0;
        for ($i = $size - 1; $i < $size * $size - 1; $i += $size - 1)
            $diagTotal += $this->squares[$i]->val;
        $minMax = self::_getBest($minMax, $diagTotal);

        return $minMax;
    }

    public function getWinner(): ?Piece
    {
        return match ($this->_getState()) {
            $this->boardSize => Piece::Cross(),
            -1 * $this->boardSize => Piece::Nought(),
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

    private function _getMinMax(Piece $piece, int $move): int {
        return rand(-1 * $this->boardSize, $this->boardSize);
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
        $size = $this->boardSize;

        for ($i = 1; $i <= $size * $size; $i++)
            $str .= $this->squares[$i - 1] . ($i % $size == 0 ? PHP_EOL : '');

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
        $turn = Board::switchPiece($turn);
    }

    public static function start(): void
    {
        $board = new Board(5);
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
