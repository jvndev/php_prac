<?php

interface Node {
    public function next(): ?Node;
    public function prev(): ?Node;
    public function setNext(?Node $node): Node;
    public function setPrev(?Node $node): Node;
}

final class DoublyLL {
    private ?Node $head = null;

    private static function createNode($val): Node {
        return new class($val) implements Node {
            private ?Node $next = null;
            private ?Node $prev = null;

            public function __construct(public $val) { }

            public function setNext(?Node $node): Node {
                $this->next = $node;

                return $this;
            }

            public function next(): ?Node {
                return $this->next;
            }

            public function setPrev(?Node $node): Node {
                $this->prev = $node;

                return $this;
            }

            public function prev(): ?Node {
                return $this->prev;
            }
        };
    }

    private function _add(?Node $node, $val): Node {
        if (!$node) return self::createNode($val);

        return $node->setNext(
            $this->_add($node->next(), $val)->setPrev($node)
        );
    }

    public function add($val): DoublyLL {
        $this->head = $this->_add($this->head, $val);

        return $this;
    }

    private function _traverseForward(
        Closure $collector, ?Node $node, $initial): void {
        if (!$node) return;

        $collector($node->val);
        $this->_traverseForward($collector, $node->next(), $initial);
    }

    #[Override]
    public function __toString(): string {
        $str = "";

        $this->_traverseForward(fn($e) => print($e), $this->head, "");

        return $str;
    }
}

echo (new DoublyLL())
    ->add(1)
    ->add(2)
    ->add(3);