<?php if ($this->editmode || count($this->calendar)): ?>

<div class="snippet calendar well">
    <h3><?= $this->input('snippet-header') ?></h3>

    <ul>
        <?php
        $currentYear = key($this->calendar);
        foreach ($this->calendar as $year => $months):
            ?>
            <li>
                <a href="<?= $this->url(array('year' => $year, 'month' => null), 'blog-calendar') ?>"
                   class="<?= ($this->year == $year) ? 'active' : '' ?>"
                ><?= $year ?></a>
                <ul>
                <?php foreach ($months as $month => $data): ?>
                    <li>
                        <a href="<?= $this->url(array('year' => $year, 'month' => $month), 'blog-calendar') ?>"
                           class="<?= ($this->month == $month) ? 'active' : '' ?>"
                        ><?= $data['month'] ?> (<?= $data['count'] ?>)</a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php endif; ?>
