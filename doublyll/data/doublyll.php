<?php

interface INode
{
    public function next(): ?INode;
    public function prev(): ?INode;
    public function setNext(?INode $node): INode;
    public function setPrev(?INode $node): INode;
}

class Node implements INode
{
    private ?INode $next = null;
    private ?INode $prev = null;

    public function __construct(public $val) {}

    public function setNext(?INode $node): INode
    {
        $this->next = $node;

        return $this;
    }

    public function next(): ?INode
    {
        return $this->next;
    }

    public function setPrev(?INode $node): INode
    {
        $this->prev = $node;

        return $this;
    }

    public function prev(): ?INode
    {
        return $this->prev;
    }
}

enum Direction
{
    case FORWARD;
    case BACKWARD;
}

final class DoublyLL
{
    private ?INode $head = null;
    private ?INode $tail = null;

    private function __construct() {}

    public static function make(array $source = null)
    {
        $dll = new DoublyLL();
        if (!$source) return $dll;

        foreach ($source as $e)
            $dll->add($e);

        return $dll;
    }

    private static function createNode($val): INode
    {
        return new Node($val);
    }

    private function _add(?INode $node, $val): INode
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
        ?INode $node,
        $initial
    ) {
        if (!$node) return $initial;

        return $collector(
            $this->_traverse(
                $direction,
                $collector,
                $direction == Direction::FORWARD ? $node->next() : $node->prev(),
                $initial
            ),
            $node->val
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
