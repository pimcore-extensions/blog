<div class="container_12">
    <div class="grid_3">&nbsp;</div>
    <div class="grid_9">

        <div class="content-box">
            <div class="content-box-shadow">

                <div class="corner"></div>

                <div class="blog-header"><h1><?= $this->input('page-header') ?></h1></div>

                <div class="separator"></div>

                <div class="blog-columns">

                    <div class="blog-column-left">

                        <div class="blog-content">

                            <!-- start_search -->

                            <!--<?= $this->paginator ?>-->


                            <?php foreach ($this->paginator as $entry): $entry instanceof Blog_Entry; ?>

                                <div class="blog-post-title"><h3><a href="<?= $entry->getFullPath() ?>"><?= $entry->getTitle(); ?></a></h3></div>
                                <div class="blog-post-date"><small><?= $entry->getDate()->toString('FFFFF'); ?></small></div>

                                <div class="resizeable-text"><div class="blog-post-entry">
                                    <p>
                                        <?=
                                        (trim($entry->getSummary())) ? $entry->getSummary() : Website_Tool_Text::cutStringRespectingWhitespace(trim(strip_tags($entry->getContent())), 200)
                                        ?>
                                    </p>
                                </div></div>

                                <?php if (count($entry->getTags())): ?>
                                    <div class="blog-post-tags"><small>
                                            tagi:
                                            <?php foreach ($entry->getTags() as $tag): ?>
                                                <a href="<?=
                                    $this->url(array(
                                        'tag' => $tag['url'],
                                        'page' => null, 'perpage' => null
                                            ), 'blog-tag')
                                                ?>"><?= $tag['tag'] ?></a>
                                    <?php endforeach; ?>
                                        </small></div>
    <?php endif; ?>

                                <div class="separator"></div>

                            <?php endforeach; ?>

                            <!-- end_search -->

<?= $this->paginator ?>


                        </div>
                    </div>

                    <div class="blog-column-right">

                        <?php if (!$this->editmode) : ?>
                            <?php if (!$this->wysiwyg("blog-sidebar-content")->isEmpty()) : ?>
                            <div style="padding: 12px 16px; padding-bottom: 0">
                            <div class="resizeable-text text">
                                <div>
                                    <?= $this->wysiwyg("blog-sidebar-content"); ?>
                                </div>
                            </div>
                            </div>
                            <div class="separator"></div>
                            <?php endif; ?>
                        <?php else : ?>

                            <?= $this->wysiwyg("blog-sidebar-content", array(
                                        "contentsCss" => '/website/static/css/wysiwyg.css',
                                        "width" => 192
                                        ));
                            ?>


                        <?php endif; ?>




                        <?php for ($i = 1; $i <= 3; $i++): ?>
    <?= $this->snippet("blog-snippet-$i") ?>
<?php endfor; ?>

                    </div>

                    <div class="clear"></div>

                </div>

            </div>
        </div>



    </div>
</div>
