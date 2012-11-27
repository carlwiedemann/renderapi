<?php 
// Feasibly, you **could** make a template for every #type tag in a base
// renderable, but that is a silly thing to do, just as you could make an
// entire bowl of rice cooking one grain at a time. So: Don't do this :)
?>
<tag<?php r($attributes) ?>><?php r($inner) ?></tag>