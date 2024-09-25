<?php defined('C5_EXECUTE') or die("Access Denied.");

$site = Config::get('concrete.site');
$themePath = $this->getThemePath();
?>
<div class="push"></div> <!-- Push div for making fixed footer -->

</div> <!-- closing of wrapper div from header_top.php -->

<footer class="footer  text-rustic-red bg-romance">
    <div class="xl:px-[6rem] md:px-[4rem] px-[2rem] xl:pt-[8rem] pt-[5rem] xl:pb-[4.5rem] md:pb-[4rem] pb-[3rem]">
        <div class="top flex flex-wrap justify-between">
            <a tabindex="0" role="link" aria-label="Get in touch" class="get-in-touch flex items-end" href="<?php echo View::url('/contact/#talk-to-us'); ?>">
                <div class="h2">Get in
                    touch</div>

                <div class="shape relative">
                    <i class="icon-right_button_arrow absolute z-3"></i>
                    <span class="bg absolute inset-0 size-full z-1"></span>
                    <span class="bg-scale absolute  size-full z-2"></span>
                </div>
            </a>

            <div class="links flex justify-between">
                <div class="cols page-items">
                    <h4>
                        <a tabindex="0" role="link" aria-label="Applications" href="<?php echo View::url('/applications'); ?>">
                            Applications
                        </a>
                    </h4>
                    <!-- <ul>
                        <li>
                            <a href="/applications/wastewater">Wastewater</a>
                        </li>
                    </ul> -->
                    <? //php $s = Stack::getByName("Footer Application Links");
                 //   $s->display(); ?>
                </div>

                <div class="cols page-items">
                    <h4>
                        <a tabindex="0" role="link" aria-label="Products" href="<?php echo View::url('/products'); ?>">Products</a>
                    </h4>
                    <!-- <ul>
                        <li>
                            <a href="/products/standard">Standard</a>
                        </li>
                    </ul> -->
                    <? //php $s = Stack::getByName("Footer Products Links"); $s->display(); ?>
                </div>

                <div class="cols">
                    <h4>
                        Quick links
                    </h4>

                    <!-- <ul>
                        <li>
                            <a href="<? //php echo View::url('/about-us');
                                        ?>">About us</a>
                        </li>

                        <li>
                            <a href="<? //php echo View::url('/products');
                                        ?>">Products</a>
                        </li>

                        <li>
                            <a href="<? //php echo View::url('/applications');
                                        ?>">Applications</a>
                        </li>

                        <li>
                            <a href="<? //php echo View::url('/case-studies');
                                        ?>">Case studies</a>
                        </li>

                        <li>
                            <a href="<? //php echo View::url('/technology');
                                        ?>">Technology</a>
                        </li>

                        <li>
                            <a href="<? //php echo View::url('/blog');
                                        ?>">Blog</a>
                        </li>

                        <li>
                            <a href="<? //php echo View::url('/careers');
                                        ?>">Careers</a>
                        </li>

                        <li>
                            <a href="<? //php echo View::url('/contact');
                                        ?>">Contact us</a>
                        </li>
                    </ul> -->

                    <? //php $s = Stack::getByName("Footer Quick Links") $s->display(); ?>
                </div>
            </div>

            <div class="social w-full relative">
                <h5 class="mb-[2rem]">Follow us</h5>

                <ul>
                    <li>
                        <a tabindex="0" role="link" aria-label="linked" class="flex justify-center items-center rounded-[0.5rem]" href="https://in.linkedin.com/company/channeline-international" target="_blank">
                            <svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.77475 2.28389C4.77475 3.52898 3.82672 4.5366 2.34415 4.5366C0.919355 4.5366 -0.0286772 3.52898 0.000662254 2.28389C-0.0286772 0.978283 0.919332 0 2.37255 0C3.82669 0 4.74633 0.978283 4.77475 2.28389ZM0.119858 20.8191V6.31621H4.62712V20.8181H0.119858V20.8191Z" fill="#F2F1EF" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.2398 10.9446C8.2398 9.13566 8.1802 7.5935 8.12061 6.31816H12.0356L12.2437 8.30499H12.3326C12.9259 7.38538 14.4084 5.99268 16.8106 5.99268C19.7757 5.99268 22 7.95016 22 12.219V20.821H17.4927V12.7838C17.4927 10.9144 16.8408 9.63993 15.2098 9.63993C13.9637 9.63993 13.2229 10.4999 12.9268 11.3297C12.8076 11.6268 12.7489 12.0412 12.7489 12.4574V20.821H8.24162V10.9446H8.2398Z" fill="#F2F1EF" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>


            <div class="flex justify-end w-full mt-[2rem] logo-home ">
                <a tabindex="0" role="link" aria-label="home" class="logo " id="scroll-to-banner" href="#banner">
                    <img src="<?php echo $themePath; ?>/assets/images/red-site-logo.svg" alt="channeline">
                </a>
            </div>

            <div class="logo-others flex justify-end w-full mt-[2rem]">
                <a tabindex="0"  role="link" aria-label="home" class="logo " href="<?php echo View::url('/'); ?>">
                    <img src="<?php echo $themePath; ?>/assets/images/red-site-logo.svg" alt="channeline">
                </a>
            </div>

        </div>

        <div class="bottom pt-[4.4rem] flex justify-between relative mt-[2rem]">
            <div class="line absolute"></div>
            <div class="flex justify-between items-center flex-wrap w-full">
                <ul>
                    <li>© Channeline <?php echo date('Y'); ?>. All rights reserved.
                    </li>
                </ul>


                <? //php $s = Stack::getByName("Terms and conditions"); $s->display(); ?>

                <ul class="made-with">
                    <li>
                        <!-- <a role="link" aria-label="tentwenty" href="https://www.tentwenty.me/" target="_blank">
                            Made with <i class="icon-heart"></i> by tentwenty
                        </a> -->
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<!-- Go to top button -->
<div id="gotoTop" class="fixed cursor-pointer bg-red-berry h-[4rem] w-[4rem]">
    <span class="absolute block h-[1.5rem] w-[1.5rem]" aria-hidden="true"></span>
</div>

<!-- For Landscape Alert -->
<div class="landscape-alert">
    <p>For better web experience, please use the website in portrait mode</p>
</div>

<?php $this->inc('elements/footer_bottom.php'); ?>