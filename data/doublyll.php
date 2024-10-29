<?php

interface Node
{
    public function next(): ?Node;
    public function prev(): ?Node;
    public function setNext(?Node $node): Node;
    public function setPrev(?Node $node): Node;
}

enum Direction
{
    case FORWARD;
    case BACKWARD;
}

final class DoublyLL
{
    private ?Node $head = null;
    private ?Node $tail = null;

    private static function createNode($val): Node
    {
        return new class($val) implements Node {
            private ?Node $next = null;
            private ?Node $prev = null;

            public function __construct(public $val) {}

            public function setNext(?Node $node): Node
            {
                $this->next = $node;

                return $this;
            }

            public function next(): ?Node
            {
                return $this->next;
            }

            public function setPrev(?Node $node): Node
            {
                $this->prev = $node;

                return $this;
            }

            public function prev(): ?Node
            {
                return $this->prev;
            }
        };
    }

    private function _add(?Node $node, $val): Node
    {
        if (!$node) return $this->tail = self::createNode($val);

        return $node->setNext(
            $this->_add($node->next(), $val)->setPrev($node)
        );
    }

    public function add($val): DoublyLL
    {
        $this->head = $this->_add($this->head, $val);

        return $this;
    }

    public function traverseForward(Closure $collector, $initial)
    {
        return $this->_traverse(Direction::FORWARD, $collector, $this->head, $initial);
    }

    public function traverseBackward(Closure $collector, $initial)
    {
        return $this->_traverse(Direction::BACKWARD, $collector, $this->tail, $initial);
    }

    private function _traverse(
        Direction $direction,
        Closure $collector,
        ?Node $node,
        $initial
    ) {
        if (!$node) return $initial;

        return $collector(
            $node->val,
            $this->_traverse(
                $direction,
                $collector,
                $direction == Direction::FORWARD ? $node->next() : $node->prev(),
                $initial
            )
        );
    }

    #[Override]
    public function __toString(): string
    {
        $initial = "";
        $collector = fn($p, $c) => "$p, $c";

        return (string)$this->traverseForward($collector, $initial)
            . "\n"
            . (string)$this->traverseBackward($collector, $initial);
    }
}

echo (new DoublyLL())
    ->add(1)
    ->add(2)
    ->add(3);

$ll = new DoublyLL();
for ($i = 1; $i < 20; $i += 2)
    $ll->add($i);
$collector = fn(int $p, array $c) => [$p, ...$c];
$strCollector = fn(string $p, int $c) => "$p, $c";

printf(
    "\n%s\n%s\n",
    array_reduce($ll->traverseForward($collector, []), $strCollector, ""),
    array_reduce($ll->traverseBackward($collector, []), $strCollector, ""),
);