<?php $this->layout()->setLayout('layout') ?>
<div class="container blog blog-post">
    <div class="row">
        <div class="span9">

            <?= $this->flashMessenger() ?>

            <h1><?= $this->input('page-header') ?></h1>

            <h2><?=$this->entry->getTitle()?></h2>
            <small><?=$this->entry->getDate()->toString('FFFFF');?></small>

            <?php if(count($this->entry->getCategories())): ?>
            <div class="categories">
                <small>kategorie:
                <?php foreach($this->entry->getCategories() as $category): ?>
                    <a href="<?=$this->url(array(
                        'cat' => $category->getKey()
                        ), 'blog-category')?>"><?=$category->getName()?></a>
                    <?php endforeach; ?>
                </small>
            </div>
            <?php endif; ?>

            <div class="entry">
                <?=$this->entry->getContent();?>
            </div>

            <?php if(count($this->entry->getTags())): ?>
            <div class="tags">
                <small>tagi:
                <?php foreach($this->entry->getTags() as $tag): ?>
                    <a href="<?=$this->url(array(
                        'tag' => $tag['url']
                        ), 'blog-tag')?>"><?=$tag['tag']?></a>
                <?php endforeach; ?>
                </small>
            </div>
            <?php endif; ?>

            <?php if (count($this->comments)): ?>
            <div class="comments">
                <h4><?= $this->translate('Comments') ?>:</h4>

                <?php foreach ($this->comments as $comment): ?>
                <blockquote>
                    <h4>
                        <?=$comment->getMetadata()->name?>
                        <small><?=$comment->getDate()->toString('FFFFF')?></small>
                    </h4>
                    <?=nl2br($comment->getData())?>
                </blockquote>
                <?php endforeach; ?>

                <?=$this->comments; // paginator ?>
            </div>
            <?php endif; ?>

            <?php if ($this->commentForm): ?>
            <div class="comments-form">
                <h4><?= $this->translate('Add comment') ?>:</h4>
                <?=$this->commentForm?>
            </div>
            <?php endif; ?>

        </div>
        <div class="span3">
            <?php for($i = 1; $i <= 3; $i++): ?>
                <?=$this->snippet("blog-snippet-$i")?>
            <?php endfor; ?>
        </div>
    </div>
</div>
