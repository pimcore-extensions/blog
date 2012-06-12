<?php /* @see http://developer.yahoo.com/ypatterns/pattern.php?pattern=searchpagination */ ?>
<div class="pagination"><ul>

    <?php $class = (isset($this->previous)) ? '' : 'disabled' ?>
    <?php $href = (isset($this->previous))
        ? $this->url(array('page' => $this->previous), null, false, false)
        : '' ?>
    <li class="<?=$class?>">
        <a href="<?=$href?>">
            &laquo; <?=$this->translate('Previous')?>
        </a>
    </li>

    <?php foreach ($this->pagesInRange as $page): ?>
        <?php $class = ($page == $this->current) ? 'active' : '' ?>
        <?php $href = ($page == $this->current)
            ? ''
            : $this->url(array('page' => $page), null, false, false) ?>
        <li class="<?=$class?>">
            <a href="<?=$href?>">
                <?=$page?>
            </a>
        </li>
    <?php endforeach; ?>

    <?php $class = (isset($this->next)) ? '' : 'disabled' ?>
    <?php $href = (isset($this->next))
        ? $this->url(array('page' => $this->next), null, false, false)
        : '' ?>
    <li class="<?=$class?>">
        <a href="<?=$href?>">
            <?=$this->translate('Next')?> &raquo;
        </a>
    </li>
</ul></div>
