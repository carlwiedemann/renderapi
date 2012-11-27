<table<?php r($attributes); ?>>
  <?php r($caption); ?>
  <?php r($colgroups); ?>
  <thead<?php r($header->attributes); ?>>
  <?php foreach ($header->inner as &$row): ?>
    <tr<?php r($row->attributes); ?>>
    <?php foreach ($row->inner as &$cell): ?>
      <th<?php r($cell->attributes); ?>><?php r($cell->inner); ?></th>
    <?php endforeach ?>
    </tr>
  <?php endforeach ?>
  </thead>
  <tbody<?php r($rows->attributes); ?>>
    <?php foreach ($rows->inner as &$row): ?>
      <tr<?php r($row->attributes); ?>>
        <?php foreach ($row->inner as &$cell): ?>
          <td<?php r($cell->attributes); ?>><?php r($cell->inner); ?></td>
        <?php endforeach ?>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
