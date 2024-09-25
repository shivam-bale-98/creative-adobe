<?php defined("C5_EXECUTE") or die("Access Denied.");
$ih = new \Application\Concrete\Helpers\ImageHelper();

$thumbnail = "";

if ($Image) {
    $thumbnail = $ih->getThumbnail($Image, 1200, 1300);
    $low = $ih->getThumbnail($Image, 1, 1);
}
?>



<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?> style="background-color: <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> two-col-list with-address relative xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-rustic-red">
    <div class="wrp flex flex-wrap justify-between items-start">
        <?php if ($Image) { ?>
            <div class="image left">
                <div class="img-wrap relative blurred-img rounded-[2rem] overflow-hidden" style="background-image: url('<?php echo $low ?>');">
                    <img loading="lazy" class="absolute inset-0 | lazy" src="<?php echo $thumbnail ?>" alt="<?php echo h($title); ?>">
                </div>
            </div>
        <?php } ?>
        <div class="content right xl:mt-[9rem] md:mt-[4rem] mt-[0rem]">
            <?php if (isset($title) && trim($title) != "") { ?>

                <h2 class="mb-[3rem]">
                    <!-- Channeline head office -->
                    <?php echo h($title); ?>
                </h2>
            <?php } ?>

            <?php if (isset($Description_1) && trim($Description_1) != "") { ?>

                <p class="mb-[3rem]">
                    <?php echo h($Description_1); ?>
                </p>
            <?php } ?>


            <address class="">
                <?php if (isset($phNumber) && trim($phNumber) != "") { ?>
                    <p class="flex items-center mb-[2rem] gap-[1rem]">
                        <span>
                            <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.9403 17.5C14.0557 17.5 12.1625 17.0618 10.2606 16.1855C8.35863 15.3092 6.6096 14.073 5.01345 12.4769C3.42372 10.8807 2.19071 9.1333 1.31442 7.2346C0.438142 5.33588 0 3.44423 0 1.55963C0 1.25963 0.1 1.00802 0.3 0.804825C0.5 0.601608 0.75 0.5 1.05 0.5H4.3115C4.56407 0.5 4.78683 0.582375 4.97977 0.747125C5.17272 0.911875 5.29548 1.1154 5.34803 1.3577L5.9211 4.29998C5.96085 4.57306 5.95252 4.80768 5.8961 5.00383C5.8397 5.19998 5.73842 5.36472 5.59225 5.49805L3.28265 7.74613C3.65445 8.42689 4.07913 9.0708 4.5567 9.67785C5.03427 10.2849 5.55125 10.8647 6.10765 11.4173C6.65638 11.966 7.23973 12.4756 7.85768 12.9462C8.47563 13.4167 9.14293 13.8545 9.8596 14.2596L12.1038 11.9962C12.2602 11.8333 12.4497 11.7192 12.6721 11.6539C12.8945 11.5885 13.1256 11.5724 13.3654 11.6058L16.1423 12.1712C16.3948 12.2378 16.6009 12.3667 16.7605 12.5577C16.9201 12.7487 17 12.9654 17 13.2077V16.45C17 16.75 16.8983 17 16.6951 17.2C16.4919 17.4 16.2403 17.5 15.9403 17.5Z" fill="#80151A" />
                            </svg>
                        </span>

                        <span>
                            Phone : <a href="tel:<?php echo h($phNumber); ?>"><?php echo h($phNumber); ?></a>
                        </span>
                    </p>
                <?php } ?>
                <?php if (isset($eMail) && trim($eMail) != "") { ?>


                    <p class="flex items-center gap-[1rem]">
                        <span>
                            <svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.8077 15.5C1.30257 15.5 0.875 15.325 0.525 14.975C0.175 14.625 0 14.1974 0 13.6923V2.3077C0 1.80257 0.175 1.375 0.525 1.025C0.875 0.675 1.30257 0.5 1.8077 0.5H17.1923C17.6974 0.5 18.125 0.675 18.475 1.025C18.825 1.375 19 1.80257 19 2.3077V13.6923C19 14.1974 18.825 14.625 18.475 14.975C18.125 15.325 17.6974 15.5 17.1923 15.5H1.8077ZM9.49998 8.55763L17.5 3.44225L17.3461 1.99998L9.49998 6.99998L1.65383 1.99998L1.49998 3.44225L9.49998 8.55763Z" fill="#80151A" />
                            </svg>
                        </span>

                        <span>
                            Email : <a href="mailto:<?php echo h($eMail); ?>"> <?php echo h($eMail); ?></a>
                        </span>
                    </p>
                <?php } ?>
            </address>
        </div>
    </div>
</section>