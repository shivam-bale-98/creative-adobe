<?php
    if (\Application\Concrete\Helpers\GeneralHelper::pagesExist($pages)) {
        
        /** @var \Application\Concrete\Models\Location\Location $page */
        foreach ($pages as $key => $page) {
            $title      = $page->getTitle();
            $address    = $page->getAddress();
            $phone      = $page->getPhone();
            $timings    = $page->getTimings();
            $website    = $page->getWebsite();
            $lat        = $page->getLatitude();
            $lon        = $page->getLongitude();
    ?>
            <div style="padding-top:60px" data-lat="<?php echo $lat; ?>" data-long="<?php echo $lon; ?>">
                <h5 class="title"><?php echo t($title) ?></h5>
                <?php if ($address) echo '<h6 class="address">' . $address . '</h6>' ?>
                <?php if ($phone) echo '<a class="h6 number" href="tel:' . $phone . '">' . $phone . '</a>' ?>
                <?php if ($timings) echo '<h6 class="time">' . $timings . '</h6>' ?>
                <div class="ctas d-flex">
                    <a class="get-drections seddiqi-btn seddiqi-btn--blue"><?php echo t('Get Directions') ?></a>
                    <a class="visit-website seddiqi-btn seddiqi-btn--bordered_black" href="<?php echo $website; ?>"><?php echo t('Visit Website') ?></a>
                </div>
            </div>
    <?php }
} ?>