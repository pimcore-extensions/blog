<?php if ($this->editmode || count($this->entries)): ?>

<?php if ($this->editmode): ?>
<div class="span4 box">
<?php endif; ?>

    <h3><?= $this->input('snippet-header') ?></h3>
    <div class="blog snippet latest">
    <?php foreach ($this->entries as $entry): ?>
    <?php /* @var $entry Blog_Entry */ ?>
        <blockquote>
            <h4>
                <a href="<?=$this->url(array(
                    'key' => $entry->getUrlPath()
                ), 'blog-show', false, false)?>"
                ><?=$entry->getTitle()?></a>
            </h4>
            <small><?=$entry->getDate()->toString('FFFFF');?></small>
            <p>
                <?=(trim($entry->getSummary()))
                    ? $entry->getSummary()
                    : Website_Tool_Text::cutStringRespectingWhitespace(trim(strip_tags($entry->getContent())), 200)?>
            </p>
        </blockquote>
    <?php endforeach; ?>
    </div>

<?php if ($this->editmode): ?>
</div>
<?php endif; ?>

<?php endif; ?>
