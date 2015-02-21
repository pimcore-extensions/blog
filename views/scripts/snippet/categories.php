<?php if($this->editmode || count($this->list)): ?>

<div class="snippet categories well">
    <h3><?= $this->input('snippet-header') ?></h3>
    <?php if ($this->editmode && !count($this->list)): ?>
        <p><?=$this->translate('No categories')?></p>
    <?php endif; ?>
    <ul>
    <?php foreach($this->list as $category): if($category->getEntryCount()): ?>
        <li>
            <a href="<?=$this->url(array('cat' => $category->getKey()), 'blog-category')?>"
               class="<?= ($this->category == $category->getKey()) ? 'active' : '' ?>"
            ><?=$category->getName()?> (<?=$category->getEntryCount()?>)</a>
        </li>
    <?php endif; endforeach; ?>
    </ul>
</div>

<?php endif; ?>
