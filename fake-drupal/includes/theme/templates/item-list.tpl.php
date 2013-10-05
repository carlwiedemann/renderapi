<?php if ($type == 'ol'): ?>
  <ol>
<?php else: ?>
  <ul>
<?php endif ?>
<?php foreach ($items as $item): ?>
  <li><?php print render($item); ?></li>
<?php endforeach ?>
<?php if ($type == 'ol'): ?>
  </ol>
<?php else: ?>
  </ul>
<?php endif ?>
