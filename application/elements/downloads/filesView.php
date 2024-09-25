<?php

?>

<?php
foreach ($files as $file) { ?>
    <div class="swiper-slide ">
        <a class="relative download-card rounded-[2rem] overflow-hidden p-[2rem] md:p-[3rem]" href="<?php echo $file['url'] ?>" target="_blank">
            <div class="file-icon absolute h-[3rem] w-[3rem]">
                <img src="<?php echo $file['fileImg'] ?>" alt="<?php echo $file['type'] ?>">
            </div>

            <span class="download-btn absolute md:h-[6rem] md:w-[6rem] h-[4.6rem] w-[4.6rem]">
                <i class="icon-download"></i>
            </span>
            <div class="content">
                <h6 class="mb-[2rem]"> <?php echo $file['year']; ?></h6>
                <h4><?php echo $file['name']; ?></h4>
            </div>
        </a>
    </div>

<?php
}
?>