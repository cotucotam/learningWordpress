<?php
    function myFirstFunction(){
        echo "<p> Hello My Name Tam </p>";
    }
    function greet($name){
        echo "<p> $name is great</p>";
    }
    myFirstFunction();
    myFirstFunction();
    myFirstFunction();
    greet('tam');
    greet('co','tam');
?>
<h1><?php greet('tam'); ?></h1>
<h2><?php bloginfo('name')?></h2>
<p><?php bloginfo('description')?></p>