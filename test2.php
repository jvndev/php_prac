<?php

class C {
    public const CONST = 42;

    public function __construct(private string $a,) { }

    public function getA() { return $this->a;}

    #[Override]
    public function __toString()
    {
        return $this->a;
    }
}

$c = 'CONST';

echo ((new C('42'))->getA());
echo (new C('a'))::{$c};
echo new C('zzz');

echo ord('a');
echo chr(97);