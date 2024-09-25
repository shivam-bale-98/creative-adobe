<?php defined("C5_EXECUTE") or die("Access Denied."); ?>
<?php if (isset($title) && trim($title) != "") { ?>
    <?php echo h($title); ?><?php } ?>

<?php
if (trim($link_URL) != "") { ?>
    <?php
    $link_Attributes = [];
    $link_Attributes['href'] = $link_URL;
    $link_AttributesHtml = join(' ', array_map(function ($key) use ($link_Attributes) {
        return $key . '="' . $link_Attributes[$key] . '"';
    }, array_keys($link_Attributes)));
    echo sprintf('<a %s>%s</a>', $link_AttributesHtml, $link_Title); ?><?php
} ?>

<div>
    <?php View::element("blocks/related_listing/item", ["pages" => $pages]); ?>
</div>
