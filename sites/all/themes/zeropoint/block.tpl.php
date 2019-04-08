<?php
//echo "<pre>";
//print_r($block);
//echo "</pre>";
//exit();
?>
<div class="block-wrapper <?php print $block_zebra .' block_'. $block_id; ?>">
  <div id="block-<?php print $block->module .'-'. $block->delta; ?>" class="block block-<?php print $block->module ?> <?php if ($themed_block): ?>themed-block<?php endif; ?>">
    <?php if ($block->subject): ?>
      <?php if ($themed_block): ?>
    <div class="block-icon"></div>
      <?php endif; ?>
    <h3 class="title block-title"><?php print $block->subject ?></h3>
      <?php endif; ?>
    <div class="content"><?php print $block->content ?></div>
  </div>
</div>
