<table<?php r($attributes); ?>>
  <?php if ($caption->bool()): ?>
    <caption<?php r($caption->attributes) ?>><?php r($caption->inner) ?></caption>
  <?php endif ?>
  <?php if ($colgroups->bool()): ?>
    <?php foreach ($colgroups as $colgroup): ?>
      <?php if ($colgroup->inner->bool()): ?>
        <colgroup<?php r($colgroup->attributes) ?>>
          <?php foreach ($colgroup->inner as $col): ?>
            <col<?php r($col->attributes) ?>><?php r($col->inner) ?></col>
          <?php endforeach ?>
        </colgroup>
      <?php else: ?>
        <colgroup<?php r($colgroup->attributes) ?>/>
      <?php endif ?>
    <?php endforeach ?>
  <?php endif ?>
  <?php if ($header->bool()): ?>
    <thead<?php r($header->attributes); ?>>
    <?php foreach ($header->inner as $row): ?>
      <tr<?php r($row->attributes); ?>>
      <?php foreach ($row->inner as $cell): ?>
        <th<?php r($cell->attributes); ?>><?php r($cell->inner); ?></th>
      <?php endforeach ?>
      </tr>
    <?php endforeach ?>
    </thead>
  <?php endif ?>
  <?php if ($rows->bool() || TRUE): ?>
    <tbody<?php r($rows->attributes); ?>>
      <?php foreach ($rows->inner as $row): ?>
        <tr<?php r($row->attributes); ?>>
          <?php foreach ($row->inner as $cell): ?>
            <td<?php r($cell->attributes); ?>><?php r($cell->inner); ?></td>
          <?php endforeach ?>
        </tr>
      <?php endforeach ?>
    </tbody>
  <?php endif ?>
</table>
