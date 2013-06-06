<div class="snippet categories well">
    <h3><?= $this->input('snippet-header') ?></h3>
    <?php if(count($this->list)): ?>
        <ul>
        <?php foreach($this->list as $category): if($category->getEntryCount()): ?>
            <li>
                <?= $category->getUrl($this->document) ?>
                <a
                    href="<?= $category->getUrl($this->document) ?>"
                    class="<?= ($this->category == $category->getKey()) ? 'active' : '' ?>"
                ><?=$category->getName()?> (<?=$category->getEntryCount()?>)</a>
            </li>
        <?php endif; endforeach; ?>
        </ul>
    <?php else: ?>
        <p><?= $this->translate('No categories defined') ?></p>
    <?php endif; ?>
</div>

