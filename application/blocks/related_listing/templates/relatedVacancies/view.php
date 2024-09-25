<div style="padding-top:90px">
    <?php if (isset($title) && trim($title) != "") { ?>
        <?php echo h($title); ?><?php } ?>
        <?php View::element("blocks/related_listing/vacancy/relatedVacancies", ["pages" => $pages]); ?>
</div>