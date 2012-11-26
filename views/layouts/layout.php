<?= $this->doctype('HTML5') . "\n" ?>
<html>
<head>
    <?php
    $this->headMeta()->setSeparator(PHP_EOL . '    ')
        ->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8')
        ->appendHttpEquiv('X-UA-Compatible', 'IE=Edge,chrome=1')
        ->appendName('viewport', 'width=device-width, initial-scale=1.0');

	$this->headTitle()->setSeparator(' - ');
    $this->headTitle($this->document->getTitle());

    if('' != ($description = $this->document->getDescription(false))) {
        $this->headMeta()->appendName('description', $description);
    }

    if('' != ($keywords = $this->document->getKeywords(false))) {
        $this->headMeta()->appendName('keywords', $keywords);
    }
    ?>

    <?= $this->headMeta() . "\n" ?>
    <?= $this->headTitle() . "\n" ?>

    <?php $this->headLink()
        ->appendStylesheet('http://twitter.github.com/bootstrap/assets/css/bootstrap.css')
        ->appendStylesheet('http://twitter.github.com/bootstrap/assets/css/bootstrap-responsive.css'); ?>
    <?= $this->headLink() ?>

</head>

<body>

    <div class="container">
        <div class="hero-unit">
            <h1>Welcome to Blog plugin</h1>
        </div>

        <div class="row">
            <div class="span12">
                <?= $this->layout()->content; ?>
            </div>
        </div>
    </div>

</body>
</html>
