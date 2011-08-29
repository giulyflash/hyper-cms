<?php
var_dump($_REQUEST);
echo '
<form action="/tmp/test_multiple_input.php">


<p>

<select size="5" multiple="" name="toppings[]">
<option value="mushrooms">mushrooms
</option><option value="greenpeppers">green peppers
</option><option value="onions">onions
</option><option value="tomatoes">tomatoes
</option><option value="olives">olives
</option></select>

</p><p>


<input type="SUBMIT" value="submit">
</p></form>
';
?>