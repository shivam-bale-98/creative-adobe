<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


<div class="w-[100vw] bg-white flex flex-col sm:flex-row items-center md:justify-center">
    <div class="md:w-1/2 items-center md:items-baseline text-center md:text-left md:mx-0 lg:mx-[3.5rem] flex flex-col justify-center lg:ml-[6rem] md:ml-0 md:mt-[0rem] mt-[16.6rem] ">
        <h2 class="text-black text-[4rem] mx-16 mb-5 lg:mx-0 lg:text-[8rem] md:text-[7rem] ">404</h2>
        <h4 class="text-black mb-5 w-[35.6rem] lg:mx-0">We can’t find the page you are looking for</h4>
        <div class="fade-text md:mb-32">
            <a class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative mt-5 no-underline" href="<?php echo View::url('/'); ?>">
                <span class="text z-2 relative" t>Go to the homepage</span>
                <div class="shape absolute">
                    <i class="icon-right_button_arrow absolute z-2"></i>
                    <span class="bg absolute inset-0 size-full"></span>
                </div>
            </a>
        </div>
    </div>
    <div class="md:bg-romance md:w-1/2 h-auto flex  items-center md:mx-0 mx-[3.5rem] lg:pl-[10rem] md:pl-[5rem]">
        <?php $a = new Area('Project Block');
        $a->display($c); ?>

        <!-- <div class="flex flex-col items-start justify-center mt-[6rem] md:mt-[18.8rem] mb-[4.9rem] md:mb-0">
            <h3 class="text-black w-96 md:w-auto">Explore our products</h3>
            <ul class="relative flex flex-col mt-20 left-0">
                <li class="mb-[3rem]"><a href="" class="text-rustic-red transition-opacity md:opacity-30 md:hover:opacity-100 md:hover:text-black hover:no-underline text-[2rem] uppercase md:capitalize md:text-[2.4rem]"><span class="relative md:top-[-2.9rem] top:-[0.6rem] text-[1.6rem] md:text-[1.4rem] mr-[2.8rem]">01</span>Standard</a></li>
                <li class="mb-[3rem]"><a href="" class="text-rustic-red transition-opacity md:opacity-30 md:hover:opacity-100 md:hover:text-black hover:no-underline text-[2rem] uppercase md:capitalize md:text-[2.4rem]"><span class="relative md:top-[-2.9rem] top:-[0.6rem] text-[1.6rem] md:text-[1.4rem] mr-[2.8rem]">02</span>Slip liner</a></li>
                <li class="mb-[3rem]"><a href="" class="text-rustic-red transition-opacity md:opacity-30 md:hover:opacity-100 md:hover:text-black hover:no-underline text-[2rem] uppercase md:capitalize md:text-[2.4rem]"><span class="relative md:top-[-2.9rem] top:-[0.6rem] text-[1.6rem] md:text-[1.4rem] mr-[2.8rem]">03</span>Multi segmental</a></li>
                <li class="mb-[3rem]"><a href="" class="text-rustic-red transition-opacity md:opacity-30 md:hover:opacity-100 md:hover:text-black hover:no-underline text-[2rem] uppercase md:capitalize md:text-[2.4rem]"><span class="relative md:top-[-2.9rem] top:-[0.6rem] text-[1.6rem] md:text-[1.4rem] mr-[2.8rem]">04</span>Curved liner</a></li>
                <li class="mb-[3rem]"><a href="" class="text-rustic-red transition-opacity md:opacity-30 md:hover:opacity-100 md:hover:text-black hover:no-underline text-[2rem] uppercase md:capitalize md:text-[2.4rem]"><span class="relative md:top-[-2.9rem] top:-[0.6rem] text-[1.6rem] md:text-[1.4rem] mr-[2.8rem]">05</span>Crown & inverts</a></li>
                <li class="mb-[3rem]"><a href="" class="text-rustic-red transition-opacity md:opacity-30 md:hover:opacity-100 md:hover:text-black hover:no-underline text-[2rem] uppercase md:capitalize md:text-[2.4rem]"><span class="relative md:top-[-2.9rem] top:-[0.6rem] text-[1.6rem] md:text-[1.4rem] mr-[2.8rem]">06</span>Manholes</a></li>
                <li class="mb-[3rem]"><a href="" class="text-rustic-red transition-opacity md:opacity-30 md:hover:opacity-100 md:hover:text-black hover:no-underline text-[2rem] uppercase md:capitalize md:text-[2.4rem]"><span class="relative md:top-[-2.9rem] top:-[0.6rem] text-[1.6rem] md:text-[1.4rem] mr-[2.8rem]">07</span>Transitions</a></li>          
                <li class="mb-[3rem]"><a href="" class="text-rustic-red transition-opacity md:opacity-30 md:hover:opacity-100 md:hover:text-black hover:no-underline text-[2rem] uppercase md:capitalize md:text-[2.4rem]"><span class="relative md:top-[-2.9rem] top:-[0.6rem] text-[1.6rem] md:text-[1.4rem] mr-[2.8rem]">08</span>Lateral connections & fittings</a></li>
                <li class="mb-[3rem]"><a href="" class="text-rustic-red transition-opacity md:opacity-30 md:hover:opacity-100 md:hover:text-black hover:no-underline text-[2rem] uppercase md:capitalize md:text-[2.4rem]"><span class="relative md:top-[-2.9rem] top:-[0.6rem] text-[1.6rem] md:text-[1.4rem] mr-[2.8rem]">09</span>Custom (TBC)</a></li>
            </ul>
        </div> -->
    </div>
</div>