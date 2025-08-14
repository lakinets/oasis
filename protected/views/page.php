<?php
/**
 * @var Controller $this
 * @var Pages $model
 */

$this->pageTitle = $model->title;
?>

<h2 class="title"><?php echo e($this->pageHeader) ?></h2>
<div class="entry">
	<div class="scroll-pane">
		<?php echo $model->text ?>
	</div>
</div>