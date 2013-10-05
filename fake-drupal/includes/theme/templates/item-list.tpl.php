<?php if ($type == 'ol'): ?>
  <ol>
<?php else: ?>
  <ul>
<?php endif ?>
<?php foreach ($items as $item): ?>
  <li><?php print $item; ?></li>
<?php endforeach ?>
<?php if ($type == 'ol'): ?>
  </ol>
<?php else: ?>
  </ul>
<?php endif ?>
