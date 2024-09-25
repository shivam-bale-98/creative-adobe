<?php defined("C5_EXECUTE") or die("Access Denied."); 
use Application\Concrete\Helpers\GeneralHelper;

?>
<?php if (isset($title) && trim($title) != "") { ?>
    <?php echo h($title); ?><?php } ?>
<?php if (isset($subTitle) && trim($subTitle) != "") { ?>
    <?php echo h($subTitle); ?><?php } ?>
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

<?php
        if (GeneralHelper::pagesExist($pages)) {
        foreach ($pages as $page) {
            $title = $page->getTitle();
            $url = $page->getUrl();
            $description = $page->getCollectionDescription();
            $thumb = $page->getThumbnailImage(850, 1190);
            
        ?>
          <?php echo $title; ?><?php echo $description; ?>

                        <a href="<?php echo $url; ?>"><?php echo t('Learn More'); ?></a>
                    
                    
                        <img class="position-absolute inset-0" src="<?php echo $thumb; ?>" alt="">
                   
        <?php }
         }
        ?>

