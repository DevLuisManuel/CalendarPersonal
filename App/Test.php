<?php

class Test
{
    private int $x = 2;

    public function function1(int $x): int
    {
        $x = $x + $this->x; //  Test 3 = $x - Test 3 = $this->x
        return $x + 5; // Test 4 = $x - Test 4 = $this->x
    }

    public function main(): array
    {
        $x = $this->x + 7; // Test 1 = $x - Test 1 = $this->x
        $this->x = $this->function1($x);
        $x += $this->x + $x; // Test 2 = $x - Test 2 = $this->x
        return [
            $x,
            $this->x,
        ];
    }

    //Aaqui va la funcion de la prueba
    public function encontrarhola(string $frase)
    {
        $partir = explode(" ", str_replace(",", "", strtolower(strtoupper($frase))));
        $cont = 0;
        foreach ($partir as $String) {
            if ($String == "hola") {
                $cont += 1;
            }
        }
        return $cont;
    }

}

// var_dump((new Test())->main()); //Que devuelve $x y devuelve $this->x

// Crear una funcion la cual teniendo una frase de X cantidad de palabras, decirme cuantas veces se repite la palabra
// en la frase: Ej. Hola como estan hOLa, todo va muy holA bien, pero un hoLA, no es suficiente para saludar (hola) //

var_dump((new Test())->encontrarhola("Hola como estan hOLa, todo va muy holA bien, pero un hoLA, no es suficiente para saludar")); //Que devuelve $x y devuelve $this->x
