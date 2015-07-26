<?php

class Person {
    
    /**
     * This is the description of my
     * doc comment
     *
     * @param string $name
     * @param integer $age
     *
     * @author Baylor Rae'
     */
    public static function greet($name, $age) {
        echo "Hello $name, you are $age";
    }
}

$r = new ReflectionMethod('Person', 'greet');
$block = $r->getDocComment();

$doc_block = new DocBlock($block);

echo 'Author: ' . $doc_block->author;

echo 'Params:';
print_r($doc_block->params);